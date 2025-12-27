<?php

namespace App\Models;

use CodeIgniter\Model;

class Profile extends Model
{
    protected $table            = 'profiles';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = false;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['id', 'firstname', 'lastname', 'gender', 'birth', 'background'];

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
    protected $validationRules      = [
        'id' => 'required|max_length[36]',
        'firstname' => 'required|min_length[1]',
        'lastname' => 'required|min_length[1]',
        'birth' => 'required',
        'background' => 'permit_empty|ext_in[avatar, gif, apng]|max_size[avatar,2048]'
    ];
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
    public function getProfile($id){
        return $this->select('profiles.*, users.email, users.avatar')
                    ->join('users', 'users.id = profiles.id')
                    ->where('profiles.id', $id)
                    ->first();
    }
}
