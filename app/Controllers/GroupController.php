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
        $avatarUrl = $user['avatar'] ?? base_url('ICON/avatar-default.png');

        return view('group/index', [
            'user_id' => $this->user_id,
            'avatarUrl' => $avatarUrl
        ]);
    }

    public function createView() {
        if (!$this->user_id) return redirect()->to('/login');

        $db = \Config\Database::connect();
        $user = $db->table('users')->where('id', $this->user_id)->get()->getRowArray();
        $avatarUrl = $user['avatar'] ?? base_url('ICON/avatar-default.png');

        return view('group/create', [
            'user_id' => $this->user_id,
            'avatarUrl' => $avatarUrl
        ]);
    }

    public function show($groupId) {
        if (!$this->user_id) return redirect()->to('/login');

        $db = \Config\Database::connect();
        $user = $db->table('users')->where('id', $this->user_id)->get()->getRowArray();
        $avatarUrl = $user['avatar'] ?? base_url('ICON/avatar-default.png');

        $group = $this->group->find($groupId);
        if (!$group) return redirect()->to('/group');

        // Check if user is member
        $groupMember = new GroupMember();
        $is_member = $groupMember->isMember($groupId, $this->user_id);
        
        // Check if user is admin
        $membership = $db->table('group_members')
            ->where(['group_id' => $groupId, 'user_id' => $this->user_id])
            ->get()->getRowArray();
        $is_admin = $membership && ($membership['role'] === 'admin' || $group['owner_id'] === $this->user_id);

        // Get member count
        $memberCount = $db->table('group_members')
            ->where('group_id', $groupId)
            ->countAllResults();

        $group['member_count'] = $memberCount;

        // Get posts (only if member or public group)
        $posts = [];
        if ($is_member || $group['privacy'] === 'public') {
            $postModel = new \App\Models\Post();
            // TODO: Get group posts
        }

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
        $avatarUrl = $user['avatar'] ?? base_url('ICON/avatar-default.png');

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
            $data['posts'] = $postModel->attachMediaToPosts($posts);
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
            
            if (empty($group['cover_photo'])) $group['cover_photo'] = base_url('ICON/background-default.jpg');
            if (empty($group['avatar'])) $group['avatar'] = base_url('ICON/group-default.png');
            
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
                $groups[] = $group;
            }
        }
        return $this->response->setJSON(['status' => 'success', 'data' => $groups]);
    }

    public function getMembers($groupId) {
        if (!$this->user_id) return $this->response->setJSON(['status' => 'error']);
        $members = (new GroupMember())->getMembers($groupId); // This model method already does joins? Let's check model... yes
        
        // Wait, Model getMembers exists but returns array. Let's format it.
        // Actually model getMembers returns raw array with joins.
        // Let's stick to inline build or use model if it formats.
        // Model getMembers lines 70-76 returns joined data.
        
        $result = [];
        foreach ($members as $member) {
            $result[] = [
                'user_id' => $member['user_id'],
                'name' => $member['firstname'] . ' ' . $member['lastname'],
                'avatar' => $member['avatar'] ?? base_url('ICON/avatar-default.svg'),
                'role' => $member['role']
            ];
        }
        return $this->response->setJSON(['status' => 'success', 'data' => $result]);
    }
}

