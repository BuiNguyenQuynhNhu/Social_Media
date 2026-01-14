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
        $file = $this->request->getFile('media');
        
        if (empty($content) && (!$file || !$file->isValid())) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Content cannot be empty']);
        }

        $data = [
            'post_id' => $postId,
            'user_id' => $this->userId,
            'content' => $content,
            'parent_id' => $this->request->getPost('parent_id') ?? null
        ];

        $result = $this->comment->addWithMedia($data, $file);

        if ($result) {
            // Need to fetch enriched user data for immediate display
            // Can modify addWithMedia to return enriched data or just fetch here quickly
            // For now, let's reuse fetchUsers logic or simple single fetch since it's just one user (self)
            
            $db = \Config\Database::connect();
            $user = $db->table('users')->where('id', $this->userId)->get()->getRowArray();
            $profile = $db->table('profiles')->where('id', $this->userId)->get()->getRowArray();
            
            $avatar = $user['avatar'] ?? 'ICON/avatar-default.svg';
            if(strpos($avatar, 'http') !== 0) $avatar = base_url($avatar);

            return $this->response->setJSON([
                'status' => 'success',
                'data' => [
                    'id' => $result['id'],
                    'content' => $result['content'],
                    'media' => $result['media'] ?? null,
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
        if (!$this->userId) return $this->response->setJSON(['status' => 'error', 'message' => 'Unauthorized']);

        $comment = $this->comment->find($commentId);
        if (!$comment || $comment['user_id'] !== $this->userId) {
             return $this->response->setJSON(['status' => 'error', 'message' => 'Unauthorized or Not Found']);
        }

        $result = $this->comment->delete($commentId);
        return $this->response->setJSON(['status' => $result->getDeletedCount() > 0 ? 'success' : 'error']);
    }

    public function like($commentId)
    {
        if (!$this->userId) return $this->response->setJSON(['status' => 'error']);
        
        $result = $this->comment->toggleLike($commentId, $this->userId);
        return $result ? $this->response->setJSON(['status' => 'success'] + $result) : $this->response->setJSON(['status' => 'error']);
    }

    public function getComments($postId)
    {
        $result = $this->comment->getThread($postId, $this->userId);
        return $this->response->setJSON(['status' => 'success', 'data' => $result]);
    }
}
