<?php

namespace App\Controllers;

use App\Models\Group;
use App\Models\GroupMember;
use App\Models\Chat;
use App\Models\ChatMember;
use App\Models\Requirement;
use App\Libraries\MongoDB;

class GroupController extends BaseController
{
    protected $group;
    protected $mongo;
    protected $user_id;

    public function __construct(){
        $this->group = new Group();
        $this->mongo = new MongoDB();
        $this->user_id = session()->get('user_id');
    }

    public function index() {
        if (!$this->user_id) return redirect()->to('/login');

        $db = \Config\Database::connect();
        $user = $db->table('users')->where('id', $this->user_id)->get()->getRowArray();
        
        $uAvatar = $user['avatar'] ?? '';
        $avatarUrl = (strpos($uAvatar, 'http') === 0) 
            ? $uAvatar 
            : ($uAvatar ? base_url($uAvatar) : base_url('ICON/avatar-default.svg'));

        return view('group/index', [
            'user_id' => $this->user_id,
            'avatarUrl' => $avatarUrl
        ]);
    }

    public function createView() {
        if (!$this->user_id) return redirect()->to('/login');

        $db = \Config\Database::connect();
        $user = $db->table('users')->where('id', $this->user_id)->get()->getRowArray();
        
        $uAvatar = $user['avatar'] ?? '';
        $avatarUrl = (strpos($uAvatar, 'http') === 0) 
            ? $uAvatar 
            : ($uAvatar ? base_url($uAvatar) : base_url('ICON/avatar-default.png'));

        return view('group/create', [
            'user_id' => $this->user_id,
            'avatarUrl' => $avatarUrl
        ]);
    }

    public function show($groupId) {
        if (!$this->user_id) return redirect()->to('/login');

        $db = \Config\Database::connect();
        $user = $db->table('users')->where('id', $this->user_id)->get()->getRowArray();
        
        $uAvatar = $user['avatar'] ?? '';
        $avatarUrl = (strpos($uAvatar, 'http') === 0) 
            ? $uAvatar 
            : ($uAvatar ? base_url($uAvatar) : base_url('ICON/avatar-default.png'));
            
        // ... (Redirects to feed usually)
        return redirect()->to("/group/$groupId/feed");
    }

    public function feed($groupId) {
        if (!$this->user_id) return redirect()->to('/login');
        return $this->renderGroupPage($groupId, 'feed');
    }

    public function membersView($groupId) {
        if (!$this->user_id) return redirect()->to('/login');
        return $this->renderGroupPage($groupId, 'members');
    }

    public function aboutView($groupId) {
        if (!$this->user_id) return redirect()->to('/login');
        return $this->renderGroupPage($groupId, 'about');
    }

    private function renderGroupPage($groupId, $page) {
        $db = \Config\Database::connect();
        $user = $db->table('users')
            ->select('users.*, profiles.firstname, profiles.lastname')
            ->join('profiles', 'profiles.id = users.id')
            ->where('users.id', $this->user_id)
            ->get()->getRowArray();
            
        $uAvatar = $user['avatar'] ?? '';
        $avatarUrl = (strpos($uAvatar, 'http') === 0) 
            ? $uAvatar 
            : ($uAvatar ? base_url($uAvatar) : base_url('ICON/avatar-default.png'));

        $group = $this->group->find($groupId);
        if (!$group) return redirect()->to('/group');

        // Check membership
        $membership = $db->table('group_members')
            ->where(['group_id' => $groupId, 'user_id' => $this->user_id])
            ->get()->getRowArray();
        
        $is_member = !empty($membership);
        $is_admin = $membership && ($membership['role'] === 'admin' || $group['owner_id'] === $this->user_id);

        // Get member count
        $group['member_count'] = $db->table('group_members')->where('group_id', $groupId)->countAllResults();

        // Data specific to page
        $data = [
            'user_id' => $this->user_id,
            'user' => $user,
            'avatarUrl' => $avatarUrl,
            'group' => $group,
            'is_member' => $is_member,
            'is_admin' => $is_admin,
            'active_tab' => $page
        ];

        if ($page === 'feed') {
            $postModel = new \App\Models\Post();
            $posts = $postModel->getPostsByGroup($groupId);
            $data['posts'] = $postModel->attachMediaToPosts($posts); // Assume attachMediaToPosts handles avatars correctly or PostComponent does (we checked PostItem)
        }

        return view("group/$page", $data);
    }

    public function create(){
        $data = [
            'name' => $this->request->getPost('name'),
            'owner_id' => $this->user_id,
            'privacy' => $this->request->getPost('privacy') ?? 'public'
        ];

        try {
            $groupId = $this->group->createGroup($data, $this->user_id);
            if (!$groupId) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'Failed to create group']);
            }
            return $this->response->setJSON(['status' => 'success', 'group_id' => $groupId]);
        } catch (\Exception $e) {
            log_message('error', 'Group Create Error: ' . $e->getMessage());
            return $this->response->setJSON(['status' => 'error', 'message' => 'Server Error']);
        }
    }

    public function myGroups() {
        if (!$this->user_id) return $this->response->setJSON(['status' => 'error']);

        $db = \Config\Database::connect();
        $memberships = $db->table('group_members')
            ->where('user_id', $this->user_id)
            ->get()->getResultArray();

        $groups = [];
        foreach ($memberships as $membership) {
            $group = $this->group->find($membership['group_id']);
            if (!$group) continue;

            $group['member_count'] = $db->table('group_members')->where('group_id', $group['id'])->countAllResults();
            
            // Fix Cover Photo URL
            $cover = $group['cover_photo'] ?? '';
            $group['cover_photo'] = (strpos($cover, 'http') === 0) ? $cover : ($cover ? base_url($cover) : base_url('ICON/background-default.jpg'));
            
            // Fix Avatar URL
            $avatar = $group['avatar'] ?? '';
            $group['avatar'] = (strpos($avatar, 'http') === 0) ? $avatar : ($avatar ? base_url($avatar) : base_url('ICON/group-default.png'));
            
            $groups[] = $group;
        }

        return $this->response->setJSON(['status' => 'success', 'data' => $groups]);
    }

    public function suggested() {
        if (!$this->user_id) return $this->response->setJSON(['status' => 'error']);

        $db = \Config\Database::connect();
        $allGroups = $this->group->where('privacy', 'public')->findAll(10);

        $groups = [];
        foreach ($allGroups as $group) {
            $isMember = $db->table('group_members')
                ->where(['group_id' => $group['id'], 'user_id' => $this->user_id])
                ->countAllResults() > 0;

            if (!$isMember) {
                $group['member_count'] = $db->table('group_members')->where('group_id', $group['id'])->countAllResults();
                
                // Fix URLs for JSON response
                $cover = $group['cover_photo'] ?? '';
                $group['cover_photo'] = (strpos($cover, 'http') === 0) ? $cover : ($cover ? base_url($cover) : base_url('ICON/background-default.jpg'));
                
                $avatar = $group['avatar'] ?? '';
                $group['avatar'] = (strpos($avatar, 'http') === 0) ? $avatar : ($avatar ? base_url($avatar) : base_url('ICON/group-default.png'));

                $groups[] = $group;
            }
        }
        return $this->response->setJSON(['status' => 'success', 'data' => $groups]);
    }

    public function getMembers($groupId) {
        if (!$this->user_id) {
            return $this->response->setJSON(['status' => 'error']);
        }
        
        $members = (new GroupMember())->getMembers($groupId);
        
        $result = [];
        foreach ($members as $member) {
            $mAvatar = $member['avatar'] ?? '';
            $result[] = [
                'user_id' => $member['user_id'],
                'name' => $member['firstname'] . ' ' . $member['lastname'],
                'avatar' => (strpos($mAvatar, 'http') === 0) ? $mAvatar : ($mAvatar ? base_url($mAvatar) : base_url('ICON/avatar-default.svg')),
                'role' => $member['role']
            ];
        }
        
        return $this->response
            ->setContentType('application/json')
            ->setJSON(['status' => 'success', 'data' => $result]);
    }

    public function join($groupId) {
        if (!$this->user_id) return $this->response->setJSON(['status' => 'error', 'message' => 'Login required']);
        
        $group = $this->group->find($groupId);
        if (!$group) return $this->response->setJSON(['status' => 'error', 'message' => 'Group not found']);

        $memberModel = new GroupMember();
        if ($memberModel->isMember($groupId, $this->user_id)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Already a member']);
        }

        // Check privacy and requirements
        $role = ($group['privacy'] === 'public') ? 'member' : 'pending';
        
        $memberModel->add($groupId, $this->user_id, $role);
        
        if ($role === 'pending') {
            $admins = $memberModel->where('group_id', $groupId)->where('role', 'admin')->findAll();
            $ownerId = $group['owner_id'];
            \App\Models\Notification::createNotification('new_join_request', $this->user_id, $ownerId, ['group_id' => $groupId]);
            
            foreach ($admins as $admin) {
                if ($admin['user_id'] !== $ownerId) {
                    \App\Models\Notification::createNotification('new_join_request', $this->user_id, $admin['user_id'], ['group_id' => $groupId]);
                }
            }
            return $this->response->setJSON(['status' => 'success', 'message' => 'Request sent']);
        } 

        return $this->response->setJSON(['status' => 'success', 'message' => 'Joined successfully']);
    }

    public function leave($groupId) {
        if (!$this->user_id) return $this->response->setJSON(['status' => 'error', 'message' => 'Login required']);
        
        $group = $this->group->find($groupId);
        if ($group['owner_id'] === $this->user_id) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Owner cannot leave group']);
        }

        $memberModel = new GroupMember();
        $memberModel->where(['group_id' => $groupId, 'user_id' => $this->user_id])->delete();
        
        return $this->response->setJSON(['status' => 'success']);
    }

}
