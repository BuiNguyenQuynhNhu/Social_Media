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
    protected $allowedFields    = ['id', 'name', 'owner_id', 'privacy', 'avatar', 'created_at'];

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
        if ($this->insert($data)) {
            return $data['id'];
        }
        return false;
    }

    public function getPrivateChatId($user1, $user2) {
        $db = \Config\Database::connect();
        $builder = $db->table('chats');
        $builder->select('chats.id');
        $builder->join('chat_members as m1', 'm1.chat_id = chats.id');
        $builder->join('chat_members as m2', 'm2.chat_id = chats.id');
        $builder->where('chats.privacy', 0); // 0 = Private
        $builder->where('m1.user_id', $user1);
        $builder->where('m2.user_id', $user2);
        
        $row = $builder->get()->getRow();
        return $row ? $row->id : null;
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
