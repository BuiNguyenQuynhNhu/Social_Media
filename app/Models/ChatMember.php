<?php

namespace App\Models;

use CodeIgniter\Model;

class ChatMember extends Model
{
    protected $table            = 'chat_members';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['chat_id', 'user_id', 'nickname', 'role', 'joined_at'];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];
    protected array $castHandlers = [];

    // Dates
    protected $useTimestamps = false;
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

    public function promote($id, $userId) {
        return $this->where(['chat_id' => $id, 'user_id' => $userId])
                    ->set(['role' => true])
                    ->update();
    }

    public function add($id, $userId, $role=false){
        return $this->insert([
            'chat_id' => $id,
            'user_id' => $userId,
            'role' => $role,
            'joined_at' => date('Y-m-d H:i:s'),
        ]);
    }

    public function isMember($chatId, $userId) {
        return $this->where(['chat_id' => $chatId, 'user_id' => $userId])->countAllResults() > 0;
    }

    public function getMembers($chatId) {
        return $this->select('chat_members.*, profiles.firstname, profiles.lastname, users.avatar')
                    ->join('users', 'users.id = chat_members.user_id')
                    ->join('profiles', 'profiles.id = users.id')
                    ->where('chat_id', $chatId)
                    ->findAll();
    }

    public function setNickname($id, $userId, $nickname){
        return $this->where(['chat_id' => $id, 'user_id' => $userId,])
                            ->set(['nickname' => $nickname])
                            ->update();
    }

    public function downPromote($id, $userId){
        return $this->where(['chat_id' => $id, 'user_id' => $userId])
                    ->set(['role' => false])
                    ->update();
    }

    public function deleteMember($id, $userID){
        return $this->where(['chat_id' => $id, 'user_id' => $userID])
                    ->delete();
    }
}
