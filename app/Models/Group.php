<?php

namespace App\Models;

use CodeIgniter\Model;
use Ramsey\Uuid\Uuid;

class Group extends Model
{
    protected $table            = 'groups';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = false;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['id', 'name', 'description', 'post_requirement', 'privacy', 'cover_photo', 'avatar', 'created_at', 'owner_id'];

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

    protected function generateUUID() {
        return Uuid::uuid4()->toString();
    }

    public function create($data) {
        $data['id'] = $this->generateUUID();
        $data['created_at'] = date('Y-m-d H:i:s');
        
        if ($this->insert($data)) {
            return $data['id'];
        }
        return false;
    }

    public function changeOwner($group_id, $nOwner_id){
        return $this->update($group_id, ['owner_id' => $nOwner_id]);
    }
    public function changePrivacy($groupId, $privacy) {
        return $this->update($groupId, ['privacy' => $privacy]);
    }

    public function getInfo($id) {
        return $this->select('groups.*, COUNT(group_members.user_id) as member_count')
                    ->join('group_members', 'group_members.group_id = groups.id', 'left')
                    ->where('chats.id', $id)
                    ->groupBy('chats.id')
                    ->first();
    }
}
