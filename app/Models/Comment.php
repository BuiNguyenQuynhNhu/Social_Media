<?php

namespace App\Models;

use App\Libraries\MongoDB;
use App\Libraries\Cloudinary;
use MongoDB\BSON\ObjectId;
use App\Models\User;
use App\Models\Profile;

class Comment
{
    protected $collection;

    public function __construct()
    {
        $mongo = new MongoDB();
        $this->collection = $mongo->getCollection('comments');
    }

    public function addWithMedia($data, $file = null)
    {
        // Handle Media Upload
        if ($file && $file->isValid()) {
            try {
                $cloudinary = new Cloudinary();
                $res = $cloudinary->uploadPost($file->getTempName(), 'comments');
                $data['media'] = [
                    'url'       => $res['secure_url'],
                    'type'      => $res['resource_type'],
                    'public_id' => $res['public_id']
                ];
            } catch (\Exception $e) {
                log_message('error', 'Comment Media Upload Error: ' . $e->getMessage());
            }
        }

        return $this->add($data);
    }

    public function add($data)
    {
        $depth = 0;
        if (!empty($data['parent_id'])) {
            $parent = $this->collection->findOne(['_id' => new ObjectId($data['parent_id'])]);
            if ($parent) {
                $parentDepth = $parent['depth'] ?? 0;
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
            'media'      => $data['media'] ?? null,
            'created_at' => date('Y-m-d H:i:s')
        ];

        $result = $this->collection->insertOne($comment);
        
        if ($result->getInsertedCount() > 0) {
            $comment['id'] = (string)$result->getInsertedId();
            return $comment;
        }
        
        return false;
    }

    public function getThread($postId, $currentUserId = null)
    {
        $comments = $this->collection->find(['post_id' => $postId], ['sort' => ['created_at' => 1]])->toArray();
        if (empty($comments)) return [];

        $userIds = array_unique(array_column($comments, 'user_id'));
        $users = $this->fetchUsers($userIds);

        $result = [];
        foreach ($comments as $comment) {
            $uid = $comment['user_id'];
            $result[] = [
                'id' => (string)$comment['_id'],
                'post_id' => $comment['post_id'],
                'content' => $comment['content'],
                'parent_id' => $comment['parent_id'] ?? null,
                'depth' => $comment['depth'] ?? 0,
                'created_at' => $comment['created_at'] ?? date('Y-m-d H:i:s'),
                'user' => $users[$uid] ?? ['name' => 'Unknown', 'avatar' => base_url('ICON/avatar-default.svg')],
                'likes_count' => count($comment['likes'] ?? []),
                'is_liked' => $currentUserId ? in_array($currentUserId, (array)($comment['likes'] ?? [])) : false,
                'can_delete' => ($uid == $currentUserId),
                'media' => $comment['media'] ?? null
            ];
        }
        return $result;
    }

    private function fetchUsers($userIds)
    {
        $userModel = new User();
        $profileModel = new Profile();
        
        // Optimize with whereIn if possible, or loop (SQL)
        $users = [];
        foreach ($userIds as $uid) {
            $u = $userModel->find($uid);
            $p = $profileModel->find($uid);
            
            $avatar = ($u && isset($u['avatar'])) ? $u['avatar'] : 'ICON/avatar-default.svg';
            if(strpos($avatar, 'http') !== 0) $avatar = base_url($avatar);

            $users[$uid] = [
                'id' => $uid,
                'avatar' => $avatar,
                'name' => $p ? $p['firstname'] . ' ' . $p['lastname'] : 'Unknown'
            ];
        }
        return $users;
    }

    public function get($postId)
    {
        return $this->collection->find(['post_id' => $postId], ['sort' => ['created_at' => 1]])->toArray();
    }

    public function find($idOrFilter)
    {
        if (is_array($idOrFilter)) {
            return $this->collection->findOne($idOrFilter);
        }
        return $this->collection->findOne(['_id' => new ObjectId($idOrFilter)]);
    }

    public function delete($idOrFilter)
    {
        if (is_array($idOrFilter)) {
            return $this->collection->deleteOne($idOrFilter);
        }
        return $this->collection->deleteOne(['_id' => new ObjectId($idOrFilter)]);
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
