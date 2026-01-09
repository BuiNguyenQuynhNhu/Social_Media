<?php

namespace App\Models;

use CodeIgniter\Model;

class CommentModel extends Model
{
    protected $table            = 'comments';
    protected $primaryKey       = 'id';
    protected $allowedFields    = ['user_id', 'post_id', 'parent_id', 'content'];
    
    // Cấu hình Soft Delete (vì bạn có cột deleted_at)
    protected $useSoftDeletes   = true; 
    protected $useTimestamps    = true;
    protected $dateFormat       = 'datetime';
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';
    protected $deletedField     = 'deleted_at';

    public function getCommentsByPostId($postId)
    {
        return $this->select('comments.*, users.avatar, profiles.firstname, profiles.lastname')
                    ->join('users', 'users.id = comments.user_id')
                    ->join('profiles', 'profiles.id = users.id', 'left')
                    ->where('comments.post_id', $postId)
                    ->orderBy('comments.created_at', 'ASC') 
                    ->findAll();
    }
}