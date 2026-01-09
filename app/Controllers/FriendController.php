<?php

namespace App\Controllers;

use App\Models\FriendshipModel;
use App\Models\User;

class FriendController extends BaseController
{
    protected $friendModel;

    public function __construct()
    {
        $this->friendModel = new FriendshipModel();
    }

    public function index()
    {
        if (!session()->get('logged_in')) return redirect()->to('/login');

        $currentUserId = session()->get('user_id');
        $userModel = new User();

        $currentUser = $userModel->find($currentUserId);

        $requests = $this->friendModel->getIncomingRequests($currentUserId);
        $friends  = $this->friendModel->getAllFriends($currentUserId);

        $avatarPath = $currentUser['avatar'] ?? 'ICON/avatar-default.svg';
        $finalAvatar = str_starts_with($avatarPath, 'http') ? $avatarPath : base_url($avatarPath);

        $data = [
            'user'      => $currentUser,
            'avatarUrl' => $finalAvatar, 
            'requests'  => $requests,
            'friends'   => $friends
        ];

        return view('User/friends', $data);
    }

    public function addFriend()
    {
        if (!session()->get('logged_in')) return $this->response->setJSON(['status' => 'error', 'message' => 'Unauthorized']);

        $requesterId = session()->get('user_id');
        $receiverId = $this->request->getPost('receiver_id');

        $exists = $this->friendModel->checkFriendshipStatus($requesterId, $receiverId);
        if ($exists !== 'not_friends') {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Request already exists']);
        }

        $this->friendModel->insert([
            'requester_id' => $requesterId,
            'receiver_id'  => $receiverId,
            'status'       => 'pending'
        ]);

        return $this->response->setJSON(['status' => 'success', 'new_status' => 'request_sent']);
    }

    public function acceptFriend()
    {
        $currentUserId = session()->get('user_id');
        $requesterId = $this->request->getPost('requester_id');

        $this->friendModel
             ->where('requester_id', $requesterId)
             ->where('receiver_id', $currentUserId)
             ->set(['status' => 'accepted'])
             ->update();

        return $this->response->setJSON(['status' => 'success', 'new_status' => 'friends']);
    }

    public function removeFriend()
    {
        $currentUserId = session()->get('user_id');
        $targetId = $this->request->getPost('target_id');

        $this->friendModel->groupStart()
                ->where('requester_id', $currentUserId)->where('receiver_id', $targetId)
            ->groupEnd()
            ->orGroupStart()
                ->where('requester_id', $targetId)->where('receiver_id', $currentUserId)
            ->groupEnd()
            ->delete();

        return $this->response->setJSON(['status' => 'success', 'new_status' => 'not_friends']);
    }
    
}