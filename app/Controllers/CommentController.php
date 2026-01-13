<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Libraries\MongoDB;
use App\Libraries\Media;
use MongoDB\BSON\ObjectId;
use App\Models\User;
use App\Models\Profile;
use App\Models\Comment;

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

        return array_merge($result, [
            'user' => [
                'name'   => $p['firstname'] . ' ' . $p['lastname'],
                'avatar' => (isset($u['avatar']) && strpos($u['avatar'], 'http') === 0) 
                            ? $u['avatar'] 
                            : base_url($u['avatar'] ?: 'ICON/avatar-default.svg')
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
                'rules' => 'max_size[media,2048]|ext_in[media,jpg,jpeg,png,gif,mp4,mov]',
                'errors' => [
                    'max_size' => 'File size exceeds 2MB limit.',
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

        $result = $this->comment->addComment($data);

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

    public function react($commentId)
    {
        if (!$this->userId) return $this->response->setJSON(['status' => 'error']);
        
        $type = $this->request->getPost('type') ?: 'like';
        $result = $this->comment->toggle($commentId, $this->userId, $type);

        if ($result) {
            return $this->response->setJSON(['status' => 'success', 'data' => $result]);
        }
        return $this->response->setJSON(['status' => 'error', 'message' => 'Not found']);
    }

    public function get($postId)
    {
        $comments = $this->comment->get($postId);
        
        $userModel = new User();
        $profileModel = new Profile();
        $usersCache = []; 
        $result = [];
        
        foreach ($comments as $comment) {
            $uid = $comment['user_id'];
            if (!isset($usersCache[$uid])) {
                $u = $userModel->find($uid);
                $p = $profileModel->where('id', $uid)->first();
                
                $avatar = $u['avatar'] ?? 'ICON/avatar-default.svg';
                if(strpos($avatar, 'http') !== 0) $avatar = base_url($avatar);

                $usersCache[$uid] = [
                    'avatar' => $avatar,
                    'name'   => $p['firstname'] . ' ' . $p['lastname']
                ];
            }
            $myReaction = null;
            foreach ($comment['reactions'] ?? [] as $r) {
                if ($r['user_id'] === $this->userId) {
                    $myReaction = $r['type'];
                    break;
                }
            }

            $result[] = [
                'id'            => (string)$comment['_id'],
                'content'       => $comment['content'],
                'media'         => $comment['media'],
                'parent_id'     => isset($comment['parent_id']) ? (string)$comment['parent_id'] : null,
                'depth'         => $comment['depth'] ?? 0,
                'created_at'    => $comment['created_at'],
                'user'          => $usersCache[$uid],
                'reactions'     => array_count_values(array_column((array)($comment['reactions'] ?? []), 'type')),
                'total_reacts'  => count((array)($comment['reactions'] ?? [])),
                'my_reaction'   => $myReaction
            ];
        }

        return $this->response->setJSON(['status' => 'success', 'data' => $result]);
    }
}
