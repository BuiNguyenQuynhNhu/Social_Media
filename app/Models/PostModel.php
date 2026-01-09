<?php
namespace App\Models;

use CodeIgniter\Model;

class PostModel extends Model
{
    protected $table = 'posts';
    protected $primaryKey       = 'id';
    protected $allowedFields = [
        'user_id',
        'content',
        'media_url',
        'privacy',
    ];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $returnType = 'array';
    public function getPostsByUserId($targetUserId, $limit = 5, $offset = 0)
    {
        $currentUserId = session()->get('user_id');

        return $this->select('posts.*')
            ->select('users.avatar, profiles.firstname, profiles.lastname')
            ->select('(SELECT COUNT(*) FROM reactions WHERE reactions.post_id = posts.id) as like_count')
            ->select('(SELECT COUNT(*) FROM comments WHERE comments.post_id = posts.id) as comment_count')
            ->select("(SELECT reaction FROM reactions WHERE reactions.post_id = posts.id AND reactions.user_id = '$currentUserId') as my_reaction")
            ->select("(SELECT reaction FROM reactions WHERE reactions.post_id = posts.id GROUP BY reaction ORDER BY COUNT(*) DESC LIMIT 1) as top_reaction")

            ->join('users', 'users.id = posts.user_id')
            ->join('profiles', 'profiles.id = users.id', 'left')

            ->groupStart()
                ->where('posts.user_id', $targetUserId) 
            ->groupEnd()

            ->orderBy('posts.created_at', 'DESC')
            ->findAll($limit, $offset); 
    }

    public function countPostsByUserId($targetUserId)
    {
        return $this->where('user_id', $targetUserId)->countAllResults();
    }
}
