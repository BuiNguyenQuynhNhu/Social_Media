<?php

namespace App\Models;

use CodeIgniter\Model;
use Ramsey\Uuid\Uuid;

class Chat extends Model
{
    protected $table            = 'chats';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = false;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['id', 'name', 'owner_id', 'privacy', 'created_at'];

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

    public function create($data){
        $data['id'] = Uuid::uuid4()->toString();
        return $this->insert($data);
    }

    public function getInfo($id) {
        return $this->select('chats.*, COUNT(chat_members.user_id) as member_count')
                    ->join('chat_members', 'chat_members.chat_id = chats.id', 'left')
                    ->where('chats.id', $id)
                    ->groupBy('chats.id')
                    ->first();
    }

    public function changeOwner($id, $nOwnerId){
        return $this->update($id, ['owner_id' => $nOwnerId]);
    }

    public function toggle($id, $private){
        return $this->update($id, ['privacy' => (bool)$private]);
    }
}
