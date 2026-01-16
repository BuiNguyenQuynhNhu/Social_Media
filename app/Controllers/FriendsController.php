<?php

namespace App\Controllers;

use App\Controllers\RecommenderController;

use App\Controllers\BaseController;
use App\Models\FriendShip;
use App\Models\User;
use App\Models\Profile;

class FriendsController extends BaseController
{
    protected $friendModel;
    protected $userModel;
    protected $userId;

    public function __construct()
    {
        $this->friendModel = new FriendShip();
        $this->userModel = new User();
        $this->userId = session()->get('user_id');
    }

    private function getUserData() {
        $user = $this->userModel->find($this->userId);
        $avatarUrl = (strpos($user['avatar'], 'http') === 0) 
            ? $user['avatar'] 
            : ($user['avatar'] ? base_url($user['avatar']) : base_url('ICON/avatar-default.svg'));
        return ['user' => $user, 'avatarUrl' => $avatarUrl, 'user_id' => $this->userId];
    }

    public function index()
    {
        if (!$this->userId) return redirect()->to('login');
        
        $data = $this->getUserData();
        $data['title'] = 'Friends';
        $data['requests'] = $this->friendModel->listReceive($this->userId);
        
        // Fetch Suggestions from Recommender
        $recommender = new RecommenderController();
        $recommendations = $recommender->getFriendRecommendations($this->userId);
        
        // Enrich recommendations with user profile data
        $suggestions = [];
        if (!empty($recommendations)) {
            $userModel = new User();
            foreach ($recommendations as $rec) {
                // Determine user ID (handle both API response format and fallback format)
                $recUserId = is_array($rec) ? ($rec['user_id'] ?? $rec['id'] ?? null) : $rec;
                
                if ($recUserId && $recUserId != $this->userId) {
                    $user = $userModel->select('users.id, users.avatar, profiles.firstname, profiles.lastname')
                                    ->join('profiles', 'profiles.id = users.id')
                                    ->where('users.id', $recUserId)
                                    ->first();
                    if ($user) {
                        $suggestions[] = $user;
                    }
                }
            }
        }
        $data['suggestions'] = $suggestions;

        $data['friends'] = $this->friendModel->listFriend($this->userId);
        
        // Pass all data to a combined view
        return view('friends/layout', [
            'content' => view('friends/combined', $data), 
            'active_tab' => 'all', 
            'avatarUrl' => $data['avatarUrl']
        ]);
    }

    // Keep these as API endpoints or specific redirects if needed
    public function requests() { return redirect()->to('friends#requests'); }
    public function suggestions() { return redirect()->to('friends#suggestions'); }
    public function list() { return redirect()->to('friends#list'); }
}
