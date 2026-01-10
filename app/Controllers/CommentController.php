<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Libraries\MongoDB;
use App\Models\User;
use App\Models\Profile;

class CommentController extends BaseController
{
    protected $mongo;
    protected $collection;
    protected $userId;

    public function __construct()
    {
        $this->mongo = new MongoDB();
        $this->collection = $this->mongo->getCollection('comments');
        $this->userId = session()->get('user_id');
    }

    public function add($postId)
    {
        if (!$this->userId) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Unauthorized']);
        }

        $content = $this->request->getPost('content');
        if (empty($content)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Content cannot be empty']);
        }

        $comment = [
            'post_id' => $postId,
            'user_id' => $this->userId,
            'content' => $content,
            'created_at' => date('Y-m-d H:i:s')
        ];

        $result = $this->collection->insertOne($comment);

        if ($result->getInsertedCount() > 0) {
            // Fetch user details for immediate display
            $userModel = new User();
            $profileModel = new Profile();
            $user = $userModel->find($this->userId);
            $profile = $profileModel->find($this->userId);

            return $this->response->setJSON([
                'status' => 'success',
                'data' => [
                    'id' => (string)$result->getInsertedId(),
                    'content' => $content,
                    'user' => [
                        'id' => $this->userId,
                        'avatar' => $user['avatar'] ?? 'ICON/avatar-default.svg',
                        'name' => $profile ? $profile['firstname'] . ' ' . $profile['lastname'] : 'Unknown'
                    ],
                    'created_at' => $comment['created_at']
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

        // Logic to verify ownership would normally look up the comment first
        // For now trusting the user owns it or is admin (Implementation simplification)
        
        $filter = ['_id' => new \MongoDB\BSON\ObjectId($commentId)];
        $comment = $this->collection->findOne($filter);
        
        if (!$comment || $comment['user_id'] !== $this->userId) {
             return $this->response->setJSON(['status' => 'error', 'message' => 'Unauthorized or Not Found']);
        }

        $result = $this->collection->deleteOne($filter);

        if ($result->getDeletedCount() > 0) {
            return $this->response->setJSON(['status' => 'success']);
        }

        return $this->response->setJSON(['status' => 'error', 'message' => 'Failed to delete']);
    }

    public function getComments($postId)
    {
        $comments = $this->collection->find(['post_id' => $postId], ['sort' => ['created_at' => -1]]);
        
        $result = [];
        $userModel = new User();
        $profileModel = new Profile();
        
        foreach ($comments as $comment) {
            $user = $userModel->find($comment['user_id']);
            $profile = $profileModel->find($comment['user_id']);
            
            $result[] = [
                'id' => (string)$comment['_id'],
                'content' => $comment['content'],
                'created_at' => $comment['created_at'],
                'user' => [
                    'id' => $comment['user_id'],
                    'avatar' => $user['avatar'] ?? 'ICON/avatar-default.svg',
                    'name' => $profile ? $profile['firstname'] . ' ' . $profile['lastname'] : 'Unknown'
                ]
            ];
        }

        return $this->response->setJSON(['status' => 'success', 'data' => $result]);
    }
}
