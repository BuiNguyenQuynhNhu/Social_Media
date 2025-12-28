<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\Profile;
use App\Models\User;

class UserController extends BaseController
{
    protected $user;
    protected $profile;
    protected $id;

    public function __construct(){
        $this->user = new User();
        $this->profile = new Profile();
        $this->id = session()->get('user_id');
    }
    public function index()
    {
        if (!$this->id)
            return redirect()->to('/login');
        $user = $this->user->find($this->id);
        $profile = $this->profile->where('id', $this->id)->first();

        if (!$user || !$profile) {
            session()->destroy();
            return redirect()->to('/login');
        }
        $data['user'] = array_merge($user, $profile);

        $data['avatarUrl'] = $user['avatar'] ? base_url($user['avatar']) : base_url('ICON/avatar-default.svg');
        return view('profile', $data);
    }

    public function updateProfile(){
        if (!$this->id)
            return redirect()->to('/login');
        $id = session()->get('user_id');
        $data = [
            'id' => $id,
            'firstname' => $this->request->getPost('firstname'),
            'lastname' => $this->request->getPost('lastname'),
            'birth' => $this->request->getPost('birth'),
            'gender' => $this->request->getPost('gender'),
        ];

        if ($this->profile->save($data))
            return redirect()->to('user/profile')->with('success', 'Profile updated successfully');

            var_dump($this->profile->errors()); die(); 
            return redirect()->back()->with('error', 'Update failed');
    }

    public function changePassword(){
        if (!$this->id)
            return redirect()->to('/login');
        $rules = [
            'old' => 'required',
            'new' => 'required|min_length[8]',
            'confirm' => 'matches[new]',
        ];

        if ($this->validate($rules))
            return redirect()->back()->with('error', $this->validator->getErrors());
        $exist = $this->user->find($this->id);
        $old = $this->request->getPost('old');

        if (!password_verify($old, $exist['password']))
            return redirect()->back()->with('error', 'New and old password is not matching');

        $new = $this->request->getPost('new');
        $this->user->updatePassword($this->id, $new);
        return redirect()->back()->with('success', 'Password change have done');
    }

    public function updateAvatar(){
        if (!$this->id)
            return redirect()->to('user/profile');
    }

    public function deleteAccount(){
        if (!$this->id)
            return redirect()->to('/login');

        $password = $this->request->getPost('password');
        if (empty($password))
            return redirect()->back()->with('error', 'Please fill the password');

        $exist = $this->user->find($this->id);
        
        if (!password_verify($password, $exist['password']))
            return redirect()->back()->with('error', 'Password is not corrected');

        if ($this->user->delete($this->id)){
            session()->destroy();
            return redirect()->to('/login')->with('success', 'Account is deleted');
        }
        return redirect()->back()->with('error', 'Have error, cannot delete you account');
    }
    public function loginView(){
        if (!$this->id)
            return redirect()->to('/login');
        if (session()->get('logged_in')) {
            return redirect()->to('/dashboard');
        }
        return view('authentication');
    }

}
