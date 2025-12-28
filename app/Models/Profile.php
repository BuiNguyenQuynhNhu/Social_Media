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
    protected $createdField  = '';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [
        'id' => 'required',
        'firstname' => 'required',
        'lastname' => 'required',
        'birth' => 'required',
        'background' => 'permit_empty',
    ];
    protected $validationMessages   = [
        'id' => [
            'required' => 'Please fill the email',
        ],
        'firstname' => [
            'required' => 'Please fill your first name',
        ],
        'lastname' => [
            'required' => 'Please fill yout last name',
        ]
    ];
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
