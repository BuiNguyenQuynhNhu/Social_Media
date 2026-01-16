<?php

namespace App\Models;

use CodeIgniter\Model;
use Ramsey\Uuid\Uuid;

class Notification extends Model
{
    // ... (rest of props)

    protected function generateUUID()
    {
        return Uuid::uuid4()->toString();
    }
    // ... properties ...
    protected $table            = 'notifications';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = false;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['id', 'requester_id', 'receiver_id', 'status', 'context', 'created_at', 'updated_at'];

    // Dates
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Get generic notifications for a user with sender details
     */
    public function getForUser($userId, $limit = 10)
    {
        // 1. Get raw notifications for the receiver (No Joins to ensure we get EVERYTHING)
        $builder = $this->builder();
        $builder->select('*');
        $builder->where('receiver_id', $userId);
        $builder->orderBy('created_at', 'DESC');
        if ($limit > 0) {
            $builder->limit($limit);
        }

        $results = $builder->get()->getResultArray();
        $formatted = [];
        
        $db = \Config\Database::connect();

        foreach ($results as $row) {
            // 2. Manual Enrichment to be safe
            $senderName = 'System Admin';
            $avatar = base_url('ICON/avatar-default.svg');

            if (!empty($row['requester_id'])) {
                // Try to find the user/profile
                $sender = $db->table('users')
                    ->select('users.avatar, profiles.firstname, profiles.lastname')
                    ->join('profiles', 'profiles.id = users.id', 'left')
                    ->where('users.id', $row['requester_id'])
                    ->get()->getRowArray();

                if ($sender) {
                   if (!empty($sender['firstname'])) {
                       $senderName = $sender['firstname'] . ' ' . $sender['lastname'];
                   }
                   if (!empty($sender['avatar'])) {
                       $avatar = (strpos($sender['avatar'], 'http') === 0) ? $sender['avatar'] : base_url($sender['avatar']);
                   }
                }
            }

            $row['sender_name'] = $senderName;
            $row['sender_avatar'] = $avatar;
            $row['time_ago'] = $this->timeAgo($row['created_at']);
            
            $formatted[] = $row;
        }

        return $formatted;
    }

    /**
     * Mark a notification as responded/read
     */
    public function markAsResponded($id)
    {
        return $this->update($id, ['status' => 'responded']);
    }

    /**
     * Update notification status
     */
    public function updateStatus($id, $status)
    {
        return $this->update($id, ['status' => $status]);
    }

    private function timeAgo($datetime)
    {
        $time = strtotime($datetime);
        $current = time();
        $diff = $current - $time;

        if ($diff < 60) return 'Just now';
        if ($diff < 3600) return floor($diff / 60) . 'm ago';
        if ($diff < 86400) return floor($diff / 3600) . 'h ago';
        return date('M d', $time);
    }

    /**
     * Create a new notification with standardized format
     */
    public static function createNotification($type, $requesterId, $receiverId, $contextData = [])
    {
        $model = new self();
        $context = array_merge(['type' => $type], $contextData);
        
        // Default to 'pending' as 'unread' does not exist in the enum.
        // Both actionable and informational start as 'pending' to be visible.
        $status = 'pending'; 
        
        $data = [
            'id' => Uuid::uuid4()->toString(),
            'requester_id' => $requesterId,
            'receiver_id' => $receiverId,
            'status' => $status,
            'context' => json_encode($context),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];
        
        if ($model->insert($data)) {
            return $data['id'];
        }
        return false;
    }

}
