<?php

namespace App\Models;

use App\Libraries\MongoDB;
use MongoDB\BSON\ObjectId;

class Comment
{
    protected $collection;

    public function __construct()
    {
        $mongo = new MongoDB();
        $this->collection = $mongo->getCollection('comments');
    }

    public function add($data)
    {
        $depth = 0;
        if (!empty($data['parent_id'])) {
            $parent = $this->collection->findOne(['_id' => new ObjectId($data['parent_id'])]);
            if ($parent) {
                $parentDepth = $parent['depth'] ?? 0;
                // Cap at depth 2, don't allow deeper nesting
                $depth = min(2, $parentDepth + 1);
            }
        }

        $comment = [
            'post_id'    => $data['post_id'],
            'user_id'    => $data['user_id'],
            'content'    => $data['content'],
            'parent_id'  => $data['parent_id'] ?? null,
            'depth'      => $depth,
            'likes'      => [],
            'created_at' => date('Y-m-d H:i:s')
        ];

        $result = $this->collection->insertOne($comment);
        
        if ($result->getInsertedCount() > 0) {
            $comment['id'] = (string)$result->getInsertedId();
            return $comment;
        }
        
        return false;
    }

    public function get($postId)
    {
        return $this->collection->find(['post_id' => $postId], ['sort' => ['created_at' => 1]])->toArray();
    }

    public function find($commentId)
    {
        return $this->collection->findOne(['_id' => new ObjectId($commentId)]);
    }

    public function delete($commentId)
    {
        return $this->collection->deleteOne(['_id' => new ObjectId($commentId)]);
    }

    public function toggleLike($commentId, $userId)
    {
        $comment = $this->find($commentId);
        
        if (!$comment) return false;

        $likes = (array)($comment['likes'] ?? []);
        $action = 'liked';

        if (in_array($userId, $likes)) {
            $likes = array_diff($likes, [$userId]);
            $action = 'unliked';
        } else {
            $likes[] = $userId;
        }

        $this->collection->updateOne(
            ['_id' => new ObjectId($commentId)],
            ['$set' => ['likes' => array_values($likes)]]
        );

        return ['action' => $action, 'count' => count($likes)];
    }
}
