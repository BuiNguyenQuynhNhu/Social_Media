<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Libraries\MongoDB;
use App\Libraries\Media;
use MongoDB\BSON\ObjectId;
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

    private function _prepareResponseData($result)
    {
        $userModel = new User();
        $profileModel = new Profile();
        $u = $userModel->find($this->userId);
        $p = $profileModel->where('id', $this->userId)->first();
        
        $avatar = ($u && isset($u['avatar'])) ? $u['avatar'] : 'ICON/avatar-default.svg';
        if(strpos($avatar, 'http') !== 0) $avatar = base_url($avatar);

        return array_merge($result, [
            'user' => [
                'id' => $this->userId,
                'name'   => $p ? $p['firstname'] . ' ' . $p['lastname'] : 'Unknown',
                'avatar' => $avatar
            ]
        ]);
    }

    public function add($postId)
    {
        if (!$this->userId) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Unauthorized']);
        }

        $rules = [
            'media' => [
                'label' => 'Media file',
                'rules' => 'max_size[media,10240]|ext_in[media,jpg,jpeg,png,gif,mp4,mov,avi]',
                'errors' => [
                    'max_size' => 'File size exceeds 10MB limit.',
                    'ext_in'   => 'Only images and videos are allowed.'
                ]
            ]
        ];

        $file = $this->request->getFile('media');

        if ($file && $file->isValid() && !$this->validate($rules)) {
            return $this->response->setJSON(['status' => 'error', 'message' => $this->validator->getError('media')]);
        }

        $content = $this->request->getPost('content');
        $parentId = $this->request->getPost('parent_id') ?: null;
        
        $mediaItem = null;
        if ($file && $file->isValid()) {
            $mediaService = new Media();
            // Assuming uploadMultiple returns an array of media objects/arrays
            $uploadedMedia = $mediaService->uploadMultiple([$file], 'comments');
            $mediaItem = $uploadedMedia[0] ?? null;
        }

        if (empty($content) && !$mediaItem) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Comment cannot be empty']);
        }

        $data = [
            'post_id'   => $postId,
            'user_id'   => $this->userId,
            'content'   => $content,
            'media'     => $mediaItem,
            'parent_id' => $parentId
        ];

        // Using basic add from model, expecting model to handle insertion
        $result = $this->comment->add($data);

        if ($result) {
            return $this->response->setJSON([
                'status' => 'success',
                'data'   => $this->_prepareResponseData($result)
            ]);
        }

        return $this->response->setJSON(['status' => 'error', 'message' => 'Failed to add comment']);
    }

    public function delete($commentId)
    {
        if (!$this->userId) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Unauthorized']);
        }

        $filter = ['_id' => new ObjectId($commentId)];
        $comment = $this->comment->find($filter);

        if (!$comment || $comment['user_id'] !== $this->userId) {
             return $this->response->setJSON(['status' => 'error', 'message' => 'Unauthorized or Not Found']);
        }

        $result = $this->comment->delete($filter);

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
        // Get raw comments
        $comments = $this->comment->get($postId);
        
        $result = [];
        $userModel = new User();
        $profileModel = new Profile();
        $usersCache = []; 

        foreach ($comments as $comment) {
            $uid = $comment['user_id'];
            
            if (!isset($usersCache[$uid])) {
                $u = $userModel->find($uid);
                $p = $profileModel->where('id', $uid)->first(); // or find($uid)
                
                $avatar = ($u && isset($u['avatar'])) ? $u['avatar'] : 'ICON/avatar-default.svg';
                if(strpos($avatar, 'http') !== 0) $avatar = base_url($avatar);

                $usersCache[$uid] = [
                    'id' => $uid,
                    'avatar' => $avatar,
                    'name' => $p ? $p['firstname'] . ' ' . $p['lastname'] : 'Unknown'
                ];
            }
            
            $result[] = [
                'id'            => (string)$comment['_id'],
                'post_id'       => $comment['post_id'],
                'content'       => $comment['content'],
                'media'         => $comment['media'] ?? null,
                'parent_id'     => isset($comment['parent_id']) ? (string)$comment['parent_id'] : null,
                'depth'         => $comment['depth'] ?? 0,
                'created_at'    => $comment['created_at'] ?? date('Y-m-d H:i:s'),
                'user'          => $usersCache[$uid],
                'likes_count'   => count($comment['likes'] ?? []),
                'is_liked'      => in_array($this->userId, (array)($comment['likes'] ?? [])),
                'can_delete'    => ($uid == $this->userId)
            ];
        }
        
        return $this->response->setJSON(['status' => 'success', 'data' => $result]);
    }
}
