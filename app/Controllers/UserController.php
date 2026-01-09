<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\Profile;
use App\Models\User;
use App\Models\PostModel;
use App\Models\FriendshipModel;

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

    public function index($profileId = null)
    {
        $currentUserId = $this->id; 
        
        if (!$currentUserId) {
            return redirect()->to('authentication');
        }


        $targetId = $profileId ?? $currentUserId; 

        $user = $this->user->find($targetId);
        $profile = $this->profile->where('id', $targetId)->first();

        $currentUser = $this->user->find($currentUserId);
        $myAvatarPath = $currentUser['avatar'] ?? 'ICON/avatar-default.svg';
        $data['myAvatar'] = str_starts_with($myAvatarPath, 'http') ? $myAvatarPath : base_url($myAvatarPath);

        if (!$user || !$profile) {
            if ($targetId == $currentUserId) {
                session()->destroy();
                return redirect()->to('/login');
            }
            return redirect()->to('/')->with('error', 'User not found');
        }

        $friendStatus = 'self'; 
    
        if ($currentUserId != $targetId) {
            $friendModel = new FriendshipModel();
            $friendStatus = $friendModel->checkFriendshipStatus($currentUserId, $targetId);
        }

        $friendModel = $friendModel ?? new FriendshipModel();
        $friendsCount = $friendModel->countFriends($targetId);

        $postModel = new PostModel();
        $posts = $postModel->getPostsByUserId($targetId, 5, 0);

        $data['user'] = array_merge($user, $profile);
        
        $userAvatarPath = $user['avatar'] ?? 'ICON/avatar-default.svg';
        $data['avatarUrl'] = str_starts_with($userAvatarPath, 'http') ? $userAvatarPath : base_url($userAvatarPath);
        
        $data['posts'] = $posts;
        $data['errors'] = session()->get('errors');
        
        $data['friendsCount'] = $friendsCount;

        $data['friendStatus'] = $friendStatus; 
        $data['profileId']    = $targetId;     

        return view('user/profile', $data);
    }

    public function updateProfile(){
        if (!$this->id) return redirect()->to('/login');
        
        $id = session()->get('user_id');

        $profileData = [
            'firstname' => $this->request->getPost('firstname'),
            'lastname'  => $this->request->getPost('lastname'),
            'birth'     => $this->request->getPost('birth'),
            'gender'    => $this->request->getPost('gender'),
        ];

        if (!$this->profile->update($id, $profileData)) {
            return redirect()->back()->with('error', 'Update profile info failed');
        }

        $file = $this->request->getFile('avatar');
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $cloudinary = new \App\Libraries\CloudinaryLib();
            $uploadedUrl = $cloudinary->uploadImage($file->getTempName(), 'social_avatars');

            if ($uploadedUrl) {
                $this->user->update($id, ['avatar' => $uploadedUrl]);
                $currentUser = $this->user->find($id);
                if (!empty($currentUser['avatar']) && !str_starts_with($currentUser['avatar'], 'http') && file_exists(FCPATH . $currentUser['avatar'])) {
                    unlink(FCPATH . $currentUser['avatar']);
                }
            }
        }

        $bgFile = $this->request->getFile('background');
        if ($bgFile && $bgFile->isValid() && !$bgFile->hasMoved()) {
            $cloudinary = new \App\Libraries\CloudinaryLib();
            
            $bgUrl = $cloudinary->uploadImage($bgFile->getTempName(), 'social_backgrounds');

            if ($bgUrl) {
                $this->profile->update($id, ['background' => $bgUrl]);
            }
        }

        return redirect()->to('user/profile')->with('success', 'Profile updated successfully');
    }

    public function changePassword(){
        if (!$this->id)
            return redirect()->to('authenticaton');
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
        if (!$this->id) return redirect()->to('/login'); 

        $password = $this->request->getPost('password');
        $exist = $this->user->find($this->id);
        
        if (!password_verify($password, $exist['password'])) {
            return redirect()->back()->with('error', 'Wrong password!');
        }

        if (!empty($exist['avatar']) && file_exists(FCPATH . $exist['avatar'])) {
            unlink(FCPATH . $exist['avatar']);
        }

        if ($this->user->delete($this->id)){
            session()->destroy();
            return redirect()->to('/login')->with('success', 'Account deleted.');
        }
        
        return redirect()->back()->with('error', 'Database error.');
    }
    public function loginView(){
        if (!$this->id)
            return redirect()->to('authenticaton');
        if (session()->get('logged_in')) {
            return redirect()->to('dashboard');
        }
        return view('authentication');
    }

    public function search()
    {
        $keyword = $this->request->getGet('q');
        
        $currentUserId = session()->get('user_id');
        if (!$currentUserId) return redirect()->to('/login');

        $results = [];
        if (!empty($keyword)) {
            $results = $this->user->searchUsers($keyword);
        }

        $currentUser = $this->user->find($currentUserId);
        $avatarPath = $currentUser['avatar'] ?? 'ICON/avatar-default.svg';
        $finalAvatarUrl = str_starts_with($avatarPath, 'http') ? $avatarPath : base_url($avatarPath);

        $data = [
            'user'      => $currentUser,
            'myAvatar'  => $finalAvatarUrl,
            'avatarUrl' => $finalAvatarUrl,
            'users'     => $results, 
            'keyword'   => $keyword
        ];

        return view('User/search_results', $data);
    }

    public function liveSearch()
    {
        $keyword = $this->request->getGet('q');
        
        if (empty($keyword)) return $this->response->setJSON([]);

        $results = $this->user->searchUsers($keyword);
        
        return $this->response->setJSON($results);
    }

}
