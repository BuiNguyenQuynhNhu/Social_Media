<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\FriendShip;
use App\Models\User;
use App\Models\Profile;
use App\Models\Notification;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\Post;

class FriendShipController extends BaseController
{
    protected $friend;
    protected $userId;

    public function __construct()
    {
        $this->friend = new FriendShip();
        $this->userId = session()->get('user_id');
    }
    private function getUserData() {
        $userModel = new User();
        $user = $userModel->find($this->userId);
        $avatarUrl = (strpos($user['avatar'], 'http') === 0) 
            ? $user['avatar'] 
            : ($user['avatar'] ? base_url($user['avatar']) : base_url('ICON/avatar-default.svg'));
        return ['user' => $user, 'avatarUrl' => $avatarUrl, 'user_id' => $this->userId];
    }

    private function getTargetUserData($targetId) {
        $userModel = new User();
        $profileModel = new Profile();
        
        $targetUser = $userModel->find($targetId);
        $targetProfile = $profileModel->where('id', $targetId)->first();
        
        if(!$targetUser || !$targetProfile) return null;
        
        $merged = array_merge($targetUser, $targetProfile);
        
        $merged['avatarUrl'] = (strpos($merged['avatar'], 'http') === 0) 
            ? $merged['avatar'] 
            : ($merged['avatar'] ? base_url($merged['avatar']) : base_url('ICON/avatar-default.svg'));
            
        $merged['coverUrl'] = (strpos($merged['background'], 'http') === 0) 
            ? $merged['background'] 
            : (!empty($merged['background']) ? base_url($merged['background']) : base_url('ICON/background-default.jpg'));
            
        return $merged;
    }

    public function index()
    {
        if (!$this->userId) return redirect()->to('login');
        
        $data = $this->getUserData();
        $data['title'] = 'All Friends';
        $data['activeTab'] = 'all';
        $data['list'] = $this->friend->listFriend($this->userId);
        
        return view('user/listFriend', $data);
    }

    public function listFriend($id) {
        if (!$this->userId) return redirect()->to('login');
        
        $data = $this->getUserData(); 
        $data['targetId'] = $id;
        
        $targetData = $this->getTargetUserData($id);
        if($targetData) {
            $data['navbarAvatarUrl'] = $data['avatarUrl']; 
            $data['avatarUrl'] = $targetData['avatarUrl'];
            $data['coverUrl'] = $targetData['coverUrl'];
            $data['user'] = $targetData; 
        }

        $postModel = new \App\Models\Post();
        $data['posts'] = $postModel->where('id_user', $id)->findAll();
        $data['friends'] = $this->friend->listFriend($id);

        $data['title'] = 'Friends List';
        $data['activeTab'] = 'all';
        $data['list'] = $data['friends'];
        
        return view('user/listFriend', $data);
    }

    public function listReceive($id) {
        if (!$this->userId) return redirect()->to('login');
        if($id != $this->userId) {
            return redirect()->to('user/dashboard')->with('error', 'Unauthorized access to requests');
        }

        $data = $this->getUserData();
        $data['targetId'] = $id;
        
        $targetData = $this->getTargetUserData($id);
        if($targetData) {
            $data['navbarAvatarUrl'] = $data['avatarUrl']; 
            $data['avatarUrl'] = $targetData['avatarUrl'];
            $data['coverUrl'] = $targetData['coverUrl'];
            $data['user'] = $targetData; 
        }

        $postModel = new Post();
        $data['posts'] = $postModel->where('id_user', $id)->findAll();
        $data['friends'] = $this->friend->listFriend($id);

        $data['title'] = 'Friend Requests';
        $data['activeTab'] = 'received';
        $data['list'] = $this->friend->listReceive($id);

        return view('user/listFriend', $data);
    }
    
    public function listRequest($id) {
        if (!$this->userId) return redirect()->to('login');
         if($id != $this->userId) {
            return redirect()->to('user/dashboard')->with('error', 'Unauthorized access to outgoing requests');
        }

        $data = $this->getUserData();
        $data['targetId'] = $id;
        
        $targetData = $this->getTargetUserData($id);
        if($targetData) {
            $data['navbarAvatarUrl'] = $data['avatarUrl']; 
            $data['avatarUrl'] = $targetData['avatarUrl'];
            $data['coverUrl'] = $targetData['coverUrl'];
            $data['user'] = $targetData; 
        }

        $postModel = new Post();
        $data['posts'] = $postModel->where('id_user', $id)->findAll();
        $data['friends'] = $this->friend->listFriend($id);

        $data['title'] = 'Sent Requests';
        $data['activeTab'] = 'sent';
        $data['list'] = $this->friend->listSend($id);

        return view('user/listFriend', $data);
    }

    public function send($receiverId)
    {
        if (!$this->userId) {
             if($this->request->isAJAX()) return $this->response->setJSON(['status'=>'error', 'message'=>'Unauthorized']);
             return redirect()->to('login');
        }
        if ($this->userId == $receiverId) {
             if($this->request->isAJAX()) return $this->response->setJSON(['status'=>'error', 'message'=>'You cannot add yourself']);
             return redirect()->back()->with('error', 'You cannot add yourself.');
        }
        
        $res = $this->friend->create($this->userId, $receiverId);
        
        if ($res) {
            // Send notification to receiver
            Notification::createNotification(
                'friend_request',
                $this->userId,
                $receiverId
            );
            if($this->request->isAJAX()) return $this->response->setJSON(['status'=>'success', 'message'=>'Friend request sent.']);
            return redirect()->back()->with('success', 'Friend request sent.');
        }
        
        // Check if they sent you a request
        $pendingFromThem = $this->friend->where('requester_id', $receiverId)
                                        ->where('receiver_id', $this->userId)
                                        ->where('status', 'pending')
                                        ->first();
        
        if ($pendingFromThem) {
            if($this->request->isAJAX()) return $this->response->setJSON(['status'=>'info', 'message'=>'This user already sent you a friend request.']);
            return redirect()->back()->with('info', 'This user already sent you a friend request. Check your Friend Requests page to accept it.');
        }
        
        if($this->request->isAJAX()) return $this->response->setJSON(['status'=>'error', 'message'=>'Request already exists or you are already friends.']);
        return redirect()->back()->with('error', 'Request already exists or you are already friends.');
    }

    public function accept($shipId)
    {
        if (!$this->userId) return redirect()->to('login');

        // Verify the current user is the receiver of this request
        $friendship = $this->friend->find($shipId);
        
        if (!$friendship) {
            return redirect()->back()->with('error', 'Friend request not found.');
        }
        
        // Only the receiver can accept the request
        if ($friendship['receiver_id'] !== $this->userId) {
            return redirect()->back()->with('error', 'You can only accept requests sent to you.');
        }
        
        // Only pending requests can be accepted
        if ($friendship['status'] !== 'pending') {
            return redirect()->back()->with('error', 'This request has already been processed.');
        }

        if ($this->friend->accept($shipId)) {
            // Send notification to requester that their request was accepted
            Notification::createNotification(
                'friend_accept',
                $this->userId,
                $friendship['requester_id']
            );

            // Auto-create Chat
            $chatModel = new \App\Models\Chat();
            $chatMemberModel = new \App\Models\ChatMember();
            
            $existingChat = $chatModel->getPrivateChatId($this->userId, $friendship['requester_id']);
            
            if (!$existingChat) {
                // Create new private chat
                $newChatId = $chatModel->create([
                    'name' => '', // specific name not needed for private chat usually
                    'owner_id' => $this->userId,
                    'privacy' => 0, // Private
                    'created_at' => date('Y-m-d H:i:s')
                ]);
                
                if ($newChatId) {
                    $chatMemberModel->add($newChatId, $this->userId, true); // Admin role?
                    $chatMemberModel->add($newChatId, $friendship['requester_id'], false);
                }
            }

            return redirect()->back()->with('success', 'Friend request accepted.');
        }
        return redirect()->back()->with('error', 'Could not accept request.');
    }

    public function remove($shipId)
    {
        if (!$this->userId) return redirect()->to('login');
        
        // Verify the current user is involved in this friendship
        $friendship = $this->friend->find($shipId);
        
        if (!$friendship) {
            return redirect()->back()->with('error', 'Friendship not found.');
        }
        
        // User must be either the requester or receiver
        if ($friendship['requester_id'] !== $this->userId && $friendship['receiver_id'] !== $this->userId) {
            return redirect()->back()->with('error', 'You can only remove your own friendships.');
        }
        
        if ($this->friend->delete($shipId))
            return redirect()->back()->with('success', 'Action completed.');
        return redirect()->back()->with('error', 'Action failed.');
    }

    public function requests()
    {
        if (!$this->userId) return redirect()->to('login');

        $data = $this->getUserData();
        $data['targetId'] = $this->userId;
        
        $targetData = $this->getTargetUserData($this->userId);
        if($targetData) {
            $data['navbarAvatarUrl'] = $data['avatarUrl']; 
            $data['avatarUrl'] = $targetData['avatarUrl'];
            $data['coverUrl'] = $targetData['coverUrl'];
            $data['user'] = $targetData; 
        }

        $postModel = new Post();
        $data['posts'] = $postModel->where('id_user', $this->userId)->findAll();
        $data['friends'] = $this->friend->listFriend($this->userId);

        $data['title'] = 'Friend Requests';
        $data['activeTab'] = 'received';
        $data['list'] = $this->friend->listReceive($this->userId);

        return view('user/listFriend', $data);
    }

    public function sent()
    {
        if (!$this->userId) return redirect()->to('login');

        $data = $this->getUserData();
        $data['targetId'] = $this->userId;
        
        $targetData = $this->getTargetUserData($this->userId);
        if($targetData) {
            $data['navbarAvatarUrl'] = $data['avatarUrl']; 
            $data['avatarUrl'] = $targetData['avatarUrl'];
            $data['coverUrl'] = $targetData['coverUrl'];
            $data['user'] = $targetData; 
        }

        $postModel = new Post();
        $data['posts'] = $postModel->where('id_user', $this->userId)->findAll();
        $data['friends'] = $this->friend->listFriend($this->userId);

        $data['title'] = 'Sent Requests';
        $data['activeTab'] = 'sent';
        $data['list'] = $this->friend->listSend($this->userId);

        return view('user/listFriend', $data);
    }
}
