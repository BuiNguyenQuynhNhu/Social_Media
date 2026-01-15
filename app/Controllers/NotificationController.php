<?php

namespace App\Controllers;

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

        try {
            $notifications = $this->notification
                ->where('receiver_id', $this->user_id)
                ->orderBy('created_at', 'DESC')
                ->findAll(20); // Limit 20

            // Enrich with user data if needed (requester info)
            $db = \Config\Database::connect();
            $enriched = [];
            
            foreach ($notifications as $n) {
                // ... (rest of the loop)
                $sender = $db->table('users')
                    ->select('users.avatar, profiles.firstname, profiles.lastname')
                    ->join('profiles', 'profiles.id = users.id')
                    ->where('users.id', $n['requester_id'])
                    ->get()->getRowArray();
                    
                $n['sender_name'] = $sender ? $sender['firstname'] . ' ' . $sender['lastname'] : 'Unknown';
                $n['sender_avatar'] = (isset($sender['avatar']) && strpos($sender['avatar'], 'http') === 0) 
                ? $sender['avatar'] 
                : (isset($sender['avatar']) ? base_url($sender['avatar']) : base_url('ICON/avatar-default.svg'));
                
                // Format time (simplified, ideally usage relative time helper)
                $n['time_ago'] = $this->timeAgo($n['created_at']);
                
                $enriched[] = $n;
            }

            return $this->response->setJSON(['status' => 'success', 'data' => $enriched]);
        } catch (\Exception $e) {
            return $this->response->setJSON(['status' => 'error', 'message' => $e->getMessage()]);
        }
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
