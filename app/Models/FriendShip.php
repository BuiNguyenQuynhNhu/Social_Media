<?php

namespace App\Models;

use Ramsey\Uuid\Uuid;
use CodeIgniter\Model;

class FriendShip extends Model
{
    protected $table            = 'friendships';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = false;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['id', 'receiver_id', 'requester_id', 'status', 'created_at', 'updated_at'];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];
    protected array $castHandlers = [];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    //function
    protected function generateUUID() {
        return Uuid::uuid4()->toString();
    }


    public function accept($shipId) {
        return $this->update($shipId, ['status' => 'accepted']);
    }



    public function create($currentUserId, $targetUserId) {
        // Check if friendship already exists in either direction
        $existing = $this->where('requester_id', $currentUserId)
                         ->where('receiver_id', $targetUserId)
                         ->first();
        
        if ($existing) {
            return false; // Already sent request to this user
        }
        
        // Check reverse direction (they sent request to you)
        $reverse = $this->where('requester_id', $targetUserId)
                        ->where('receiver_id', $currentUserId)
                        ->first();
        
        if ($reverse) {
            // If they sent you a request, you must accept/decline it
            // You cannot send a request back
            return false;
        }
        
        // Create new friend request
        $data = [
            'id'           => $this->generateUUID(),
            'requester_id' => $currentUserId,
            'receiver_id'  => $targetUserId,
            'status'       => 'pending'
        ];

        return $this->insert($data) ? $data['id'] : false;
    }

    public function listSend($id) {
        return $this->select('friendships.id as id_friend_ship, users.id, profiles.firstname, profiles.lastname, users.avatar, friendships.created_at, friendships.updated_at')
            ->join('users', 'users.id = friendships.receiver_id')
            ->join('profiles', 'profiles.id = users.id')
            ->where('friendships.requester_id', $id)
            ->where('friendships.status', 'pending')
            ->findAll();
    }
    public function listReceive($id) {
        return $this->select('friendships.id as id_friend_ship, users.id, profiles.firstname, profiles.lastname, users.avatar, friendships.created_at, friendships.updated_at')
            ->join('users', 'users.id = friendships.requester_id')
            ->join('profiles', 'profiles.id = users.id')
            ->where('friendships.receiver_id', $id)
            ->where('friendships.status', 'pending')
            ->findAll();
    }
    public function listFriend($id) {
        return $this->select('friendships.id as id_friend_ship, users.id, profiles.firstname, profiles.lastname, users.avatar')
            ->join('users', "users.id = (CASE WHEN friendships.requester_id = '$id' THEN friendships.receiver_id ELSE friendships.requester_id END)")
            ->join('profiles', 'profiles.id = users.id')
            ->where('friendships.status', 'accepted')
            ->groupStart()
                ->where('friendships.requester_id', $id)
                ->orWhere('friendships.receiver_id', $id)
            ->groupEnd()
            ->findAll();
    }
}
