<?php

namespace App\Models;

use CodeIgniter\Model;
use Ramsey\Uuid\Uuid;

class Notification extends Model
{
    // ... (rest of props)

    protected function generateUUID()
    {
        return Uuid::uuid4()->toString();
    }
    protected $table            = 'notifications';
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

    /**
     * Create a new notification with standardized format
     * @param string $type Type of notification (friend_request, group_invite, etc.)
     * @param string $requesterId User ID who triggered the notification  
     * @param string $receiverId User ID who should receive the notification
     * @param array $contextData Additional context data (group_id, group_name, etc.)
     * @return string|false Notification ID or false on failure
     */
    public static function createNotification($type, $requesterId, $receiverId, $contextData = [])
    {
        $model = new self();
        
        $context = array_merge(['type' => $type], $contextData);
        
        $data = [
            'id' => $model->generateUUID(),
            'requester_id' => $requesterId,
            'receiver_id' => $receiverId,
            'status' => 'unread',
            'context' => json_encode($context),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];
        
        if ($model->insert($data)) {
            return $data['id'];
        }
        
        return false;
    }

}
