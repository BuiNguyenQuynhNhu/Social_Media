<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Libraries\MongoDB;
use MongoDB\BSON\ObjectId;

class Commen
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
                $depth = ($parentDepth >= 2) ? 2 : $parentDepth + 1;
            }
        }

        $comment = [
            'post_id'    => $data['post_id'],
            'user_id'    => $data['user_id'],
            'content'    => $data['content'],
            'media'      => $data['media'] ?? null,
            'parent_id'  => $data['parent_id'],
            'depth'      => $depth,
            'reactions'  => [],
            'created_at' => date('Y-m-d H:i:s')
        ];

        $result = $this->collection->insertOne($comment);
        return $result->getInsertedCount() > 0 ? array_merge($comment, ['id' => (string)$result->getInsertedId()]) : false;
    }

    public function toggle($commentId, $userId, $type)
    {
        $oid = new ObjectId($commentId);
        $comment = $this->collection->findOne(['_id' => $oid]);
        if (!$comment) return false;

        $reactions = (array)($comment['reactions'] ?? []);
        $existingIndex = -1;
        foreach ($reactions as $index => $react) {
            if ($react['user_id'] === $userId) {
                $existingIndex = $index;
                break;
            }
        }

        if ($existingIndex !== -1) {
            if ($reactions[$existingIndex]['type'] === $type) {
                array_splice($reactions, $existingIndex, 1);
                $action = 'removed';
            } else {
                $reactions[$existingIndex]['type'] = $type;
                $action = 'updated';
            }
        } else {
            $reactions[] = ['user_id' => $userId, 'type' => $type];
            $action = 'added';
        }

        $this->collection->updateOne(['_id' => $oid], ['$set' => ['reactions' => $reactions]]);
        $counts = array_count_values(array_column($reactions, 'type'));
        
        return ['action' => $action, 'counts' => $counts, 'total' => count($reactions)];
    }

    public function find($id){
        return $this->collection->findOne(['_id' => new ObjectID($id)]);
    }

    public function get($post_id){
        return $this->collection->find(['post_id' => $post_id], ['sort' => ['created_at' => 1]])->toArray();
    }

    public function delete($id, $user_id){
        return $this->collection->deleteOne([
            '_id' => new ObjectId($id),
            'user_id' => $user_id
        ]);
    }
}
