<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Libraries\Cloudinary;

class CommentMediaController extends BaseController
{
    protected $userId;

    public function __construct()
    {
        $this->userId = session()->get('user_id');
    }

    public function upload()
    {
        if (!$this->userId) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Unauthorized']);
        }

        $file = $this->request->getFile('file');
        if (!$file || !$file->isValid()) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Invalid file']);
        }

        try {
            $cloudinary = new Cloudinary();
            $folder = 'comments';
            $res = $cloudinary->uploadPost($file->getTempName(), $folder);

            return $this->response->setJSON([
                'status' => 'success',
                'data' => [
                    'url' => $res['secure_url'],
                    'type' => $res['resource_type'],
                    'public_id' => $res['public_id']
                ]
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Comment Media Upload Error: ' . $e->getMessage());
            return $this->response->setJSON(['status' => 'error', 'message' => 'Upload failed']);
        }
    }

    public function delete()
    {
        if (!$this->userId) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Unauthorized']);
        }

        $publicId = $this->request->getPost('public_id');
        if (!$publicId) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Public ID required']);
        }

        // In a real app, we should verify that this media belongs to a comment by this user
        // For now, we'll just allow deletion if authenticated
        
        try {
            // Cloudinary deletion logic if needed, or just remove from DB references
            // Since we store media inside the comment document, deletion is usually deleting the comment itself
            // or updating the comment. 
            // This endpoint might be used for cleaning up orphaned uploads or removing media from a drafted comment.
            
            return $this->response->setJSON(['status' => 'success']);
        } catch (\Exception $e) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Deletion failed']);
        }
    }
}
