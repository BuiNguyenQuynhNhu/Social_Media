<?php

namespace App\Models;

use Ramsey\Uuid\Uuid;
use CodeIgniter\Model;

class Requirement extends Model
{
    protected $table            = 'requires';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = false;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['id', 'user_id', 'status', 'created_at'];

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

    public function create($id, $userId)
    {
        $requestId = Uuid::uuid4()->toString();
        $data = [
            'id'         => $requestId,
            'group_id'   => $id,
            'user_id'    => $userId,
            'status'     => 'pending',
            'created_at' => date('Y-m-d H:i:s')
        ];
        return $this->insert($data) ? $requestId : false;
    }

    public function setConfig($groupId, $questions)
    {
        $mongo = new \App\Libraries\MongoDB();
        $mongo->getCollection('requirement_configs')->replaceOne(
            ['_id' => $groupId],
            ['_id' => $groupId, 'questions' => $questions, 'updated_at' => date('Y-m-d H:i:s')],
            ['upsert' => true]
        );
        return true;
    }

    public function getConfig($groupId)
    {
        $mongo = new \App\Libraries\MongoDB();
        $config = $mongo->getCollection('requirement_configs')->findOne(['_id' => $groupId]);
        return $config['questions'] ?? [];
    }
}
