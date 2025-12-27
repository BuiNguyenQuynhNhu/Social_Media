<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\User;
use App\Models\Profile;

class AuthController extends BaseController
{
    protected $user;
    protected $profile;

    public function __construct()
    {
        $this->user = new User();
        $this->profile = new Profile();
    }

    public function register(){
        $userData = [
            'email' => $this->request->getPost('email'),
            'password' => $this->request->getPost('password'),
            'avatar' => $this->request->getPost('avatar'),
        ];

        $profileData = [
            'firstname' => $this->request->getPost('firstname'),
            'lastname' => $this->request->getPost('lastname'),
            'birth' => $this->request->getPost('birth'),
            'gender' => $this->request->getPost('gender'),
        ];

        $res = $this->user->createAccount($userData, $profileData);
        if ($res['status'])
            return redirect()->to('/login')->with('success', 'Sign up completed successfully');
        else
            return redirect()->back()->withInput()->with('error', $res['message']);
    }

    public function login(){
        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');

        $exist = $this->user->where('email', $email)->first();
        if (!$exist)
            return redirect()->back()->with('error', 'Email is not exist');
        if (!password_verify($password, $exist['password']))
            return redirect()->back()->with('error', 'Password is not corrected');

        session()->set([
            'user_id' => $exist['id'],
            'username' => $exist['password'],
            'logged_in' => true,
        ]);
        return redirect()->to('/dashboard');
    }

}
