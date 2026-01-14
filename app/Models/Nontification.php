<?php

namespace App\Models;

use CodeIgniter\Model;

class Nontification extends Model
{
    protected $table            = 'nontifications';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = false;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['id', 'requester_id', 'receiver_id', 'status', 'context', 'created_at', 'updated_at'];

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
        $data['id'] = $this->generateUUID();
        $data['created_at'] = date('Y-m-d H:i:s');
    
        if ($this->insert($data)) {
            return $data['id'];
        }
        return false;
    }

    public function responsive($data){
        $data['id'] = $this->generateUUID();
        $data['updated_at'] = date('Y-m-d H:i:s');
        $data['status'] = 'responsed';
        if ($this->insert($data)) {
            return $data['id'];
        }
        return false;
    }
    
    public function updated($data){
        $data['updated_at'] = date('Y-m-d H:i:s');
        $data['status'] = 'updated';
        if ($this->insert($data)) {
            return $data['id'];
        }
        return false;
    }

    public function close($data){
        //is other 2 status which do this or do not do this
        $data['updated_at'] = date('Y-m-d H:i:s');
        if ($this->insert($data)) {
            return $data['id'];
        }
        return false;
    }

}
