<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Libraries\MongoDB;

class MessageController extends BaseController
{
    protected $mongo;
    protected $collection;
    protected $userId;

    public function __construct()
    {
        $this->mongo = new MongoDB();
        $this->collection = $this->mongo->getCollection('messages');
        $this->userId = session()->get('user_id');
    }

    public function index()
    {
        if (!$this->userId) return redirect()->to('login');

        $userModel = new \App\Models\User();
        $user = $userModel->find($this->userId);
        
        $avatarUrl = (strpos($user['avatar'], 'http') === 0) 
            ? $user['avatar'] 
            : ($user['avatar'] ? base_url($user['avatar']) : base_url('ICON/avatar-default.svg'));

        $data = [
            'user' => $user,
            'avatarUrl' => $avatarUrl,
            'user_id' => $this->userId
        ];

        return view('message/index', $data);
    }

    public function getConversations()
    {
        if (!$this->userId) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Unauthorized']);
        }

        // Get all unique conversations
        $pipeline = [
            ['$match' => [
                '$or' => [
                    ['sender_id' => $this->userId],
                    ['receiver_id' => $this->userId]
                ]
            ]],
            ['$sort' => ['created_at' => -1]],
            ['$group' => [
                '_id' => [
                    '$cond' => [
                        ['$eq' => ['$sender_id', $this->userId]],
                        '$receiver_id',
                        '$sender_id'
                    ]
                ],
                'last_message' => ['$first' => '$content'],
                'last_time' => ['$first' => '$created_at'],
                'unread_count' => [
                    '$sum' => [
                        '$cond' => [
                            ['$and' => [
                                ['$eq' => ['$receiver_id', $this->userId]],
                                ['$eq' => ['$read_at', null]]
                            ]],
                            1,
                            0
                        ]
                    ]
                ]
            ]]
        ];

        $conversations = $this->collection->aggregate($pipeline)->toArray();

        // Get user info and nicknames
        $userModel = new \App\Models\User();
        $profileModel = new \App\Models\Profile();
        $friendModel = new \App\Models\FriendShip();

        $result = [];
        foreach ($conversations as $conv) {
            $friendId = $conv['_id'];
            $user = $userModel->find($friendId);
            $profile = $profileModel->find($friendId);

            if (!$user || !$profile) continue;

            // Get nickname from friendship
            $friendship = $friendModel->where("(requester_id = '$this->userId' AND receiver_id = '$friendId') OR (requester_id = '$friendId' AND receiver_id = '$this->userId')", null, false)
                ->where('status', 'accepted')
                ->first();

            $displayName = $profile['firstname'] . ' ' . $profile['lastname'];
            
            if ($friendship) {
                if ($friendship['requester_id'] == $this->userId && !empty($friendship['nickname1'])) {
                    $displayName = $friendship['nickname1'];
                } elseif ($friendship['receiver_id'] == $this->userId && !empty($friendship['nickname2'])) {
                    $displayName = $friendship['nickname2'];
                }
            }

            $avatar = $user['avatar'] ?? 'ICON/avatar-default.svg';
            if (strpos($avatar, 'http') !== 0) $avatar = base_url($avatar);

            $result[] = [
                'friend_id' => $friendId,
                'name' => $displayName,
                'avatar' => $avatar,
                'last_message' => $conv['last_message'] ?? '',
                'last_time' => $conv['last_time'],
                'unread_count' => $conv['unread_count']
            ];
        }

        return $this->response->setJSON(['status' => 'success', 'data' => $result]);
    }

    public function getMessages($friendId)
    {
        if (!$this->userId) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Unauthorized']);
        }

        // Mark messages as read
        $this->collection->updateMany(
            [
                'sender_id' => $friendId,
                'receiver_id' => $this->userId,
                'read_at' => null
            ],
            ['$set' => ['read_at' => date('Y-m-d H:i:s')]]
        );

        // Get messages
        $messages = $this->collection->find(
            [
                '$or' => [
                    ['sender_id' => $this->userId, 'receiver_id' => $friendId],
                    ['sender_id' => $friendId, 'receiver_id' => $this->userId]
                ]
            ],
            ['sort' => ['created_at' => 1]]
        )->toArray();

        $result = [];
        foreach ($messages as $msg) {
            $result[] = [
                'id' => (string)$msg['_id'],
                'sender_id' => $msg['sender_id'],
                'content' => $msg['content'] ?? '',
                'media' => $msg['media'] ?? null,
                'created_at' => $msg['created_at'],
                'is_mine' => ($msg['sender_id'] == $this->userId)
            ];
        }

        return $this->response->setJSON(['status' => 'success', 'data' => $result]);
    }

    public function sendMessage($receiverId)
    {
        if (!$this->userId) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Unauthorized']);
        }

        $content = $this->request->getPost('content');
        $media = null;

        // Handle media upload
        $file = $this->request->getFile('media');
        if ($file && $file->isValid()) {
            $cloudinary = new \App\Libraries\Cloudinary();
            $upload = $cloudinary->upload($file->getTempName(), [
                'folder' => 'messages',
                'resource_type' => 'auto'
            ]);

            if ($upload) {
                $media = [
                    'type' => strpos($file->getMimeType(), 'video') !== false ? 'video' : 'image',
                    'url' => $upload['secure_url'],
                    'public_id' => $upload['public_id']
                ];
            }
        }

        if (empty($content) && empty($media)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Message cannot be empty']);
        }

        $message = [
            'sender_id' => $this->userId,
            'receiver_id' => $receiverId,
            'content' => $content ?? '',
            'media' => $media,
            'created_at' => date('Y-m-d H:i:s'),
            'read_at' => null
        ];

        $result = $this->collection->insertOne($message);

        if ($result->getInsertedCount() > 0) {
            return $this->response->setJSON([
                'status' => 'success',
                'data' => [
                    'id' => (string)$result->getInsertedId(),
                    'sender_id' => $this->userId,
                    'content' => $content,
                    'media' => $media,
                    'created_at' => $message['created_at'],
                    'is_mine' => true
                ]
            ]);
        }

        return $this->response->setJSON(['status' => 'error', 'message' => 'Failed to send message']);
    }

    public function deleteMessage($messageId)
    {
        if (!$this->userId) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Unauthorized']);
        }

        $oid = new \MongoDB\BSON\ObjectId($messageId);
        $message = $this->collection->findOne(['_id' => $oid]);

        if (!$message || $message['sender_id'] !== $this->userId) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Unauthorized']);
        }

        $result = $this->collection->deleteOne(['_id' => $oid]);

        if ($result->getDeletedCount() > 0) {
            return $this->response->setJSON(['status' => 'success']);
        }

        return $this->response->setJSON(['status' => 'error', 'message' => 'Failed to delete']);
    }
}
