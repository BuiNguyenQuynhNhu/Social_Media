<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\Admin;
use App\Models\User;
use App\Models\Post;

class AdminController extends BaseController
{
    protected $admin;
    protected $id;

    public function __construct(){
        $this->admin = new Admin();
        $type = session()->get('type');
        $this->id = session()->get('user_id');

        if (!$this->id || $type !== '1'){
            return redirect()->to('login')->send();
        }
        
    }
    public function index()
    {
        if (!$this->id)
            return redirect()->to('login')->send();
        $admin = $this->admin->find($this->id);
        return view('admin/dashboard', $admin);
    }

    public function listUser(){
        $Model = new User;
        $data = [
            'title' => 'Users',
            'users' => $Model->list(),
            'admin' => $this->admin,
        ];
        return view('admin/listUser', $data);

    }

    public function listPost() {
        $Model = new Post();
        
        $data = [
            'title'     => 'Posts',
            'posts'     => $Model->list(),
            'admin' => $this->admin,
        ];

        return view('admin/listPost', $data);
    }

    public function listGroup(){
        $model = new \App\Models\Group();
        $data = [
            'title' => 'Groups',
            'groups' => $model->findAll(),
            'admin' => $this->admin,
        ];
        return view('admin/listGroup', $data);
    }

    public function listChat(){
        $model = new \App\Models\Chat();
        $data = [
            'title' => 'Chats',
            'chats' => $model->findAll(),
            'admin' => $this->admin,
        ];
        return view('admin/listChat', $data);
    }
}
