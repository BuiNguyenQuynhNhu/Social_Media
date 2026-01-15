<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\FriendShip;
use App\Models\User;
use App\Models\Profile;

class FriendsController extends BaseController
{
    protected $friendModel;
    protected $userModel;
    protected $userId;

    public function __construct()
    {
        $this->friendModel = new FriendShip();
        $this->userModel = new User();
        $this->userId = session()->get('user_id');
    }

    private function getUserData() {
        $user = $this->userModel->find($this->userId);
        $avatarUrl = (strpos($user['avatar'], 'http') === 0) 
            ? $user['avatar'] 
            : ($user['avatar'] ? base_url($user['avatar']) : base_url('ICON/avatar-default.svg'));
        return ['user' => $user, 'avatarUrl' => $avatarUrl, 'user_id' => $this->userId];
    }

    public function index()
    {
        return $this->requests(); // Default to requests or home of friends
    }

    public function requests()
    {
        if (!$this->userId) return redirect()->to('login');
        
        $data = $this->getUserData();
        $data['title'] = 'Friend Requests';
        $data['active_tab'] = 'requests';
        $data['requests'] = $this->friendModel->listReceive($this->userId);
        
        return view('friends/layout', [
            'content' => view('friends/requests', $data),
            'active_tab' => 'requests',
            'avatarUrl' => $data['avatarUrl']
        ]);
    }

    public function suggestions()
    {
        if (!$this->userId) return redirect()->to('login');

        $data = $this->getUserData();
        $data['title'] = 'People You May Know';
        $data['active_tab'] = 'suggestions';
        // Placeholder for suggestions logic
        $data['suggestions'] = []; 
        
        return view('friends/layout', [
            'content' => view('friends/suggestions', $data),
            'active_tab' => 'suggestions',
            'avatarUrl' => $data['avatarUrl']
        ]);
    }

    public function list()
    {
        if (!$this->userId) return redirect()->to('login');

        $data = $this->getUserData();
        $data['title'] = 'All Friends';
        $data['active_tab'] = 'all';
        $data['friends'] = $this->friendModel->listFriend($this->userId);

        return view('friends/layout', [
            'content' => view('friends/list', $data),
            'active_tab' => 'all',
            'avatarUrl' => $data['avatarUrl']
        ]);
    }
}
