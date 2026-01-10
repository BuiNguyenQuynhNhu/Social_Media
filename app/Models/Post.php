<?php

namespace App\Models;

use CodeIgniter\Model;
use Ramsey\Uuid\Uuid;
class Post extends Model
{
    protected $table            = 'posts';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = false;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['id', 'content', 'created_at', 'privacy', 'id_user'];

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


    //functions
    protected function generateUUID(){
        return Uuid::uuid4()->toString();
    }
    public function create($data){
        $data['id'] = $this->generateUUID();
        $data['created_at'] = date('Y-m-d H:i:s');
    
        if ($this->insert($data)) {
            return $data['id'];
        }
        return false;
    }

    public function getMedia(){

    }

    public function insertMedia($media){

    }

    public function getPostByUser($id){
        return $this->select('posts.*, profiles.firstname, profiles.lastname, users.avatar')
                    ->join('profiles', 'profiles.id = posts.id_user')
                    ->join('users', 'users.id = posts.id_user')
                    ->where('posts.id_user', $id)
                    ->orderBy('posts.created_at', 'DESC')
                    ->findAll();
    }

    public function countPostUser($id) {
        return $this->where('id_user', $id)->countAllResults();
    }

    public function list() {
        return $this->select('posts.*, profiles.firstname, profiles.lastname, users.avatar')
                    ->join('profiles', 'profiles.id= posts.id_user')
                    ->join('users', 'users.id = posts.id_user')
                    ->orderBy('posts.created_at', 'DESC')
                    ->findAll();
    }
}
