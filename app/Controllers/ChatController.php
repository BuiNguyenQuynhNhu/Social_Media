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

    public function index() {
        if (!$this->userId) return redirect()->to('/login');

        $db = \Config\Database::connect();
        $user = $db->table('users')->where('id', $this->userId)->get()->getRowArray();
        $avatarUrl = $user['avatar'] ?? base_url('images/default-avatar.png');

        return view('chat/index', [
            'user_id' => $this->userId,
            'avatarUrl' => $avatarUrl
        ]);
    }

    public function list() {
        if (!$this->userId) return $this->response->setJSON(['status' => 'error']);

        // Get all chat IDs where user is a member
        $db = \Config\Database::connect();
        $userChats = $db->table($this->chatMemberModel->table)
            ->where('user_id', $this->userId)
            ->get()->getResultArray();

        $chats = [];
        foreach ($userChats as $membership) {
            $chat = $this->chatModel->find($membership['chat_id']);
            if (!$chat) continue;

            // Get last message from MongoDB
            $messageModel = new \App\Models\Message();
            $messages = $messageModel->get($this->userId, $membership['chat_id']);
            $lastMessage = !empty($messages) ? end($messages)['content'] : null;

            $chats[] = [
                'id' => $chat['id'],
                'name' => $chat['name'],
                'avatar' => null,
                'last_message' => $lastMessage,
                'unread_count' => 0
            ];
        }

        return $this->response->setJSON(['status' => 'success', 'data' => $chats]);
    }

    public function getMembers($chatId) {
        if (!$this->userId) return $this->response->setJSON(['status' => 'error']);

        if (!$this->chatMemberModel->isMember($chatId, $this->userId)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Not a member']);
        }

        $db = \Config\Database::connect();
        $members = $db->table('chatmembers')
            ->select('chatmembers.*, users.avatar, profiles.firstname, profiles.lastname')
            ->join('users', 'users.id = chatmembers.user_id')
            ->join('profiles', 'profiles.id = chatmembers.user_id')
            ->where('chatmembers.chat_id', $chatId)
            ->get()->getResultArray();

        $result = [];
        foreach ($members as $member) {
            $result[] = [
                'user_id' => $member['user_id'],
                'name' => $member['firstname'] . ' ' . $member['lastname'],
                'nickname' => $member['nickname'],
                'avatar' => $member['avatar'] ?? base_url('images/default-avatar.png'),
                'is_admin' => (bool)$member['role']
            ];
        }

        return $this->response->setJSON(['status' => 'success', 'data' => $result]);
    }

    public function update($chatId) {
        if (!$this->userId) return $this->response->setJSON(['status' => 'error']);

        $chat = $this->chatModel->find($chatId);
        if (!$chat || $chat['owner_id'] !== $this->userId) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Unauthorized']);
        }

        $name = $this->request->getPost('name');
        if ($name && $this->chatModel->update($chatId, ['name' => $name])) {
            return $this->response->setJSON(['status' => 'success']);
        }

        return $this->response->setJSON(['status' => 'error']);
    }

    public function leave($chatId) {
        if (!$this->userId) return $this->response->setJSON(['status' => 'error']);

        if ($this->chatMemberModel->deleteMember($chatId, $this->userId)) {
            return $this->response->setJSON(['status' => 'success']);
        }

        return $this->response->setJSON(['status' => 'error']);
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