<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\Profile;
use App\Models\User;
use App\Models\Post;
use App\Models\FriendShip;
use App\Libraries\Cloudinary;

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
            return redirect()->to('login');
        $user = $this->user->find($this->id);
        $profile = $this->profile->where('id', $this->id)->first();

        if (!$user || !$profile) {
            session()->destroy();
            return redirect()->to('/login');
        }
        $data['user'] = array_merge($user, $profile);
        $data['user_id'] = $this->id; 
        $data['keyword'] = $this->request->getGet('q') ?? '';

        
        $data['avatarUrl'] = (strpos($user['avatar'], 'http') === 0) 
            ? $user['avatar'] 
            : ($user['avatar'] ? base_url($user['avatar']) : base_url('ICON/avatar-default.svg'));

        // Load posts for the dashboard feed
        $postController = new PostController();
        $data['posts'] = $postController->listAll(); 

        return view('dashboard', $data);
    }

    public function profile($id = null){
        if (!$this->id)
            return redirect()->to('authenticaton');
        $targetId = $id ?? $this->id;
    
        $user = $this->user->find($targetId);
        $profile = $this->profile->where('id', $targetId)->first();

        if (!$user || !$profile) {
            session()->destroy();
            return redirect()->to('/login');
        }
        $friend = new FriendShip();
        $postController = new PostController();
        $data['posts'] = $postController->listUserPosts($targetId);
        $data['user'] = array_merge($user, $profile);
        $data['friends'] = $friend->listFriend($targetId);
        $data['avatarUrl'] = (strpos($user['avatar'], 'http') === 0) 
            ? $user['avatar'] 
            : ($user['avatar'] ? base_url($user['avatar']) : base_url('ICON/avatar-default.svg'));

        $data['coverUrl'] = (strpos($profile['background'], 'http') === 0) 
            ? $profile['background'] 
            : (!empty($profile['background']) ? base_url($profile['background']) : base_url('ICON/background-default.jpg'));
        return view('user/profile', $data);
    }

    public function updateProfile($id = null) {
        if (!$this->id) return redirect()->to('/login');
        
        $cloudinary = new Cloudinary();
        $targetId = $id ?? $this->id;

        $profileData = [
            'id'        => $targetId,
            'firstname' => $this->request->getPost('firstname'),
            'lastname'  => $this->request->getPost('lastname'),
            'birth'     => $this->request->getPost('birth'),
            'gender'    => $this->request->getPost('gender'),
        ];

        $cover = $this->request->getFile('cover');
        if ($cover && $cover->isValid()) {
            $profileData['background'] = $cloudinary->upload($cover->getTempName(), 'covers');
        }

        if ($this->profile->save($profileData)) {
            $avatar = $this->request->getFile('avatar');
            if ($avatar && $avatar->isValid()) {
                $avatarUrl = $cloudinary->upload($avatar->getTempName(), 'avatars');
                $this->user->update($targetId, ['avatar' => $avatarUrl]);
            }
            return redirect()->to("user/profile/$targetId")->with('success', 'Update successfully');
        }

        return redirect()->back()->with('error', 'Update failed');
    }

    public function changePassword(){
        if (!$this->id)
            return redirect()->to('login');
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

    public function updateBackground(){
        if (!$this->id)
            return redirect()->to('user/profile');
    }

    public function deleteAccount(){
        if (!$this->id)
            return redirect()->to('authenticaton');

        $password = $this->request->getPost('password');
        if (empty($password))
            return redirect()->back()->with('error', 'Please fill the password');

        $exist = $this->user->find($this->id);
        
        if (!password_verify($password, $exist['password']))
            return redirect()->back()->with('error', 'Password is not corrected');

        if ($this->user->delete($this->id)){
            session()->destroy();
            return redirect()->to('authenticaton')->with('success', 'Account is deleted');
        }
        return redirect()->back()->with('error', 'Have error, cannot delete you account');
    }
    public function loginView(){
        if (!$this->id)
            return redirect()->to('authenticaton');
        if (session()->get('logged_in')) {
            return redirect()->to('dashboard');
        }
        return view('authentication');
    }

}
