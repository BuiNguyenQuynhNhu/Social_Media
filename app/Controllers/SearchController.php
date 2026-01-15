<?php

namespace App\Controllers;

use App\Models\User;
use App\Models\Group;

class SearchController extends BaseController
{
    protected $userId;

    public function __construct()
    {
        $this->userId = session()->get('user_id');
    }

    public function index()
    {
        if (!$this->userId) return redirect()->to('/login');

        $query = $this->request->getGet('q');
        $db = \Config\Database::connect();
        
        $user = $db->table('users')->where('id', $this->userId)->get()->getRowArray();
        $avatarUrl = $user['avatar'] ?? base_url('ICON/avatar-default.png');

        $results = [
            'users' => [],
            'groups' => [],
            'posts' => [],
            'query' => $query
        ];

        if (!empty($query)) {
            // Search users
            $users = $db->table('users')
                ->select('users.id, users.avatar, profiles.firstname, profiles.lastname')
                ->join('profiles', 'profiles.id = users.id')
                ->like('profiles.firstname', $query)
                ->orLike('profiles.lastname', $query)
                ->limit(20)
                ->get()->getResultArray();

            foreach ($users as $u) {
                // Check friend status
                $isFriend = $db->table('friendships')
                    ->groupStart()
                        ->groupStart()
                            ->where('requester_id', $this->userId)
                            ->where('receiver_id', $u['id'])
                        ->groupEnd()
                        ->orGroupStart()
                            ->where('requester_id', $u['id'])
                            ->where('receiver_id', $this->userId)
                        ->groupEnd()
                    ->groupEnd()
                    ->where('status', 'accepted')
                    ->countAllResults() > 0;
                
                $isRequested = $db->table('friendships')
                    ->where('requester_id', $this->userId)
                    ->where('receiver_id', $u['id'])
                    ->where('status', 'pending')
                    ->countAllResults() > 0;

                $hasRequested = $db->table('friendships')
                    ->where('requester_id', $u['id'])
                    ->where('receiver_id', $this->userId)
                     ->where('status', 'pending')
                    ->countAllResults() > 0;

                $status = 'none';
                if ($u['id'] === $this->userId) $status = 'self';
                else if ($isFriend) $status = 'friend';
                else if ($isRequested) $status = 'pending';
                else if ($hasRequested) $status = 'incoming';

                // Fix avatar
                $avatar = $u['avatar'] ?? '';
                if (empty($avatar)) {
                    $avatar = base_url('ICON/avatar-default.svg');
                } elseif (strpos($avatar, 'http') !== 0) {
                    $avatar = base_url($avatar);
                }

                $results['users'][] = [
                    'id' => $u['id'],
                    'name' => $u['firstname'] . ' ' . $u['lastname'],
                    'avatar' => $avatar,
                    'type' => 'user',
                    'url' => base_url('user/profile/' . $u['id']),
                    'status' => $status
                ];
            }

            // Search groups
            $groups = $db->table('groups')
                ->select('id, name, avatar, privacy')
                ->like('name', $query)
                ->limit(20)
                ->get()->getResultArray();

            foreach ($groups as $g) {
                // Fix avatar
                $avatar = $g['avatar'] ?? '';
                if (empty($avatar)) {
                    $avatar = base_url('ICON/group-default.png');
                } elseif (strpos($avatar, 'http') !== 0) {
                    $avatar = base_url($avatar);
                }

                $results['groups'][] = [
                    'id' => $g['id'],
                    'name' => $g['name'],
                    'avatar' => $avatar,
                    'privacy' => $g['privacy'],
                    'type' => 'group',
                    'url' => base_url('group/' . $g['id'])
                ];
            }

            // Search posts
            /*
             * Optimization: limit search to public posts or friend posts if possible.
             * For now, simplistic search on content.
             */
            $postModel = new \App\Models\Post();
            $posts = $postModel->select("posts.*, profiles.firstname, profiles.lastname, users.avatar")
                ->join('profiles', 'profiles.id = posts.id_user')
                ->join('users', 'users.id = posts.id_user')
                ->like('posts.content', $query)
                ->orderBy('posts.created_at', 'DESC')
                ->limit(20)
                ->findAll();

            // Attach media/counts (reusing model function)
            $posts = $postModel->attachMediaToPosts($posts);

            // Refine for display
             foreach ($posts as $p) {
                 $results['posts'][] = $p;
             }
        }

        return view('search/results', [
            'user_id' => $this->userId,
            'avatarUrl' => $avatarUrl,
            'results' => $results
        ]);
    }

    public function liveSearch()
    {
        if (!$this->userId) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Unauthorized']);
        }

        $query = $this->request->getGet('q');
        
        if (empty($query) || strlen($query) < 2) {
            return $this->response->setJSON(['status' => 'success', 'data' => []]);
        }

        $db = \Config\Database::connect();
        $results = [];

        // Search users (limit 5 for live search)
        $users = $db->table('users')
            ->select('users.id, users.avatar, profiles.firstname, profiles.lastname')
            ->join('profiles', 'profiles.id = users.id')
            ->groupStart()
                ->like('profiles.firstname', $query)
                ->orLike('profiles.lastname', $query)
            ->groupEnd()
            ->limit(5)
            ->get()->getResultArray();

        foreach ($users as $user) {
            // Check friend status
            $isFriend = $db->table('friendships')
                ->groupStart()
                    ->groupStart()
                        ->where('requester_id', $this->userId)
                        ->where('receiver_id', $user['id'])
                    ->groupEnd()
                    ->orGroupStart()
                        ->where('requester_id', $user['id'])
                        ->where('receiver_id', $this->userId)
                    ->groupEnd()
                ->groupEnd()
                ->where('status', 'accepted')
                ->countAllResults() > 0;
            
            $isRequested = $db->table('friendships')
                ->where('requester_id', $this->userId)
                ->where('receiver_id', $user['id'])
                ->where('status', 'pending')
                ->countAllResults() > 0;

            $hasRequested = $db->table('friendships')
                ->where('requester_id', $user['id'])
                ->where('receiver_id', $this->userId)
                 ->where('status', 'pending')
                ->countAllResults() > 0;

            $status = 'none';
            if ($user['id'] === $this->userId) $status = 'self';
            else if ($isFriend) $status = 'friend';
            else if ($isRequested) $status = 'pending';
            else if ($hasRequested) $status = 'incoming';

            // Fix avatar URL
            $avatar = $user['avatar'];
            if (empty($avatar)) {
                $avatar = base_url('ICON/avatar-default.svg');
            } elseif (strpos($avatar, 'http') !== 0) {
                $avatar = base_url($avatar);
            }

            $results[] = [
                'id' => $user['id'],
                'name' => $user['firstname'] . ' ' . $user['lastname'],
                'avatar' => $avatar,
                'type' => 'user',
                'url' => base_url('user/profile/' . $user['id']),
                'status' => $status
            ];
        }

        // Search groups (limit 5 for live search)
        $groups = $db->table('groups')
            ->select('id, name, avatar, privacy')
            ->like('name', $query)
            ->limit(5)
            ->get()->getResultArray();

        foreach ($groups as $group) {
            // Fix group avatar
            $avatar = $group['avatar'];
            if (empty($avatar)) {
                $avatar = base_url('ICON/group-default.png');
            } elseif (strpos($avatar, 'http') !== 0) {
                $avatar = base_url($avatar);
            }

            $results[] = [
                'id' => $group['id'],
                'name' => $group['name'],
                'avatar' => $avatar,
                'type' => 'group',
                'privacy' => $group['privacy'],
                'url' => base_url('group/' . $group['id'])
            ];
        }

        return $this->response->setJSON(['status' => 'success', 'data' => $results]);
    }
}
