<?php

namespace App\Controllers;

use App\Models\Notification;

class NotificationController extends BaseController
{
    protected $notification;
    protected $user_id;

    public function __construct() {
        $this->notification = new Notification();
        $this->user_id = session()->get('user_id');
    }

    public function index() {
        if (!$this->user_id) return $this->response->setJSON(['status' => 'error']);

        try {
            // Use the Model's method which handles joining, avatar logic, and time formatting
            $data = $this->notification->getForUser($this->user_id);
            
            return $this->response->setJSON([
                'status' => 'success', 
                'data' => $data,
                'debug_user_id' => $this->user_id
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function markRead($id) {
        if (!$this->user_id) return $this->response->setJSON(['status' => 'error']);
        
        $action = $this->request->getPost('action');
        $status = 'responded'; // Default generic read state (corrected from responsed)
        
        if ($action === 'confirm' || $action === 'approve') {
            $status = 'approved';
        } elseif ($action === 'delete' || $action === 'reject') {
            $status = 'cancelled';
        } elseif ($action === 'read') {
            $status = 'responded';
        }
        
        $success = $this->notification->updateStatus($id, $status);
        
        return $this->response->setJSON([
            'status' => 'success',
            'updated' => $success,
            'new_status' => $status
        ]);
    }
}
