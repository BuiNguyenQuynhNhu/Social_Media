<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\Post;
use App\Libraries\Cloudinary;
use App\Libraries\MongoDB; 
use App\Libraries\Media;


class PostController extends BaseController
{
    protected $post;
    protected $userId;

    public function __construct() {
        $this->post = new Post();
        $this->userId = session()->get('user_id');
    }

    public function create() {
        if (!$this->userId) return redirect()->to('login');
        
        $content = $this->request->getPost('content');
        if (empty($content)) {
            return redirect()->back()->with('error', 'Post content is required.');
        }

        $data = [
            'content' => $content,
            'id_user' => $this->userId,
            'privacy' => $this->request->getPost('privacy') ?? 'public',
        ];

        $files = $this->request->getFileMultiple('images');
        $groupId = $this->request->getPost('group_id');

        if ($this->post->createPost($data, $files, $groupId)) {
            return redirect()->back()->with('success', 'Posted successfully!');
        }

        return redirect()->back()->with('error', 'Failed to create post.');
    }

    public function delete($postId) {
        if (!$this->userId) return redirect()->to('login');

        $post = $this->post->find($postId);
        if (!$post || $post['id_user'] !== $this->userId) {
            return redirect()->back()->with('error', 'Unauthorized access.');
        }

        if ($this->post->deleteWithMedia($postId)) {
            return redirect()->back()->with('success', 'Post deleted.');
        }
        return redirect()->back()->with('error', 'Could not delete post.');
    }


    public function update($postId) {
        if (!$this->userId) return redirect()->to('login');

        $post = $this->post->find($postId);
        if (!$post || $post['id_user'] !== $this->userId) {
            return redirect()->back()->with('error', 'Unauthorized access.');
        }

        $content = $this->request->getPost('content');
        $privacy = $this->request->getPost('privacy');

        $data = [];
        if (!empty($content)) $data['content'] = $content;
        if (!empty($privacy)) $data['privacy'] = $privacy;

        if (!empty($data) && $this->post->update($postId, $data)) {
            return redirect()->back()->with('success', 'Post updated.');
        }

        return redirect()->back()->with('error', 'Failed to update post.');
    }

    public function listAll() {
        return $this->post->list();
    }

    public function listFeed($userId) {
        return $this->post->getFeed($userId);
    }

    public function listUserPosts($id) {
        return $this->post->getPostByUser($id);
    }

}