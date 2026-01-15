<?php

namespace App\Models;

use CodeIgniter\Model;
use Ramsey\Uuid\Uuid;
use App\Models\Profile;
class User extends Model
{
    protected $table            = 'users';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = false;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['id', 'email','password', 'avatar', 'created_at'];

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
        'id' => 'required',
        'email' => 'required|valid_email|is_unique[users.email,id,{id}]',
        'password' => 'required|min_length[8]',
        'avatar' => 'permit_empty',
    ];
    protected $validationMessages   = [
        'email' => [
            'required' => 'Email is required',
            'valid_email' => 'Email formatting is not correct',
            'is_unique' => 'This email already exists',
        ],
        'password' => [
            'min_length' => 'Min length of password is 8 character',
        ],
    ];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = ['hashPassword'];
    protected $afterInsert    = [];
    protected $beforeUpdate   = ['hashPassword'];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];


    //function
    protected function hashPassword(array $data){
        if (isset($data['data']['password'])){
            $data['data']['password'] = password_hash($data['data']['password'], PASSWORD_DEFAULT);
        }
        return $data;
    }
    protected function generateUUID(){
        return Uuid::uuid4()->toString();
    }
    protected function processAvatar(array $data){
        
    }
    public function createAccount(array $user, array $profile){
        $db = \Config\Database::connect();
        $uuid = $this->generateUUID();
        $profileModel = new Profile;
        $db->transStart();
            $user['id'] = $uuid;
            $profile['id'] = $uuid;
        if ($this->insert($user) === false || $profileModel->insert($profile) === false) {
                        $db->transRollback();
                        return ['status' => false, 'message' => 'Registration failed (Database Error)'];
            }
        $db->transComplete();
        return ['status' => true, 'id' => $uuid];
    }

    public function getEmail($email){
        return $this->where('email', $email)->first();
    }

    public function updatePassword($id, $nPassword){
        return $this->save([
            'id' => $id,
            'password' => $nPassword,
        ]);
    }

    public function updateAvatar($id, $file){
        return $this->update($id, ['avatar' => $file]);
    }


    public function searchUsers($keyword)
    {
        if (empty($keyword)) return [];

        $builder = $this->select('users.id, users.email, users.avatar, profiles.firstname, profiles.lastname') 
                        ->join('profiles', 'profiles.id = users.id', 'left') 
                        ->groupStart()
                            ->like('users.email', $keyword)
                            ->orLike('profiles.firstname', $keyword) 
                            ->orLike('profiles.lastname', $keyword)  
                            ->orLike("CONCAT(COALESCE(profiles.firstname,''), ' ', COALESCE(profiles.lastname,''))", $keyword) 
                        ->groupEnd();

        $result = $builder->findAll();
        return $result;
    }

    public function list(){
        return $this->findAll();
    }
}
