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
        $parentId = $this->request->getPost('parent_id') ?? null;
        
        if (empty($content)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Content cannot be empty']);
        }

        $depth = 0;
        if ($parentId) {
            $parent = $this->collection->findOne(['_id' => new \MongoDB\BSON\ObjectId($parentId)]);
            if ($parent) {
                $depth = ($parent['depth'] ?? 0) + 1;
            }
            if ($depth > 3) {
                 return $this->response->setJSON(['status' => 'error', 'message' => 'Max reply depth reached']);
            }
        }

        $comment = [
            'post_id' => $postId,
            'user_id' => $this->userId,
            'content' => $content,
            'parent_id' => $parentId,
            'depth' => $depth,
            'likes' => [],
            'created_at' => date('Y-m-d H:i:s')
        ];

        $result = $this->collection->insertOne($comment);

        if ($result->getInsertedCount() > 0) {
            $userModel = new User();
            $profileModel = new Profile();
            $user = $userModel->find($this->userId);
            $profile = $profileModel->find($this->userId);
            
            $avatar = $user['avatar'] ?? 'ICON/avatar-default.svg';
            if(strpos($avatar, 'http') !== 0) $avatar = base_url($avatar);

            return $this->response->setJSON([
                'status' => 'success',
                'data' => [
                    'id' => (string)$result->getInsertedId(),
                    'content' => $content,
                    'parent_id' => $parentId,
                    'depth' => $depth,
                    'likes_count' => 0,
                    'user' => [
                        'id' => $this->userId,
                        'avatar' => $avatar,
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

    public function like($commentId) {
        if (!$this->userId) return $this->response->setJSON(['status' => 'error']);
        
        $oid = new \MongoDB\BSON\ObjectId($commentId);
        $comment = $this->collection->findOne(['_id' => $oid]);
        
        if (!$comment) return $this->response->setJSON(['status' => 'error', 'message' => 'Not found']);

        $likes = (array)($comment['likes'] ?? []);
        $action = 'liked';

        if (in_array($this->userId, $likes)) {
            $likes = array_diff($likes, [$this->userId]);
            $action = 'unliked';
        } else {
            $likes[] = $this->userId;
        }

        $this->collection->updateOne(
            ['_id' => $oid],
            ['$set' => ['likes' => array_values($likes)]]
        );

        return $this->response->setJSON(['status' => 'success', 'action' => $action, 'count' => count($likes)]);
    }

    public function getComments($postId)
    {
        $comments = $this->collection->find(['post_id' => $postId], ['sort' => ['created_at' => 1]]);
        
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
                'created_at' => $comment['created_at'],
                'user' => $usersCache[$uid],
                'likes_count' => count($comment['likes'] ?? []),
                'is_liked' => in_array($this->userId, (array)($comment['likes'] ?? [])),
                'can_delete' => ($uid == $this->userId)
            ];
        }

        return $this->response->setJSON(['status' => 'success', 'data' => $result]);
    }
}
