<?php

namespace App\Controllers;

<<<<<<< HEAD
use App\Models\NotificationModel;
use App\Models\FriendshipModel;

class NotificationController extends BaseController
{
    public function getLatest()
    {
        if (!session()->get('logged_in')) return $this->response->setJSON([]);

        $userId = session()->get('user_id');
        $notifModel = new NotificationModel();

        $data = $notifModel->getNotifications($userId, 10);
        $unreadCount = $notifModel->where('user_id', $userId)->where('is_read', 0)->countAllResults();

        $friendModel = new FriendshipModel();
        $pendingRequestCount = $friendModel->where('receiver_id', $userId)
                                           ->where('status', 'pending')
                                           ->countAllResults();

        return $this->response->setJSON([
            'status' => 'success',
            'data'   => $data,
            'unread' => $unreadCount,
            'pending_friends' => $pendingRequestCount 
        ]);
    }

    public function markItemRead($id)
    {
        if (!session()->get('logged_in')) return;
        
        $userId = session()->get('user_id');
        $notifModel = new NotificationModel();
        
        $notifModel->where('user_id', $userId)
                   ->where('id', $id)
                   ->set(['is_read' => 1])
                   ->update();
        
        return $this->response->setJSON(['status' => 'success']);
    }
}
=======
use App\Models\Notification;

class NotificationController extends BaseController
{
    protected $notification;
    protected $user_id;

    public function __construct() {
        $this->notification = new Notification();
        $this->user_id = session()->get('user_id');
    }

    public function index() {
        if (!$this->user_id) return $this->response->setJSON(['status' => 'error']);

        $notifications = $this->notification
            ->where('receiver_id', $this->user_id)
            ->orderBy('created_at', 'DESC')
            ->findAll(20); // Limit 20

        // Enrich with user data if needed (requester info)
        $db = \Config\Database::connect();
        $enriched = [];
        
        foreach ($notifications as $n) {
            $sender = $db->table('users')
                ->select('users.avatar, profiles.firstname, profiles.lastname')
                ->join('profiles', 'profiles.id = users.id')
                ->where('users.id', $n['requester_id'])
                ->get()->getRowArray();
                
            $n['sender_name'] = $sender ? $sender['firstname'] . ' ' . $sender['lastname'] : 'Unknown';
            $n['sender_avatar'] = $sender['avatar'] ?? base_url('ICON/avatar-default.svg');
            
            // Format time (simplified, ideally usage relative time helper)
            $n['time_ago'] = $this->timeAgo($n['created_at']);
            
            $enriched[] = $n;
        }

        return $this->response->setJSON(['status' => 'success', 'data' => $enriched]);
    }

    public function markRead($id) {
        if (!$this->user_id) return $this->response->setJSON(['status' => 'error']);
        
        $this->notification->update($id, ['status' => 'read']);
        return $this->response->setJSON(['status' => 'success']);
    }

    public function markAllRead() {
        if (!$this->user_id) return $this->response->setJSON(['status' => 'error']);

        $this->notification->where('receiver_id', $this->user_id)
                           ->set(['status' => 'read'])
                           ->update();
                           
        return $this->response->setJSON(['status' => 'success']);
    }

    private function timeAgo($datetime) {
        $time = strtotime($datetime);
        $current = time();
        $diff = $current - $time;
        
        if ($diff < 60) return 'Just now';
        if ($diff < 3600) return floor($diff / 60) . 'm ago';
        if ($diff < 86400) return floor($diff / 3600) . 'h ago';
        return date('M d', $time);
    }
}
>>>>>>> admin
