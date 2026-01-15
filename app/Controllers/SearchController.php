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
                $results['users'][] = [
                    'id' => $u['id'],
                    'name' => $u['firstname'] . ' ' . $u['lastname'],
                    'avatar' => $u['avatar'] ?? base_url('ICON/avatar-default.svg'),
                    'type' => 'user'
                ];
            }

            // Search groups
            $groups = $db->table('groups')
                ->select('id, name, avatar, privacy')
                ->like('name', $query)
                ->limit(20)
                ->get()->getResultArray();

            foreach ($groups as $g) {
                $results['groups'][] = [
                    'id' => $g['id'],
                    'name' => $g['name'],
                    'avatar' => $g['avatar'] ?? base_url('ICON/group-default.png'),
                    'privacy' => $g['privacy'],
                    'type' => 'group'
                ];
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
            $results[] = [
                'id' => $user['id'],
                'name' => $user['firstname'] . ' ' . $user['lastname'],
                'avatar' => $user['avatar'] ?? base_url('ICON/avatar-default.svg'),
                'type' => 'user',
                'url' => base_url('user/profile/' . $user['id'])
            ];
        }

        // Search groups (limit 5 for live search)
        $groups = $db->table('groups')
            ->select('id, name, avatar, privacy')
            ->like('name', $query)
            ->limit(5)
            ->get()->getResultArray();

        foreach ($groups as $group) {
            $results[] = [
                'id' => $group['id'],
                'name' => $group['name'],
                'avatar' => $group['avatar'] ?? base_url('ICON/group-default.png'),
                'type' => 'group',
                'privacy' => $group['privacy'],
                'url' => base_url('group/' . $group['id'])
            ];
        }

        return $this->response->setJSON(['status' => 'success', 'data' => $results]);
    }
}
