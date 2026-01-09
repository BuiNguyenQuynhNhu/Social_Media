<?php

namespace App\Controllers;

use App\Models\NotificationModel;
use App\Models\FriendshipModel;

class NotificationController extends BaseController
{
    public function getLatest()
    {
        if (!session()->get('logged_in')) return $this->response->setJSON([]);

        $userId = session()->get('user_id');
        $notifModel = new NotificationModel();

        $data = $notifModel->getNotifications($userId, 10);
        $unreadCount = $notifModel->where('user_id', $userId)->where('is_read', 0)->countAllResults();

        $friendModel = new FriendshipModel();
        $pendingRequestCount = $friendModel->where('receiver_id', $userId)
                                           ->where('status', 'pending')
                                           ->countAllResults();

        return $this->response->setJSON([
            'status' => 'success',
            'data'   => $data,
            'unread' => $unreadCount,
            'pending_friends' => $pendingRequestCount 
        ]);
    }

    public function markItemRead($id)
    {
        if (!session()->get('logged_in')) return;
        
        $userId = session()->get('user_id');
        $notifModel = new NotificationModel();
        
        $notifModel->where('user_id', $userId)
                   ->where('id', $id)
                   ->set(['is_read' => 1])
                   ->update();
        
        return $this->response->setJSON(['status' => 'success']);
    }
}