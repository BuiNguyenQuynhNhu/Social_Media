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
        $posts = $postModel->getNewsFeed($currentUserId, 1000, 0);

        $friendModel = new FriendshipModel();
        $friends = $friendModel->getAllFriends($currentUserId);

        $data = [
            'user'      => $currentUser, 
            'avatarUrl' => $finalAvatar,
            'posts'     => $posts,
            'friends'   => $friends
        ];

        return view('User/dashboard', $data);
    }
}