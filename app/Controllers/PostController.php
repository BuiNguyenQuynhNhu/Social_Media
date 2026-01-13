<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Libraries\Cloudinary;
use App\Libraries\MongoDB; 
use App\Libraries\Media;
use App\Models\Post;


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
        $rules = [
            'images' => 'uploaded[images]|max_size[images,5120]|ext_in[images,jpg,jpeg,png,gif,mp4,mov,avi]'
        ];

        $files = $this->request->getFileMultiple('images');
        if ($files && $files[0]->isValid() && !$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', $this->validator->getErrors());
        }

        $postId = $this->post->create([
            'content' => $this->request->getPost('content'),
            'id_user' => $this->userId,
            'privacy' => $this->request->getPost('privacy') ?? 'public',
        ]);

        if ($postId) {
            if ($files && $files[0]->isValid()) {
                $mediaService = new Media();
                $mediaItems = $mediaService->uploadMultiple($files, 'posts');
                $mediaService->storeMetadata($postId, $mediaItems, 'post_media');
            }
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

        $mongo = new MongoDB();
        $mediaCollection = $mongo->getCollection('post_media');
        $mediaData = $mediaCollection->findOne(['post_id' => $postId]);

        if ($mediaData) {
            $cloudinary = new Cloudinary();
            foreach ($mediaData['items'] as $item) {
                $cloudinary->destroy($item['public_id']); 
            }
            $mediaCollection->deleteOne(['post_id' => $postId]);
        }

        if ($this->post->delete($postId)) {
            return redirect()->back()->with('success', 'Post deleted.');
        }
        return redirect()->back()->with('error', 'Could not delete post.');
    }

    public function listAll() {
        $sqlPosts = $this->post->list();
        return $this->mergeMedia($sqlPosts);
    }

    public function listFeed($userId) {
        $sqlPosts = $this->post->getFeed($userId);
        return $this->mergeMedia($sqlPosts);
    }

    public function listUserPosts($id) {
        $sqlPosts = $this->post->getPostByUser($id);
        return $this->mergeMedia($sqlPosts);
    }

    private function mergeMedia($posts) {
        if (empty($posts)) return [];

        $mongo = new MongoDB();
        $mediaCollection = $mongo->getCollection('post_media');

        foreach ($posts as &$post) {
            $media = $mediaCollection->findOne(['post_id' => $post['id']]);
            $post['media'] = $media ? (array)$media['items'] : [];
        }

        return $posts;
    }
}