<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\User;
use App\Models\Profile;
use App\Models\Admin;

class AuthController extends BaseController
{
    protected $user;
    protected $admin;
    protected $profile;

    public function __construct()
    {
        $this->user = new User();
        $this->admin = new Admin();
        $this->profile = new Profile();
    }

    public function loginView(){
        // session()->destroy();
        if (session()->get('logged_in'))
            return redirect()->to('user/profile');

        return view('authentication');
    }

    public function register(){
        $userData = [
            'email' => $this->request->getPost('email'),
            'password' => $this->request->getPost('password'),
        ];

        $profileData = [
            'firstname' => $this->request->getPost('firstname'),
            'lastname' => $this->request->getPost('lastname'),
            'birth' => $this->request->getPost('birth'),
            'gender' => $this->request->getPost('gender'),
        ];

        $res = $this->user->createAccount($userData, $profileData);
        if ($res['status']){
            session()->set([
                'user_id' => $res['id'],
                'username' => $userData['email'],
                'logged_in' => true,
            ]);
            return redirect()->to('user/dashboard')->with('success', 'Sign up completed successfully');
        }
        return redirect()->back()->withInput()->with('error_register', $res['message']);
    
    }

    public function login(){
        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');

        $exist = $this->user->where('email', $email)->first();
        if (!$exist)
            return redirect()->back()->with('error', 'Email is not exist');
        if (!password_verify($password, $exist['password']))
            return redirect()->back()->with('error', 'Password is incorrected');

        session()->set([
            'user_id' => $exist['id'],
            'username' => $exist['email'],
            'email' => $exist['email'],
            'type'      => $exist['type'],
            'logged_in' => true,
        ]);
        if ($exist['type'] === '1') {
            return redirect()->to('admin/dashboard');
        }
        return redirect()->to('user/profile/'.$exist['id']);
    }

    public function logout(){
        session()->destroy();
        return redirect()->to('/login');
    }

}
