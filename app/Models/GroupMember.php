<?php

namespace App\Models;

use CodeIgniter\Model;

class GroupMember extends Model
{
    protected $table            = 'group_members';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = false;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['id', 'group_id', 'user_id', 'role', 'joined_at'];

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

    public function promote($groupId, $userId) {
        // SQL Role Update
        $this->where(['group_id' => $groupId, 'user_id' => $userId])
             ->set(['role' => 'admin']) // Simplified to admin for now, or pass role
             ->update();
             
        // Chat Role Update (Mongo)
        (new \App\Models\ChatMember())->where(['chat_id' => $groupId, 'user_id' => $userId])
            ->set(['role' => 'admin'])
            ->update();

        return true;
    }

    public function add($groupId, $userId, $role = 'member')
    {
        $data = [
            'id' => \Ramsey\Uuid\Uuid::uuid4()->toString(),
            'group_id' => $groupId,
            'user_id' => $userId,
            'role' => $role,
            'joined_at' => date('Y-m-d H:i:s')
        ];

        return $this->insert($data);
    }

    public function isMember($id, $userId) {
        return $this->where(['group_id' => $id, 'user_id' => $userId])->countAllResults() > 0;
    }

    public function getMembers($id) {
        return $this->select('group_members.*, profiles.firstname, profiles.lastname, users.avatar')
                    ->join('users', 'users.id = group_members.user_id')
                    ->join('profiles', 'profiles.id = users.id')
                    ->where('group_id', $id)
                    ->findAll();
    }

    public function downPromote($id, $userId){
        return $this->where(['group_id' => $id, 'user_id' => $userId])
                    ->set(['role' => false])
                    ->update();
    }

    public function kick($groupId, $userId) {
        // SQL Delete
        $this->where(['group_id' => $groupId, 'user_id' => $userId])->delete();
        
        // Mongo Chat Delete
        (new \App\Models\ChatMember())->where(['chat_id' => $groupId, 'user_id' => $userId])->delete();

        return true;
    }
}
