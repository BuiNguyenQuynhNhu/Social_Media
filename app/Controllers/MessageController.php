<?php
namespace App\Controllers;

use App\Models\Message;
use App\Models\FriendShip;

class MessageController extends BaseController {
    protected $msg;
    protected $friend;
    protected $userId;

    public function __construct() {
        $this->msg = new Message();
        $this->friend = new FriendShip();
        $this->userId = session()->get('user_id');
    }

    public function index() {
        if (!$this->userId) return redirect()->to('/login');

        $db = \Config\Database::connect();
        $user = $db->table('users')->where('id', $this->userId)->get()->getRowArray();
        $avatarUrl = $user['avatar'] ?? base_url('ICON/avatar-default.svg');

        return view('message/index', [
            'user_id' => $this->userId,
            'avatarUrl' => $avatarUrl
        ]);
    }

    public function getConversations() {
        if (!$this->userId) return $this->response->setJSON(['status' => 'error']);

        $db = \Config\Database::connect();
        
        // Get all accepted friendships
        $friends = $this->friend->where('status', 'accepted')
            ->groupStart()
                ->where('requester_id', $this->userId)
                ->orWhere('receiver_id', $this->userId)
            ->groupEnd()
            ->findAll();

        $conversations = [];
        foreach ($friends as $friendship) {
            $friendId = ($friendship['requester_id'] == $this->userId) 
                ? $friendship['receiver_id'] 
                : $friendship['requester_id'];

            // Get friend info
            $friendUser = $db->table('users')->where('id', $friendId)->get()->getRowArray();
            $friendProfile = $db->table('profiles')->where('id', $friendId)->get()->getRowArray();

            // Get nickname
            $nickname = null;
            if ($friendship['requester_id'] == $this->userId && !empty($friendship['nickname1'])) {
                $nickname = $friendship['nickname1'];
            } elseif ($friendship['receiver_id'] == $this->userId && !empty($friendship['nickname2'])) {
                $nickname = $friendship['nickname2'];
            }

            $name = $nickname ?? ($friendProfile['firstname'] . ' ' . $friendProfile['lastname']);

            // Get last message
            $lastMessages = $this->msg->get($this->userId, $friendId);
            $lastMessage = !empty($lastMessages) ? end($lastMessages)['content'] : null;

            $conversations[] = [
                'friend_id' => $friendId,
                'name' => $name,
                'avatar' => $friendUser['avatar'] ?? base_url('images/default-avatar.png'),
                'last_message' => $lastMessage,
                'unread_count' => 0 // TODO: Implement unread count logic
            ];
        }

        return $this->response->setJSON(['status' => 'success', 'data' => $conversations]);
    }
    public function get($receiverId) {
        if (!$this->userId) return $this->response->setJSON(['status' => 'error']);

        $chatType = $this->request->getGet('type') ?? 'private'; 
        $groupId = ($chatType === 'group') ? $receiverId : null;

        $messages = $this->msg->get($this->userId, $receiverId);

        $result = [];
        foreach ($messages as $msg) {
            $senderId = $msg['sender_id'];
            
            $result[] = [
                'id' => (string)$msg['_id'],
                'content' => $msg['content'],
                'sender_name' => $this->DisplayName($senderId, $chatType, $groupId),
                'is_mine' => ($senderId == $this->userId),
                'media' => $msg['media'] ?? null,
                'created_at' => $msg['created_at']
            ];
        }
        return $this->response->setJSON(['status' => 'success', 'data' => $result]);
    }
    private function DisplayName($targetUserId, $chatType, $groupId = null)
    {
        $db = \Config\Database::connect();

        if ($chatType === 'group' && $groupId) {
            $groupMember = $db->table('group_members')
                            ->select('nickname')
                            ->where(['group_id' => $groupId, 'user_id' => $targetUserId])
                            ->get()->getRowArray();
            
            if (!empty($groupMember['nickname'])) return $groupMember['nickname'];
            
            $profile = $db->table('profiles')->where('id', $targetUserId)->get()->getRowArray();
            return $profile;
        }

        $friendship = $this->friend->where('status', 'accepted')
            ->groupStart()
                ->where(['requester_id' => $this->userId, 'receiver_id' => $targetUserId])
                ->orWhere(['requester_id' => $targetUserId, 'receiver_id' => $this->userId])
            ->groupEnd()->first();

        if ($friendship) {
            if ($friendship['requester_id'] == $this->userId && !empty($friendship['nickname1'])) {
                return $friendship['nickname1'];
            } elseif ($friendship['receiver_id'] == $this->userId && !empty($friendship['nickname2'])) {
                return $friendship['nickname2'];
            }
        }

        $profile = $db->table('profiles')->where('id', $targetUserId)->get()->getRowArray();
        return $profile;
    }
    public function sendMessage($receiverId) {
        if (!$this->userId) return $this->response->setJSON(['status' => 'error']);
        $chatType = $this->request->getPost('chat_type') ?? 'private';
        $isFriend = $this->friend->where('status', 'accepted')
            ->groupStart()
                ->where('requester_id', $this->userId)->where('receiver_id', $receiverId)
                ->orWhere('requester_id', $receiverId)->where('receiver_id', $this->userId)
            ->groupEnd()->first();

        if (!$isFriend) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'You can only message friends.']);
        }

        $content = $this->request->getPost('content');
        $replyTo = $this->request->getPost('reply_to');

        $data = [
            'sender_id'   => $this->userId,
            'receiver_id' => $receiverId,
            'chat_type'   => $chatType,
            'content'     => $content,
            'reply_to'    => $replyTo ? $replyTo : null,
            'reactions'   => [],
            'created_at'  => date('Y-m-d H:i:s'),
            'read_at'     => null
        ];

        // Handle media upload
        $file = $this->request->getFile('media');
        $mediaData = null;
        
        if ($file && $file->isValid()) {
            $cloudinary = new \App\Libraries\Cloudinary();
            $folder = 'messages';
            try {
                $res = $cloudinary->uploadPost($file->getTempName(), $folder);
                $mediaData = [
                    'url'       => $res['secure_url'],
                    'type'      => $res['resource_type'], 
                    'public_id' => $res['public_id']
                ];
            } catch (\Exception $e) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'Media upload failed']);
            }
        }

        $data = [
            'sender_id'   => $this->userId,
            'receiver_id' => $receiverId,
            'chat_type'   => $chatType,
            'content'     => $content,
            'media'       => $mediaData,
            'reply_to'    => $replyTo ? $replyTo : null,
            'reactions'   => [],
            'created_at'  => date('Y-m-d H:i:s'),
            'read_at'     => null
        ];

        if ($this->msg->save($data)) {
            // Include sender info for immediate UI update if needed
            $data['is_mine'] = true; 
            return $this->response->setJSON(['status' => 'success', 'data' => $data]);
        }
    }

    public function react($messageId) {
        $type = $this->request->getPost('type');
        if ($this->msg->toggle($messageId, $this->userId, $type)) {
            return $this->response->setJSON(['status' => 'success']);
        }
        return $this->response->setJSON(['status' => 'error']);
    }
}