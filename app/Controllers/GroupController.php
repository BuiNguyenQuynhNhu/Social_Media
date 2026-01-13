<?php

namespace App\Controllers;

use App\Models\Group;
use App\Models\GroupMember;
use App\Models\Chat;
use App\Models\ChatMember;
use App\Models\Requirement;
use App\Libraries\MongoDB;

class GroupController extends BaseController
{
    protected $group;
    protected $mongo;
    protected $user_id;

    public function __construct(){
        $this->group = new Group();
        $this->mongo = new MongoDB();
        $this->user_id = session()->get('user_id');
    }

    public function create(){
        $data = [
            'name' => $this->request->getPost('name'),
            'owner_id' => $this->user_id,
            'privacy' => $this->request->getPost('privacy') ?? 'public'
        ];

        $groupId = $this->group->create($data);

        (new GroupMember())->add($groupId, $this->user_id, 'admin');
        (new Chat())->insert(['id' => $groupId, 'name' => $data['name'], 'owner_id' => $this->user_id]);
        (new ChatMember())->add($groupId, $this->user_id, 'admin');
        return $this->response->setJSON(['status' => 'success', 'group_id' => $groupId]);
    }

    public function setRequire($groupId){
        $questions = $this->request->getPost('questions');
        
        $this->mongo->getCollection('requirement_configs')->replaceOne(
            ['_id' => $groupId],
            ['_id' => $groupId, 'questions' => $questions, 'updated_at' => date('Y-m-d H:i:s')],
            ['upsert' => true]
        );

        return $this->response->setJSON(['status' => 'success']);
    }
    
    public function promote($groupId, $targetUserId) {
        $group = $this->group->find($groupId);
        if ($group['owner_id'] !== $this->user_id) return $this->response->setJSON(['status' => 'error']);

        (new GroupMember())->promote($groupId, $targetUserId);
        return $this->response->setJSON(['status' => 'success']);
    }
}
