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

    public function createGroup($data, $userId) {
        $db = \Config\Database::connect();
        $db->transStart();

        $groupId = $this->generateUUID();
        $data['id'] = $groupId;
        $data['created_at'] = date('Y-m-d H:i:s');
        
        $this->insert($data);

        // Add creator as Admin
        (new \App\Models\GroupMember())->add($groupId, $userId, 'admin');

        // Create Chat
        $isPrivate = ($data['privacy'] === 'private');
        (new \App\Models\Chat())->insert([
            'id' => $groupId, 
            'name' => $data['name'], 
            'owner_id' => $userId,
            'privacy' => $isPrivate ? 1 : 0
        ]);
        (new \App\Models\ChatMember())->add($groupId, $userId, 'admin');

        $db->transComplete();

        return $db->transStatus() ? $groupId : false;
    }

    public function deleteGroup($groupId) {
        $db = \Config\Database::connect();
        $db->transStart();

        $this->delete($groupId);
        // Delete Members
        $db->table('group_members')->where('group_id', $groupId)->delete();
        // Delete Group Posts associations (posts themselves might remain or soft delete, depends on policy)
        $db->table('group_posts')->where('group_id', $groupId)->delete();
        
        // Delete Chat
        (new \App\Models\Chat())->delete($groupId);
        (new \App\Models\ChatMember())->where('chat_id', $groupId)->delete();

        $db->transComplete();
        return $db->transStatus();
    }

    public function changeOwner($group_id, $nOwner_id){
        return $this->update($group_id, ['owner_id' => $nOwner_id]);
    }
    public function changePrivacy($groupId, $privacy) {
        return $this->update($groupId, ['privacy' => $privacy]);
    }

    public function getStats($groupId) {
        $db = \Config\Database::connect();
        return [
            'member_count' => $db->table('group_members')->where('group_id', $groupId)->countAllResults(),
            'pending_posts_count' => $db->table('group_posts')->where(['group_id' => $groupId, 'status' => 'pending'])->countAllResults(),
            // Add more stats as needed
        ];
    }
}
