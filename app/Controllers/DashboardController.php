<?php

namespace App\Controllers;

use App\Models\PostModel;
use App\Models\User;
use App\Models\Profile; 
use App\Models\FriendshipModel;

class DashboardController extends BaseController
{
    public function index()
    {
        $currentUserId = session()->get('user_id');
        if (!$currentUserId) return redirect()->to('/login');

        $userModel = new User();
        $user = $userModel->find($currentUserId);

        $profileModel = new Profile();
        $profile = $profileModel->where('id', $currentUserId)->first();

        $currentUser = array_merge($user, $profile);

        $avatarPath = $currentUser['avatar'] ?? 'ICON/avatar-default.svg';
        $finalAvatar = str_starts_with($avatarPath, 'http') ? $avatarPath : base_url($avatarPath);

        $postModel = new PostModel();
        $posts = $postModel->getNewsFeed($currentUserId, 10, 0);

        $friendModel = new FriendshipModel();
        $friends = $friendModel->getAllFriends($currentUserId);

        // Get recent conversations - simplified approach
        $friendshipModel = new \App\Models\FriendShip();
        $allFriends = $friendshipModel->listFriend($currentUserId);
        
        $recentChats = [];
        foreach (array_slice($allFriends, 0, 5) as $friend) {
            $recentChats[] = [
                'friend_id' => $friend['id'],
                'name' => $friend['firstname'] . ' ' . $friend['lastname'],
                'avatar' => $friend['avatar'] ? base_url($friend['avatar']) : base_url('ICON/avatar-default.svg'),
                'last_message' => 'Send a message',
                'last_message_time' => null
            ];
        }

        $data = [
            'user'      => $currentUser, 
            'avatarUrl' => $finalAvatar,
            'posts'     => $posts,
            'friends'   => $friends,
            'recentChats' => $recentChats
        ];

        return view('User/dashboard', $data);
    }
}