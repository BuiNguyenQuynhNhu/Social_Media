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
        $avatarUrl = $user['avatar'] ?? base_url('images/default-avatar.png');

        return view('group/index', [
            'user_id' => $this->user_id,
            'avatarUrl' => $avatarUrl
        ]);
    }

    public function createView() {
        if (!$this->user_id) return redirect()->to('/login');

        $db = \Config\Database::connect();
        $user = $db->table('users')->where('id', $this->user_id)->get()->getRowArray();
        $avatarUrl = $user['avatar'] ?? base_url('images/default-avatar.png');

        return view('group/create', [
            'user_id' => $this->user_id,
            'avatarUrl' => $avatarUrl
        ]);
    }

    public function show($groupId) {
        if (!$this->user_id) return redirect()->to('/login');

        $db = \Config\Database::connect();
        $user = $db->table('users')->where('id', $this->user_id)->get()->getRowArray();
        $avatarUrl = $user['avatar'] ?? base_url('images/default-avatar.png');

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

        return view('group/index', [
            'user_id' => $this->user_id,
            'avatarUrl' => $avatarUrl,
            'group' => $group,
            'is_member' => $is_member,
            'is_admin' => $is_admin,
            'posts' => $posts
        ]);
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

            // Get member count
            $memberCount = $db->table('group_members')
                ->where('group_id', $group['id'])
                ->countAllResults();

            $group['member_count'] = $memberCount;
            $groups[] = $group;
        }

        return $this->response->setJSON(['status' => 'success', 'data' => $groups]);
    }

    public function suggested() {
        if (!$this->user_id) return $this->response->setJSON(['status' => 'error']);

        // Get public groups that user is not a member of
        $db = \Config\Database::connect();
        $allGroups = $this->group->where('privacy', 'public')->findAll(10);

        $groups = [];
        foreach ($allGroups as $group) {
            $isMember = $db->table('group_members')
                ->where(['group_id' => $group['id'], 'user_id' => $this->user_id])
                ->countAllResults() > 0;

            if (!$isMember) {
                $memberCount = $db->table('group_members')
                    ->where('group_id', $group['id'])
                    ->countAllResults();
                $group['member_count'] = $memberCount;
                $groups[] = $group;
            }
        }

        return $this->response->setJSON(['status' => 'success', 'data' => $groups]);
    }

    public function getMembers($groupId) {
        if (!$this->user_id) return $this->response->setJSON(['status' => 'error']);

        $db = \Config\Database::connect();
        $members = $db->table('group_members')
            ->select('group_members.*, users.avatar, profiles.firstname, profiles.lastname')
            ->join('users', 'users.id = group_members.user_id')
            ->join('profiles', 'profiles.id = group_members.user_id')
            ->where('group_members.group_id', $groupId)
            ->get()->getResultArray();

        $result = [];
        foreach ($members as $member) {
            $result[] = [
                'user_id' => $member['user_id'],
                'name' => $member['firstname'] . ' ' . $member['lastname'],
                'avatar' => $member['avatar'] ?? base_url('images/default-avatar.png'),
                'role' => $member['role']
            ];
        }

        return $this->response->setJSON(['status' => 'success', 'data' => $result]);
    }

    public function join($groupId) {
        if (!$this->user_id) return $this->response->setJSON(['status' => 'error']);

        $group = $this->group->find($groupId);
        if (!$group) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Group not found']);
        }

        $groupMember = new GroupMember();
        if ($groupMember->isMember($groupId, $this->user_id)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Already a member']);
        }

        if ($groupMember->add($groupId, $this->user_id)) {
            return $this->response->setJSON(['status' => 'success']);
        }

        return $this->response->setJSON(['status' => 'error']);
    }

    public function leave($groupId) {
        if (!$this->user_id) return $this->response->setJSON(['status' => 'error']);

        $group = $this->group->find($groupId);
        if ($group && $group['owner_id'] === $this->user_id) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Owner cannot leave group']);
        }

        $groupMember = new GroupMember();
        if ($groupMember->deleteMember($groupId, $this->user_id)) {
            return $this->response->setJSON(['status' => 'success']);
        }

        return $this->response->setJSON(['status' => 'error']);
    }

    public function update($groupId) {
        if (!$this->user_id) return $this->response->setJSON(['status' => 'error']);

        $group = $this->group->find($groupId);
        if (!$group || $group['owner_id'] !== $this->user_id) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Unauthorized']);
        }

        $data = [];
        if ($name = $this->request->getPost('name')) {
            $data['name'] = $name;
        }
        if ($description = $this->request->getPost('description')) {
            $data['description'] = $description;
        }

        if (!empty($data) && $this->group->update($groupId, $data)) {
            return $this->response->setJSON(['status' => 'success']);
        }

        return $this->response->setJSON(['status' => 'error']);
    }

    public function updatePrivacy($groupId) {
        if (!$this->user_id) return $this->response->setJSON(['status' => 'error']);

        $group = $this->group->find($groupId);
        if (!$group || $group['owner_id'] !== $this->user_id) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Unauthorized']);
        }

        $privacy = $this->request->getPost('privacy');
        if ($privacy && $this->group->changePrivacy($groupId, $privacy)) {
            return $this->response->setJSON(['status' => 'success']);
        }

        return $this->response->setJSON(['status' => 'error']);
    }

    public function updatePostRequirements($groupId) {
        if (!$this->user_id) return $this->response->setJSON(['status' => 'error']);

        $group = $this->group->find($groupId);
        if (!$group || $group['owner_id'] !== $this->user_id) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Unauthorized']);
        }

        $requireApproval = $this->request->getPost('require_approval');
        // TODO: Implement post requirement logic in database

        return $this->response->setJSON(['status' => 'success']);
    }

    public function delete($groupId) {
        if (!$this->user_id) return $this->response->setJSON(['status' => 'error']);

        $group = $this->group->find($groupId);
        if (!$group || $group['owner_id'] !== $this->user_id) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Unauthorized']);
        }

        if ($this->group->delete($groupId)) {
            // Also delete all members
            $db = \Config\Database::connect();
            $db->table('group_members')->where('group_id', $groupId)->delete();

            return $this->response->setJSON(['status' => 'success']);
        }

        return $this->response->setJSON(['status' => 'error']);
    }

    public function create(){
        $data = [
            'name' => $this->request->getPost('name'),
            'owner_id' => $this->user_id,
            'privacy' => $this->request->getPost('privacy') ?? 'public'
        ];

        $groupId = $this->group->create($data);

        (new GroupMember())->add($groupId, $this->user_id, 'admin');
        (new Chat())->insert(['id' => $groupId, 'name' => $data['name'], 'owner_id' => $this->user_id]);
        (new ChatMember())->add($groupId, $this->user_id, 'admin');
        return $this->response->setJSON(['status' => 'success', 'group_id' => $groupId]);
    }

    public function setRequire($groupId){
        $questions = $this->request->getPost('questions');
        
        $this->mongo->getCollection('requirement_configs')->replaceOne(
            ['_id' => $groupId],
            ['_id' => $groupId, 'questions' => $questions, 'updated_at' => date('Y-m-d H:i:s')],
            ['upsert' => true]
        );

        return $this->response->setJSON(['status' => 'success']);
    }
    
    public function promote($groupId, $targetUserId) {
        $group = $this->group->find($groupId);
        if ($group['owner_id'] !== $this->user_id) return $this->response->setJSON(['status' => 'error']);

        (new GroupMember())->promote($groupId, $targetUserId);
        return $this->response->setJSON(['status' => 'success']);
    }
}
