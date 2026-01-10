<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\FriendShip;
use App\Models\User;
use CodeIgniter\HTTP\ResponseInterface;

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

    public function index()
    {
        if (!$this->userId) return redirect()->to('login');
        
        $data = $this->getUserData();
        $data['title'] = 'All Friends';
        $data['activeTab'] = 'all';
        $data['list'] = $this->friend->listFriend($this->userId);
        
        return view('user/listFriend', $data);
    }

    // New methods for ID-based routing with privacy checks
    public function listFriend($id) {
        if (!$this->userId) return redirect()->to('login');
        
        $data = $this->getUserData(); 
        $data['targetId'] = $id;
        $data['title'] = 'Friends List';
        $data['activeTab'] = 'all';
        $data['list'] = $this->friend->listFriend($id);
        
        return view('user/listFriend', $data);
    }

    public function listReceive($id) {
        if (!$this->userId) return redirect()->to('login');
        if($id != $this->userId) {
            return redirect()->to('user/dashboard')->with('error', 'Unauthorized access to requests');
        }

        $data = $this->getUserData();
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
        $data['title'] = 'Sent Requests';
        $data['activeTab'] = 'sent';
        $data['list'] = $this->friend->listSend($id);

        return view('user/listFriend', $data);
    }

    public function send($receiverId)
    {
        if (!$this->userId) return redirect()->to('login');
        if ($this->userId == $receiverId)
            return redirect()->back()->with('error', 'You cannot add yourself.');
        $res = $this->friend->create($this->userId, $receiverId);
        
        if ($res) 
            return redirect()->back()->with('success', 'Friend request sent.');
        return redirect()->back()->with('error', 'Failed to send request.');
    }

    public function accept($shipId)
    {
        if (!$this->userId) return redirect()->to('login');

        if ($this->friend->accept($shipId))
            return redirect()->back()->with('success', 'Friend request accepted.');
        return redirect()->back()->with('error', 'Could not accept request.');
    }

    public function remove($shipId)
    {
        if (!$this->userId) return redirect()->to('login');
        if ($this->friend->delete($shipId))
            return redirect()->back()->with('success', 'Action completed.');
        return redirect()->back()->with('error', 'Action failed.');
    }

    public function requests()
    {
        if (!$this->userId) return redirect()->to('login');

        $data = $this->getUserData();
        $data['title'] = 'Friend Requests';
        $data['activeTab'] = 'received';
        $data['list'] = $this->friend->listReceive($this->userId);

        return view('user/listFriend', $data);
    }

    public function sent()
    {
        if (!$this->userId) return redirect()->to('login');

        $data = $this->getUserData();
        $data['title'] = 'Sent Requests';
        $data['activeTab'] = 'sent';
        $data['list'] = $this->friend->listSend($this->userId);

        return view('user/listFriend', $data);
    }
}
