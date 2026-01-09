<?php

namespace App\Models;

use CodeIgniter\Model;

class FriendshipModel extends Model
{
    protected $table            = 'friendships';
    protected $primaryKey       = 'id';
    protected $allowedFields    = ['requester_id', 'receiver_id', 'status', 'created_at', 'updated_at'];
    protected $useTimestamps    = true;

    public function checkFriendshipStatus($user1_id, $user2_id)
    {
        $friendship = $this->groupStart()
                                ->where('requester_id', $user1_id)
                                ->where('receiver_id', $user2_id)
                            ->groupEnd()
                            ->orGroupStart()
                                ->where('requester_id', $user2_id)
                                ->where('receiver_id', $user1_id)
                            ->groupEnd()
                            ->first();

        if (!$friendship) {
            return 'not_friends';
        }

        if ($friendship['status'] === 'accepted') {
            return 'friends';
        }

        if ($friendship['status'] === 'pending') {
            return ($friendship['requester_id'] == $user1_id) ? 'request_sent' : 'request_received';
        }

        return 'blocked';
    }

    public function getIncomingRequests($userId)
    {
        return $this->select('friendships.id as friendship_id, users.id as user_id, profiles.firstname, profiles.lastname, users.avatar')
                    ->join('users', 'users.id = friendships.requester_id')
                    ->join('profiles', 'profiles.id = users.id')
                    ->where('friendships.receiver_id', $userId)
                    ->where('friendships.status', 'pending')
                    ->findAll();
    }

    public function getAllFriends($userId)
    {
        $db = \Config\Database::connect();
        
        $sql = "
            SELECT users.id as user_id, profiles.firstname, profiles.lastname, users.avatar 
            FROM friendships
            JOIN users ON (
                (friendships.requester_id = users.id AND friendships.receiver_id = ?) 
                OR 
                (friendships.receiver_id = users.id AND friendships.requester_id = ?)
            )
            JOIN profiles ON profiles.id = users.id  
            WHERE friendships.status = 'accepted'
        ";
        
        return $db->query($sql, [$userId, $userId])->getResultArray();
    }
    public function countFriends($userId)
    {
        return $this->db->table('friendships')
            ->where('status', 'accepted')
            ->groupStart()
                ->where('requester_id', $userId)
                ->orWhere('receiver_id', $userId)
            ->groupEnd()
            ->countAllResults();
    }

}