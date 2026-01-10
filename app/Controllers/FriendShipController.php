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
    public function index()
    {
        if (!$this->userId) return redirect()->to('login');

        $data = [
            'title'     => 'All Friends',
            'activeTab' => 'all',
            'list'      => $this->friend->listFriend($this->userId)
        ];
        return view('user/listFriend', $data);
    }

    public function listFriend(){
        
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

        $data = [
            'title'     => 'Friend Requests',
            'activeTab' => 'received',
            'list'      => $this->friend->listReceive($this->userId),
        ];

        return view('user/listFriend', $data);
    }

    public function sent()
    {
        if (!$this->userId) return redirect()->to('login');

        $data = [
            'title'     => 'Sent Requests',
            'activeTab' => 'sent',
            'list'      => $this->friend->listSend($this->userId),
        ];

        return view('user/listFriend', $data);
    }
}
