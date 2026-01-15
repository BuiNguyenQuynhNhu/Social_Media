<?php

namespace App\Controllers;

use App\Models\Group;
use App\Models\GroupMember;
use App\Models\Requirement;
use App\Models\Post;

class GroupManageController extends BaseController
{
    protected $group;
    protected $user_id;

    public function __construct(){
        $this->group = new Group();
        $this->user_id = session()->get('user_id');
    }

    private function checkAccess($groupId, $requiredRole = ['owner', 'admin']) {
        if (!$this->user_id) return false;
        
        $group = $this->group->find($groupId);
        if (!$group) return false;

        if ($group['owner_id'] === $this->user_id && in_array('owner', $requiredRole)) return $group;

        $db = \Config\Database::connect();
        $membership = $db->table('group_members')->where(['group_id' => $groupId, 'user_id' => $this->user_id])->get()->getRowArray();
        
        if ($membership && in_array('admin', $requiredRole) && $membership['role'] === 'admin') return $group;
        
        return false;
    }

    public function manage($groupId) {
        return redirect()->to("/group/$groupId/manage/overview");
    }

    public function overview($groupId) {
        return $this->renderPage($groupId, 'overview');
    }

    public function members($groupId) {
        return $this->renderPage($groupId, 'members');
    }

    public function posts($groupId) {
        return $this->renderPage($groupId, 'posts');
    }

    public function requirements($groupId) {
        return $this->renderPage($groupId, 'requirements');
    }

    public function settings($groupId) {
        $group = $this->checkAccess($groupId, ['owner']); 
        $group = $this->checkAccess($groupId); 

        if (!$group) return redirect()->to("/group/$groupId")->with('error', 'Unauthorized');

        $db = \Config\Database::connect();
        $user = $db->table('users')->where('id', $this->user_id)->get()->getRowArray();
        $avatarUrl = $user['avatar'] ?? base_url('ICON/avatar-default.png');

        return view('group/settings', [
            'user_id' => $this->user_id,
            'avatarUrl' => $avatarUrl,
            'group' => $group
        ]);
    }

    public function update($groupId) {
        $group = $this->checkAccess($groupId);
        if (!$group) return $this->response->setJSON(['status' => 'error', 'message' => 'Unauthorized']);

        $data = [];
        if ($name = $this->request->getPost('name')) $data['name'] = $name;
        if ($description = $this->request->getPost('description')) $data['description'] = $description;

        // Handle avatar upload
        $avatar = $this->request->getFile('avatar');
        if ($avatar && $avatar->isValid() && !$avatar->hasMoved()) {
            try {
                $cloudinary = new \App\Libraries\Cloudinary();
                $avatarUrl = $cloudinary->upload($avatar->getTempName(), 'group_avatars');
                $data['avatar'] = $avatarUrl;
            } catch (\Exception $e) {
                log_message('error', 'Group avatar upload failed: ' . $e->getMessage());
            }
        }

        // Handle cover photo upload
        $cover = $this->request->getFile('cover_photo');
        if ($cover && $cover->isValid() && !$cover->hasMoved()) {
            try {
                $cloudinary = new \App\Libraries\Cloudinary();
                $coverUrl = $cloudinary->upload($cover->getTempName(), 'group_covers');
                $data['cover_photo'] = $coverUrl;
            } catch (\Exception $e) {
                log_message('error', 'Group cover photo upload failed: ' . $e->getMessage());
            }
        }

        if (empty($data)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'No data to update']);
        }

        return $this->group->update($groupId, $data) 
            ? $this->response->setJSON(['status' => 'success']) 
            : $this->response->setJSON(['status' => 'error', 'message' => 'Update failed']);
    }

    public function updatePrivacy($groupId) {
        $group = $this->checkAccess($groupId, ['owner']); // Only owner usually calls this?
        // Let's stick to checkAccess which allows admin too if strict not needed, or refine.
        // Usually privacy change is sensitive, maybe owner only.
        if (!$group || $group['owner_id'] !== $this->user_id) return $this->response->setJSON(['status' => 'error', 'message' => 'Unauthorized']);

        $privacy = $this->request->getPost('privacy');
        return $this->group->changePrivacy($groupId, $privacy)
            ? $this->response->setJSON(['status' => 'success'])
            : $this->response->setJSON(['status' => 'error']);
    }

    public function updatePostRequirements($groupId) {
        $group = $this->checkAccess($groupId);
        if (!$group) return $this->response->setJSON(['status' => 'error', 'message' => 'Unauthorized']);

        $requireApproval = $this->request->getPost('require_approval');
        return $this->group->update($groupId, ['post_requirement' => ($requireApproval === 'true' || $requireApproval === '1') ? 1 : 0])
             ? $this->response->setJSON(['status' => 'success'])
             : $this->response->setJSON(['status' => 'error']);
    }

    public function delete($groupId) {
        $group = $this->checkAccess($groupId, ['owner']); // STRICTLY OWNER
        if (!$group || $group['owner_id'] !== $this->user_id) return $this->response->setJSON(['status' => 'error', 'message' => 'Unauthorized']);

        $password = $this->request->getPost('password');
        $db = \Config\Database::connect();
        $user = $db->table('users')->where('id', $this->user_id)->get()->getRowArray();

        if (!$user || !password_verify($password, $user['password'])) {
             return $this->response->setJSON(['status' => 'error', 'message' => 'Incorrect password']);
        }

        return $this->group->deleteGroup($groupId)
            ? $this->response->setJSON(['status' => 'success'])
            : $this->response->setJSON(['status' => 'error']);
    }

    public function setRequire($groupId) {
        if (!$this->checkAccess($groupId)) return $this->response->setJSON(['status' => 'error', 'message' => 'Unauthorized']);
        
        $questions = $this->request->getPost('questions');
        (new Requirement())->setConfig($groupId, $questions);
        return $this->response->setJSON(['status' => 'success']);
    }

    public function kickMember($groupId, $userId) {
        if (!$this->checkAccess($groupId)) return $this->response->setJSON(['status' => 'error', 'message' => 'Unauthorized']);
        
        $group = $this->group->find($groupId);
        if ($group['owner_id'] == $userId) return $this->response->setJSON(['status' => 'error', 'message' => 'Cannot kick owner']);

        (new GroupMember())->kick($groupId, $userId);
        return $this->response->setJSON(['status' => 'success']);
    }

    public function promote($groupId, $userId) {
        $group = $this->checkAccess($groupId, ['owner']); // Only owner can promote to admin usually?
        if (!$group || $group['owner_id'] !== $this->user_id) return $this->response->setJSON(['status' => 'error', 'message' => 'Unauthorized']);

        (new GroupMember())->promote($groupId, $userId);
        return $this->response->setJSON(['status' => 'success']);
    }

    public function approvePost($postId) {
        $db = \Config\Database::connect();
        $groupPost = $db->table('group_posts')->where('post_id', $postId)->get()->getRowArray();
        if (!$groupPost) return $this->response->setJSON(['status' => 'error']);

        if (!$this->checkAccess($groupPost['group_id'])) return $this->response->setJSON(['status' => 'error', 'message' => 'Unauthorized']);
        
        $db->table('group_posts')->where('post_id', $postId)->update(['status' => 'accepted']);
        
        // Notify Author
        $post = (new Post())->find($postId);
        if ($post) {
            \App\Models\Notification::createNotification('post_approved', $this->user_id, $post['id_user'], $groupPost['group_id']); 
        }

        return $this->response->setJSON(['status' => 'success']);
    }

    public function rejectPost($postId) {
        $db = \Config\Database::connect();
        $groupPost = $db->table('group_posts')->where('post_id', $postId)->get()->getRowArray();
        if (!$groupPost) return $this->response->setJSON(['status' => 'error']);
        
        if (!$this->checkAccess($groupPost['group_id'])) return $this->response->setJSON(['status' => 'error', 'message' => 'Unauthorized']);

        $db->table('group_posts')->where('post_id', $postId)->delete();
        (new Post())->deleteWithMedia($postId);
        
        // Notify Author? (Optional, maybe "Your post was declined")
        
        return $this->response->setJSON(['status' => 'success']);
    }

    public function approveMember($groupId, $userId) {
        if (!$this->checkAccess($groupId)) return $this->response->setJSON(['status' => 'error', 'message' => 'Unauthorized']);
        
        $memberModel = new GroupMember();
        $memberModel->where(['group_id' => $groupId, 'user_id' => $userId])
                    ->set(['role' => 'member'])
                    ->update();
                    
        // Notify User
        \App\Models\Notification::createNotification('group_accept', $this->user_id, $userId, $groupId);
        
        return $this->response->setJSON(['status' => 'success']);
    }

    public function rejectMember($groupId, $userId) {
        if (!$this->checkAccess($groupId)) return $this->response->setJSON(['status' => 'error', 'message' => 'Unauthorized']);
        
        $memberModel = new GroupMember();
        $memberModel->where(['group_id' => $groupId, 'user_id' => $userId])->delete();
        
        return $this->response->setJSON(['status' => 'success']);
    }

    private function renderPage($groupId, $page) {
        $group = $this->checkAccess($groupId);
        if (!$group) return redirect()->to("/group/$groupId");

        $db = \Config\Database::connect();
        $user = $db->table('users')->where('id', $this->user_id)->get()->getRowArray();
        $avatarUrl = $user['avatar'] ?? base_url('ICON/avatar-default.png');
        
        // Enrich group with stats
        $stats = $this->group->getStats($groupId);
        $group = array_merge($group, $stats);

        $data = [
            'user_id' => $this->user_id,
            'avatarUrl' => $avatarUrl,
            'group' => $group,
            'active_page' => $page
        ];

        if ($page === 'posts') {
            $postModel = new Post();
            $pendingPosts = $db->table('group_posts')
                ->select('posts.*, profiles.firstname, profiles.lastname, users.avatar')
                ->join('posts', 'posts.id = group_posts.post_id')
                ->join('users', 'users.id = posts.id_user')
                ->join('profiles', 'profiles.id = users.id')
                ->where('group_posts.group_id', $groupId)
                ->where('group_posts.status', 'pending')
                ->orderBy('posts.created_at', 'DESC')
                ->get()->getResultArray();
            $data['pending_posts'] = $postModel->attachMediaToPosts($pendingPosts);
        }

        if ($page === 'members') {
             $data['members'] = $db->table('group_members')
                ->select('group_members.*, users.avatar, profiles.firstname, profiles.lastname')
                ->join('users', 'users.id = group_members.user_id')
                ->join('profiles', 'profiles.id = group_members.user_id')
                ->where('group_members.group_id', $groupId)
                ->where('group_members.role !=', 'pending')
                ->orderBy('group_members.role', 'ASC')
                ->get()->getResultArray();
                
            $data['requests'] = $db->table('group_members')
                ->select('group_members.*, users.avatar, profiles.firstname, profiles.lastname')
                ->join('users', 'users.id = group_members.user_id')
                ->join('profiles', 'profiles.id = group_members.user_id')
                ->where('group_members.group_id', $groupId)
                ->where('group_members.role', 'pending')
                ->get()->getResultArray();
        }

        if ($page === 'requirements') {
             $data['questions'] = (new Requirement())->getConfig($groupId);
        }

        return view("group/manage/$page", $data);
    }
}
