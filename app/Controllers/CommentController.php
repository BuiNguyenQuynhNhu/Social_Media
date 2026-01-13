<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\Comment;
use App\Models\User;
use App\Models\Profile;

class CommentController extends BaseController
{
    protected $comment;
    protected $userId;

    public function __construct()
    {
        $this->comment = new Comment();
        $this->userId = session()->get('user_id');
    }

    public function add($postId)
    {
        if (!$this->userId) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Unauthorized']);
        }

        $content = $this->request->getPost('content');
        $parentId = $this->request->getPost('parent_id') ?? null;
        
        if (empty($content)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Content cannot be empty']);
        }

        $data = [
            'post_id' => $postId,
            'user_id' => $this->userId,
            'content' => $content,
            'parent_id' => $parentId
        ];

        $result = $this->comment->add($data);

        if ($result) {
            $userModel = new User();
            $profileModel = new Profile();
            $user = $userModel->find($this->userId);
            $profile = $profileModel->find($this->userId);
            
            $avatar = $user['avatar'] ?? 'ICON/avatar-default.svg';
            if(strpos($avatar, 'http') !== 0) $avatar = base_url($avatar);

            return $this->response->setJSON([
                'status' => 'success',
                'data' => [
                    'id' => $result['id'],
                    'content' => $result['content'],
                    'parent_id' => $result['parent_id'],
                    'depth' => $result['depth'],
                    'likes_count' => 0,
                    'user' => [
                        'id' => $this->userId,
                        'avatar' => $avatar,
                        'name' => $profile ? $profile['firstname'] . ' ' . $profile['lastname'] : 'Unknown'
                    ],
                    'created_at' => $result['created_at']
                ]
            ]);
        }

        return $this->response->setJSON(['status' => 'error', 'message' => 'Failed to add comment']);
    }

    public function delete($commentId)
    {
        if (!$this->userId) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Unauthorized']);
        }

        $comment = $this->comment->find($commentId);
        
        if (!$comment || $comment['user_id'] !== $this->userId) {
             return $this->response->setJSON(['status' => 'error', 'message' => 'Unauthorized or Not Found']);
        }

        $result = $this->comment->delete($commentId);

        if ($result->getDeletedCount() > 0) {
            return $this->response->setJSON(['status' => 'success']);
        }

        return $this->response->setJSON(['status' => 'error', 'message' => 'Failed to delete']);
    }

    public function like($commentId)
    {
        if (!$this->userId) {
            return $this->response->setJSON(['status' => 'error']);
        }
        
        $result = $this->comment->toggleLike($commentId, $this->userId);
        
        if ($result) {
            return $this->response->setJSON(['status' => 'success', 'action' => $result['action'], 'count' => $result['count']]);
        }
        
        return $this->response->setJSON(['status' => 'error', 'message' => 'Not found']);
    }

    public function getComments($postId)
    {
        $comments = $this->comment->get($postId);
        
        $result = [];
        $userModel = new User();
        $profileModel = new Profile();
        $usersCache = []; 
        
        foreach ($comments as $comment) {
            $uid = $comment['user_id'];
            if (!isset($usersCache[$uid])) {
                $u = $userModel->find($uid);
                $p = $profileModel->find($uid);
                
                $avatar = $u['avatar'] ?? 'ICON/avatar-default.svg';
                if(strpos($avatar, 'http') !== 0) $avatar = base_url($avatar);

                $usersCache[$uid] = [
                    'id' => $uid,
                    'avatar' => $avatar,
                    'name' => $p ? $p['firstname'] . ' ' . $p['lastname'] : 'Unknown'
                ];
            }
            
            $result[] = [
                'id' => (string)$comment['_id'],
                'post_id' => $comment['post_id'],
                'content' => $comment['content'],
                'parent_id' => $comment['parent_id'] ?? null,
                'depth' => $comment['depth'] ?? 0,
                'created_at' => $comment['created_at'] ?? date('Y-m-d H:i:s'), // Ensure created_at is present
                'user' => $usersCache[$uid],
                'likes_count' => count($comment['likes'] ?? []),
                'is_liked' => in_array($this->userId, (array)($comment['likes'] ?? [])),
                'can_delete' => ($uid == $this->userId)
            ];
        }

        return $this->response->setJSON(['status' => 'success', 'data' => $result]);
    }
}
