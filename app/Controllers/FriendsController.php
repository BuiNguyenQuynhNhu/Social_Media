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
        if (!$this->userId) return redirect()->to('login');
        
        $data = $this->getUserData();
        $data['title'] = 'Friends';
        $data['requests'] = $this->friendModel->listReceive($this->userId);
        $data['suggestions'] = []; // Implement logic later
        $data['friends'] = $this->friendModel->listFriend($this->userId);
        
        // Pass all data to a combined view
        return view('friends/layout', [
            'content' => view('friends/combined', $data), // We will create this
            'active_tab' => 'all', // Not strictly needed for scroll spy but good for default
            'avatarUrl' => $data['avatarUrl']
        ]);
    }

    // Keep these as API endpoints or specific redirects if needed
    public function requests() { return redirect()->to('friends#requests'); }
    public function suggestions() { return redirect()->to('friends#suggestions'); }
    public function list() { return redirect()->to('friends#list'); }
}
