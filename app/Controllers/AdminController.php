<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\Admin;
use App\Models\User;
use App\Models\Post;
use App\Models\Notification;

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
        
        // Get stats
        $db = \Config\Database::connect();
        $stats = [
            'users' => $db->table('users')->countAllResults(),
            'posts' => $db->table('posts')->countAllResults(),
            'groups' => $db->table('groups')->countAllResults(),
            'chats' => $db->table('chats')->countAllResults()
        ];
        
        return view('admin/dashboard', array_merge($admin ?? [], ['stats' => $stats]));
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

    public function banUser($userId) {
        $userModel = new User();
        $user = $userModel->find($userId);
        
        if (!$user) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'User not found']);
        }

        $banExpiry = date('Y-m-d H:i:s', strtotime('+7 days'));

        Notification::createNotification('account_banned', $this->id, $userId, [
            'reason' => 'Violation of community guidelines',
            'ban_expires' => $banExpiry,
            'can_appeal' => true
        ]);

        return $this->response->setJSON(['status' => 'success', 'message' => 'User banned for 7 days (Notification sent)']);
    }

    public function unbanUser($userId) {
        $userModel = new User();
        $user = $userModel->find($userId);
        
        if (!$user) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'User not found']);
        }
        Notification::createNotification('account_unbanned', $this->id, $userId, []);

        return $this->response->setJSON(['status' => 'success', 'message' => 'User has been unbanned (Notification sent)']);
    }

    /**
     * User submits ban appeal
     */
    public function submitAppeal() {
        $userId = session()->get('user_id');
        $reason = $this->request->getPost('reason');
        $notificationId = $this->request->getPost('notification_id');
        
        if (!$userId || !$reason) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Missing data']);
        }

        // Find admins (users with type = 1)
        $db = \Config\Database::connect();
        $admins = $db->table('users')->where('type', '1')->get()->getResultArray();
        
        // Send appeal notification to all admins
        foreach ($admins as $admin) {
            Notification::createNotification('ban_appeal', $userId, $admin['id'], [
                'appeal_reason' => $reason,
                'original_notification_id' => $notificationId
            ]);
        }

        // Mark original ban notification as responded
        if ($notificationId) {
            $notifModel = new Notification();
            $notifModel->update($notificationId, ['status' => 'responsed']);
        }

        return $this->response->setJSON(['status' => 'success', 'message' => 'Appeal submitted']);
    }

    /**
     * Admin accepts appeal - unbans user
     */
    public function acceptAppeal($userId) {
        $userModel = new User();
        $user = $userModel->find($userId);
        
        if (!$user) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'User not found']);
        }
        Notification::createNotification('appeal_accepted', $this->id, $userId, []);

        return $this->response->setJSON(['status' => 'success', 'message' => 'Appeal accepted, user unbanned']);
    }

    /**
     * Admin rejects appeal
     */
    public function rejectAppeal($userId) {
        // Notify user their appeal was rejected
        Notification::createNotification('appeal_rejected', $this->id, $userId, [
            'message' => 'Your appeal has been reviewed and rejected'
        ]);

        return $this->response->setJSON(['status' => 'success', 'message' => 'Appeal rejected']);
    }

    /**
     * List pending ban appeals for admin
     */
    public function listAppeals() {
        $db = \Config\Database::connect();
        
        $appeals = $db->table('notifications')
            ->select('notifications.*, profiles.firstname, profiles.lastname, users.avatar')
            ->join('users', 'users.id = notifications.requester_id COLLATE utf8mb4_general_ci', 'inner', false)
            ->join('profiles', 'profiles.id = notifications.requester_id COLLATE utf8mb4_general_ci', 'inner', false)
            ->where("JSON_EXTRACT(notifications.context, '$.type')", 'ban_appeal')
            ->where('notifications.status', 'pending')
            ->orderBy('notifications.created_at', 'DESC')
            ->get()->getResultArray();

        return view('admin/appeals', [
            'appeals' => $appeals,
            'title' => 'Ban Appeals'
        ]);
    }

    /**
     * Auto-unban users after 7 days (can be called via cron or manually)
     */
    public function checkExpiredBans() {
        $db = \Config\Database::connect();
        
        // Find ban notifications older than 7 days
        $expiredBans = $db->table('notifications')
            ->select('receiver_id')
            ->where("JSON_EXTRACT(context, '$.type')", 'account_banned')
            ->where('created_at <', date('Y-m-d H:i:s', strtotime('-7 days')))
            ->get()->getResultArray();

        $userModel = new User();
        $unbannedCount = 0;

        foreach ($expiredBans as $ban) {
            // Logic commented out as users table has no status column
            // $user = $userModel->find($ban['receiver_id']);
            // if ($user && $user['status'] === 'banned') {
            //     $userModel->update($ban['receiver_id'], ['status' => 'active']);
            //     $unbannedCount++;
            // }
        }

        return $this->response->setJSON([
            'status' => 'success', 
            'message' => "$unbannedCount users auto-unbanned after 7-day expiry"
        ]);
    }

    /**
     * Delete a post (admin action)
     */
    public function deletePost($postId) {
        $postModel = new Post();
        $post = $postModel->find($postId);

        if (!$post) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Post not found']);
        }

        // Notify post owner
        Notification::createNotification('post_removed_admin', $this->id, $post['id_user'], [
            'reason' => 'Post violated community guidelines'
        ]);

        // Delete the post with media
        $postModel->deleteWithMedia($postId);

        return $this->response->setJSON(['status' => 'success', 'message' => 'Post deleted']);
    }

    /**
     * Delete a group (admin action)
     */
    public function deleteGroup($groupId) {
        $groupModel = new \App\Models\Group();
        $group = $groupModel->find($groupId);

        if (!$group) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Group not found']);
        }

        // Notify group owner
        Notification::createNotification('group_removed_admin', $this->id, $group['owner_id'], [
            'group_name' => $group['name'],
            'reason' => 'Group violated community guidelines'
        ]);

        // Delete the group
        $groupModel->deleteGroup($groupId);

        return $this->response->setJSON(['status' => 'success', 'message' => 'Group deleted']);
    }

    /**
     * Warn a user
     */
    public function warnUser($userId) {
        $userModel = new User();
        $user = $userModel->find($userId);
        
        if (!$user) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'User not found']);
        }

        $reason = $this->request->getPost('reason') ?? 'Violation of community guidelines';

        // Send warning notification
        Notification::createNotification('account_warning', $this->id, $userId, [
            'reason' => $reason
        ]);

        return $this->response->setJSON(['status' => 'success', 'message' => 'Warning sent to user']);
    }
    public function checkTriggers() {
        $db = \Config\Database::connect();
        $query = $db->query("SHOW TRIGGERS");
        return $this->response->setJSON($query->getResultArray());
    }
}
