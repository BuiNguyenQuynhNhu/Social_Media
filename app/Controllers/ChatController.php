<?php

namespace App\Controllers;

use App\Models\Chat;
use App\Models\ChatMember;
use App\Models\User;
use App\Models\FriendShip;

class ChatController extends BaseController
{
    protected $chatModel;
    protected $chatMemberModel;
    protected $userId;

    public function __construct()
    {
        $this->chatModel = new Chat();
        $this->chatMemberModel = new ChatMember();
        $this->userId = session()->get('user_id');
    }

    public function create()
    {
        if (!$this->userId) return $this->response->setJSON(['status' => 'error']);

        $name = $this->request->getPost('name');
        $data = [
            'name'     => $name,
            'owner_id' => $this->userId,
            'privacy'  => $this->request->getPost('privacy') ?? false
        ];

        $chatId = $this->chatModel->create($data);

        if ($chatId) {
            $this->chatMemberModel->add($chatId, $this->userId, true);
            return $this->response->setJSON(['status' => 'success', 'chat_id' => $chatId]);
        }

        return $this->response->setJSON(['status' => 'error', 'message' => 'Could not create chat']);
    }

    public function invite($chatId)
    {
        $targetUserId = $this->request->getPost('user_id');

        if (!$this->chatMemberModel->isMember($chatId, $this->userId)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Unauthorized']);
        }

        if ($this->chatMemberModel->add($chatId, $targetUserId)) {
            return $this->response->setJSON(['status' => 'success']);
        }
        return $this->response->setJSON(['status' => 'error']);
    }

    public function updateNickname($chatId)
    {
        $nickname = $this->request->getPost('nickname');
        if ($this->chatMemberModel->setNickname($chatId, $this->userId, $nickname)) {
            return $this->response->setJSON(['status' => 'success']);
        }
        return $this->response->setJSON(['status' => 'error']);
    }
}