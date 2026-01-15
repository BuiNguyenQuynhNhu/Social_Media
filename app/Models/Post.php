<?php

namespace App\Models;

use CodeIgniter\Model;
use Ramsey\Uuid\Uuid;
use App\Libraries\MongoDB;
use App\Libraries\Cloudinary;
use App\Libraries\Media;
class Post extends Model
{
    protected $table            = 'posts';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = false;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['id', 'content', 'created_at', 'privacy', 'id_user'];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];
    protected array $castHandlers = [];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];


    //functions
    protected function generateUUID(){
        return Uuid::uuid4()->toString();
    }
    public function create($data){
        $data['id'] = $this->generateUUID();
        $data['created_at'] = date('Y-m-d H:i:s');
    
        if ($this->insert($data)) {
            return $data['id'];
        }
        return false;
    }

    public function getMedia(){

    }

    public function insertMedia($media){

    }

    public function getPostByUser($id){
        $posts = $this->select("posts.*, profiles.firstname, profiles.lastname, users.avatar, CONCAT(profiles.firstname, ' ', profiles.lastname) as author_name, users.avatar as author_avatar, groups.name as group_name, groups.id as group_id")
                    ->join('profiles', 'profiles.id = posts.id_user')
                    ->join('users', 'users.id = posts.id_user')
                    ->join('group_posts', 'group_posts.post_id = posts.id', 'left')
                    ->join('groups', 'groups.id = group_posts.group_id', 'left')
                    ->where('posts.id_user', $id)
                    ->orderBy('posts.created_at', 'DESC')
                    ->findAll();
        return $this->attachMediaToPosts($posts);
    }

    public function countPostUser($id) {
        return $this->where('id_user', $id)->countAllResults();
    }

    public function getFeed($userId) {
        $posts = $this->select("posts.*, profiles.firstname, profiles.lastname, users.avatar, CONCAT(profiles.firstname, ' ', profiles.lastname) as author_name, users.avatar as author_avatar, groups.name as group_name, groups.id as group_id")
            ->join('profiles', 'profiles.id = posts.id_user')
            ->join('users', 'users.id = posts.id_user')
            ->join('group_posts', 'group_posts.post_id = posts.id', 'left')
            ->join('groups', 'groups.id = group_posts.group_id', 'left')
            ->groupStart()
                ->where('posts.id_user', $userId)
                ->orWhere("posts.id_user IN (SELECT receiver_id FROM friendships WHERE requester_id = '$userId' AND status = 'accepted')", null, false)
                ->orWhere("posts.id_user IN (SELECT requester_id FROM friendships WHERE receiver_id = '$userId' AND status = 'accepted')", null, false)
                // Include posts from joined groups
                ->orWhere("posts.id IN (SELECT post_id FROM group_posts WHERE group_id IN (SELECT group_id FROM group_members WHERE user_id = '$userId') AND status = 'accepted')", null, false)
            ->groupEnd()
            ->orderBy('posts.created_at', 'DESC')
            ->findAll();
        return $this->attachMediaToPosts($posts);
    }

    public function list() {
        $posts = $this->select("posts.*, profiles.firstname, profiles.lastname, users.avatar, CONCAT(profiles.firstname, ' ', profiles.lastname) as author_name, users.avatar as author_avatar, groups.name as group_name, groups.id as group_id")
                    ->join('profiles', 'profiles.id= posts.id_user')
                    ->join('users', 'users.id = posts.id_user')
                    ->join('group_posts', 'group_posts.post_id = posts.id', 'left')
                    ->join('groups', 'groups.id = group_posts.group_id', 'left')
                    ->orderBy('posts.created_at', 'DESC')
                    ->findAll();
        return $this->attachMediaToPosts($posts);
    }
    public function getPostsByGroup($groupId) {
        $posts = $this->select("posts.*, profiles.firstname, profiles.lastname, users.avatar, CONCAT(profiles.firstname, ' ', profiles.lastname) as author_name, users.avatar as author_avatar, groups.name as group_name, groups.id as group_id")
            ->join('group_posts', 'group_posts.post_id = posts.id')
            ->join('profiles', 'profiles.id = posts.id_user')
            ->join('users', 'users.id = posts.id_user')
            ->join('groups', 'groups.id = group_posts.group_id')
            ->where('group_posts.group_id', $groupId)
            ->where('group_posts.status', 'accepted')
            ->orderBy('posts.created_at', 'DESC')
            ->findAll();
        return $this->attachMediaToPosts($posts);
    }

    public function attachMediaToPosts(array $posts) {
        if (empty($posts)) return [];

        $mongo = new MongoDB();
        $mediaCollection = $mongo->getCollection('post_media');
        $commentCollection = $mongo->getCollection('comments');
        
        $db = \Config\Database::connect();
        $currentUserId = session()->get('user_id');

        foreach ($posts as &$post) {
            // Media
            $media = $mediaCollection->findOne(['entity_id' => $post['id']]);
            $post['media'] = $media ? json_decode(json_encode($media['items']), true) : []; 
            
            // Comment Count (Mongo)
            $post['comment_count'] = $commentCollection->countDocuments(['post_id' => $post['id']]);
            
            // Like Count (SQL)
            $post['like_count'] = $db->table('interations')->where('post_id', $post['id'])->where('type !=', 'unliked')->countAllResults();
            
            // My Reaction (SQL)
            $myReact = null;
            if ($currentUserId) {
                $reactRow = $db->table('interations')
                    ->where('post_id', $post['id'])
                    ->where('user_id', $currentUserId)
                    ->get()->getRowArray();
                if ($reactRow) $myReact = $reactRow['type'];
            }
            $post['my_reaction'] = $myReact;
        }

        return $posts;
    }

    public function createPost($data, $files = null, $groupId = null) {
        $db = \Config\Database::connect();
        $db->transStart();

        $postId = $this->create($data); // This uses the model's create method we defined earlier/inherited

        if ($postId) {
            // Handle Group Post
            if (!empty($groupId)) {
                $status = 'accepted';
                
                // Check group requirements
                $groupModel = new \App\Models\Group();
                $group = $groupModel->find($groupId);
                if ($group && !empty($group['post_requirement']) && $group['post_requirement'] == 1) {
                    $status = 'pending';
                }

                $db->table('group_posts')->insert([
                    'id' => Uuid::uuid4()->toString(),
                    'group_id' => $groupId,
                    'post_id' => $postId,
                    'status' => $status
                ]);
            }

            // Handle media upload
            if ($files && $files[0]->isValid()) {
                try {
                    $mediaService = new Media();
                    $mediaItems = $mediaService->uploadMultiple($files, 'posts');
                    $mediaService->storeMetadata($postId, $mediaItems, 'post_media');
                } catch (\Exception $e) {
                    log_message('error', 'Media upload failed: ' . $e->getMessage());
                }
            }
            
            $db->transComplete();
            return $db->transStatus() ? $postId : false;
        }
        
        $db->transRollback();
        return false;
    }

    public function deleteWithMedia($postId) {
        $mongo = new MongoDB();
        $mediaCollection = $mongo->getCollection('post_media');
        $mediaData = $mediaCollection->findOne(['post_id' => $postId]);

        if ($mediaData) {
            $cloudinary = new Cloudinary();
            foreach ($mediaData['items'] as $item) {
                // Check if public_id exists before trying to destroy
                if (isset($item['public_id'])) {
                    $cloudinary->destroy($item['public_id']);
                }
            }
            $mediaCollection->deleteOne(['post_id' => $postId]);
        }

        // Clean up group_posts
        $db = \Config\Database::connect();
        $db->table('group_posts')->where('post_id', $postId)->delete();

        return $this->delete($postId);
    }
}
