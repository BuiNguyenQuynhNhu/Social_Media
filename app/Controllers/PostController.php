<?php

namespace App\Controllers;

use App\Models\PostModel;
use App\Models\ReactionModel;
use App\Models\CommentModel;

class PostController extends BaseController
{
    public function create()
    {
        if (!session()->get('logged_in')) {
            return $this->response->setJSON([
                'status' => 'error', 
                'message' => 'Please login to post.'
            ]);
        }

        $rules = [
            'content' => [
                'rules'  => 'required|min_length[1]', 
                'errors' => [
                    'required'   => 'The content cannot be empty.',
                    'min_length' => 'The article is too short.'
                ]
            ]
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $this->validator->getErrors()
            ]);
        }

        $content = $this->request->getPost('content');
        $userId  = session()->get('user_id');
        $imagePath = null; 
        $file = $this->request->getFile('image');

        if ($file && $file->isValid() && !$file->hasMoved()) {
            $cloudinary = new \App\Libraries\CloudinaryLib();
            $uploadedUrl = $cloudinary->uploadImage($file->getTempName(), 'social_posts');
            if ($uploadedUrl) {
                $imagePath = $uploadedUrl; 
            } else {
                return $this->response->setJSON([
                    'status' => 'error', 
                    'message' => 'Image upload failed. Please try again.'
                ]);
            }
        }

        $postModel = new PostModel();
        
        $data = [
            'user_id'   => $userId,
            'content'   => $content,
            'media_url' => $imagePath, 
            'privacy'   => $this->request->getPost('privacy') ?? 'public',
            'created_at'=> date('Y-m-d H:i:s') 
        ];

        if ($postModel->insert($data)) {
            return $this->response->setJSON([
                'status' => 'success', 
                'message' => 'Posted successfully!'
            ]);
        } else {
            return $this->response->setJSON([
                'status' => 'error', 
                'message' => 'Database error, please try again.'
            ]);
        }
    }

    public function getPostsPaging()
    {
        $targetUserId = $this->request->getGet('user_id');
        $page = $this->request->getGet('page') ?? 1;
        $perPage = 10; 
        
        $offset = ($page - 1) * $perPage;

        $postModel = new PostModel();

        $posts = $postModel->getPostsByUserId($targetUserId, $perPage, $offset);

        $totalPosts = $postModel->countPostsByUserId($targetUserId);
        $totalPages = ceil($totalPosts / $perPage);

        return $this->response->setJSON([
            'status' => 'success', 
            'data' => $posts,
            'currentPage' => (int)$page,
            'totalPages' => $totalPages
        ]);
    }

    public function update($id)
    {
        $postModel = new PostModel();
        $post = $postModel->find($id);
        $userId = session()->get('user_id');

        if (!$post || $post['user_id'] !== $userId) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Unauthorized access']);
        }

        $postModel->update($id, [
            'content' => $this->request->getPost('content'),
            'privacy' => $this->request->getPost('privacy')
        ]);

        return $this->response->setJSON(['status' => 'success', 'message' => 'Post updated successfully']);
    }

    public function delete($id)
    {
        $postModel = new PostModel();
        $post = $postModel->find($id);
        $userId = session()->get('user_id');

        if ($post && $post['user_id'] == $userId) {
            if ($post['media_url'] && file_exists(FCPATH . $post['media_url'])) {
                unlink(FCPATH . $post['media_url']);
            }
            $postModel->delete($id);
            return redirect()->back()->with('success', 'Post deleted.');
        }
        return redirect()->back()->with('error', 'Unauthorized.');
    }
    public function react()
    {
        if (!session()->get('logged_in')) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Unauthorized']);
        }

        $userId = session()->get('user_id');
        $postId = $this->request->getPost('post_id');
        $type   = $this->request->getPost('reaction') ?? 'like';

        $reactionModel = new ReactionModel();

        $existing = $reactionModel->where(['user_id' => $userId, 'post_id' => $postId])->first();

        if ($existing) {
            if ($existing['reaction'] === $type) {
                $reactionModel->delete($existing['id']);
                return $this->response->setJSON(['status' => 'removed']);
            } else {
                $reactionModel->update($existing['id'], ['reaction' => $type]);
                return $this->response->setJSON(['status' => 'updated']);
            }
        } else {
            $reactionModel->insert([
                'user_id' => $userId,
                'post_id' => $postId,
                'reaction' => $type
            ]);
            $postModel = new PostModel();
            $post = $postModel->find($postId);
            if ($post) {
                $notifModel = new \App\Models\NotificationModel();
                $notifModel->addNotification($post['user_id'], $userId, 'post_like', $postId);
            }
            return $this->response->setJSON(['status' => 'added']);
        }
    }


    public function addComment()
    {
        if (!session()->get('logged_in')) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Unauthorized']);
        }

        $commentModel = new CommentModel();
        
        $data = [
            'user_id' => session()->get('user_id'),
            'post_id' => $this->request->getPost('post_id'),
            'content' => $this->request->getPost('content'),
            'parent_id' => null,
            'created_at' => date('Y-m-d H:i:s')
        ];

        if ($commentModel->insert($data)) {
            $newId = $commentModel->getInsertID();
            $postModel = new PostModel();
            $post = $postModel->find($data['post_id']);
            if ($post) {
                $notifModel = new \App\Models\NotificationModel();
                $notifModel->addNotification($post['user_id'], $data['user_id'], 'post_comment', $data['post_id']);
            }
            $newComment = $commentModel->select('comments.*, users.avatar, profiles.firstname, profiles.lastname')
                                       ->join('users', 'users.id = comments.user_id')
                                       ->join('profiles', 'profiles.id = users.id', 'left')
                                       ->find($newId);

            return $this->response->setJSON(['status' => 'success', 'comment' => $newComment]);
        }

        return $this->response->setJSON(['status' => 'error']);
    }

    public function updateComment()
    {
        if (!session()->get('logged_in')) return $this->response->setJSON(['status' => 'error', 'message' => 'Unauthorized']);

        $commentId = $this->request->getPost('comment_id');
        $newContent = $this->request->getPost('content');
        $userId = session()->get('user_id');
        
        $commentModel = new CommentModel();
        $comment = $commentModel->find($commentId);

        if ($comment && $comment['user_id'] === $userId) {
            $commentModel->update($commentId, ['content' => $newContent]);
            return $this->response->setJSON(['status' => 'success']);
        }

        return $this->response->setJSON(['status' => 'error', 'message' => 'Unauthorized']);
    }

    public function deleteComment()
    {
        if (!session()->get('logged_in')) return $this->response->setJSON(['status' => 'error']);

        $commentId = $this->request->getPost('comment_id');
        $userId = session()->get('user_id');
        
        $commentModel = new CommentModel();
        $comment = $commentModel->find($commentId);

        if ($comment && $comment['user_id'] === $userId) {
            $commentModel->delete($commentId);
            return $this->response->setJSON(['status' => 'success']);
        }

        return $this->response->setJSON(['status' => 'error', 'message' => 'Unauthorized']);
    }

    public function getComments($postId)
    {
        $commentModel = new CommentModel();
        $comments = $commentModel->getCommentsByPostId($postId);
        return $this->response->setJSON(['status' => 'success', 'data' => $comments]);
    }

    public function getReactors($postId)
    {
        $reactionModel = new ReactionModel();
        
        $reactors = $reactionModel->select('reactions.reaction, users.id as user_id, users.avatar, profiles.firstname, profiles.lastname')
            ->join('users', 'users.id = reactions.user_id')
            ->join('profiles', 'profiles.id = users.id', 'left')
            ->where('reactions.post_id', $postId)
            ->orderBy('reactions.created_at', 'DESC')
            ->findAll();

        return $this->response->setJSON(['status' => 'success', 'data' => $reactors]);
    }

}