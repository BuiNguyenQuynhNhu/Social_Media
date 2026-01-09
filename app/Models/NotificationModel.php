<?php

namespace App\Models;

use CodeIgniter\Model;

class NotificationModel extends Model
{
    protected $table            = 'notifications';
    protected $primaryKey       = 'id';
    protected $allowedFields    = ['user_id', 'actor_id', 'type', 'reference_id', 'is_read', 'created_at'];

    public function getNotifications($userId, $limit = 10)
    {
        return $this->select('notifications.*, profiles.firstname, profiles.lastname, users.avatar')
                    ->join('users', 'users.id = notifications.actor_id', 'left') 
                    ->join('profiles', 'profiles.id = users.id', 'left') 
                    ->where('notifications.user_id', $userId)
                    ->orderBy('notifications.created_at', 'DESC')
                    ->findAll($limit);
    }

    public function addNotification($userId, $actorId, $type, $refId = null)
    {
        if ($userId == $actorId) return; 

        if ($type == 'post_like') {
            $exists = $this->where([
                'user_id' => $userId, 
                'actor_id' => $actorId, 
                'type' => 'post_like', 
                'reference_id' => $refId
            ])->first();
            if ($exists) return; 
        }

        $this->insert([
            'user_id'      => $userId,
            'actor_id'     => $actorId,
            'type'         => $type,
            'reference_id' => $refId,
            'created_at'   => date('Y-m-d H:i:s')
        ]);
    }
}