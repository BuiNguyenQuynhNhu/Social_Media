<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\Interation;
use Ramsey\Uuid\Uuid;

class InteractionController extends BaseController
{
    protected $interaction;
    protected $userId;

    public function __construct()
    {
        $this->interaction = new Interation();
        $this->userId = session()->get('user_id');
    }

    public function react($postId)
    {
        if (!$this->userId) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Unauthorized']);
        }

        $type = $this->request->getPost('type') ?? 'like';
        $allowedTypes = ['like', 'love', 'haha', 'wow', 'sad', 'angry'];

        if (!in_array($type, $allowedTypes)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Invalid reaction type']);
        }

        // Check if already reacted
        $existing = $this->interaction->where('user_id', $this->userId)
                                      ->where('post_id', $postId)
                                      ->first();

        if ($existing) {
            if ($existing['type'] === $type) {
                // Remove reaction (toggle off)
                $this->interaction->delete($existing['id']);
                return $this->response->setJSON(['status' => 'success', 'action' => 'unreacted']);
            } else {
                // Update reaction type
                $this->interaction->update($existing['id'], ['type' => $type]);
                return $this->response->setJSON(['status' => 'success', 'action' => 'updated', 'type' => $type]);
            }
        }

        // Create new reaction
        $this->interaction->insert([
            'id'      => Uuid::uuid4()->toString(),
            'user_id' => $this->userId,
            'post_id' => $postId,
            'type'    => $type
        ]);

        return $this->response->setJSON(['status' => 'success', 'action' => 'reacted', 'type' => $type]);
    }

    public function getLikes($postId)
    {
        $reactions = $this->interaction->select('users.id, users.avatar, profiles.firstname, profiles.lastname, interations.type')
                                   ->join('users', 'users.id = interations.user_id')
                                   ->join('profiles', 'profiles.id = users.id')
                                   ->where('post_id', $postId)
                                   ->findAll();
        
        $counts = array_count_values(array_column($reactions, 'type'));
        
        // Check current user's reaction
        $myReaction = $this->interaction->where('user_id', $this->userId)
                                        ->where('post_id', $postId)
                                        ->first();
        
        return $this->response->setJSON([
            'counts' => $counts, 
            'total' => count($reactions), 
            'users' => $reactions,
            'myReaction' => $myReaction ? $myReaction['type'] : null
        ]);
    }
}
