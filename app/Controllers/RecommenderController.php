<?php

namespace App\Controllers;

use App\Models\User;
use App\Models\FriendShip;

class RecommenderController extends BaseController
{
    private $apiBaseUrl = 'http://localhost:5000/api';
    
    /**
     * Check if user is logged in
     */
    protected function isLoggedIn()
    {
        return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
    }
    
    /**
     * Load a view
     */
    protected function view($viewPath, $data = [])
    {
        return view($viewPath, $data);
    }
    
    /**
     * Display recommendations overview page
     */
    public function index()
    {
        if (!$this->isLoggedIn()) {
            redirect('login');
            return;
        }
        
        $data = [
            'title' => 'AI Recommendations'
        ];
        
        $this->view('recommendations/index', $data);
    }
    
    
    /**
     * Get friend recommendations for a user
     * 
     * @param int $userId
     * @param int $topK Number of recommendations (default: 10)
     * @return array
     */
    public function getFriendRecommendations($userId, $topK = 5)
    {
        try {
            $url = $this->apiBaseUrl . "/recommendations/friends/{$userId}?top_k={$topK}";
            
            $response = $this->makeApiRequest($url, 'GET');
            
            if ($response && $response['success'] && !empty($response['recommendations'])) {
                return $response['recommendations'];
            }
        } catch (\Exception $e) {
            error_log("Error getting friend recommendations: " . $e->getMessage());
        }

        // Fallback: Return random users
        $userModel = new User();
        // Get generic list of users (e.g. latest or random)
        $users = $userModel->orderBy('created_at', 'DESC')->findAll($topK * 2);
        
        $fallback = [];
        foreach ($users as $u) {
            if ($u['id'] == $userId) continue;
            $fallback[] = [
                'user_id' => $u['id'],
                'score' => 0.1, // Low score
                'reason' => 'Popular usage'
            ];
        }
        return $fallback;
    }


    
    /**
     * Display friend recommendations page
     */
    public function showFriendRecommendations()
    {
        if (!$this->isLoggedIn()) {
            redirect('login');
            return;
        }
        
        $userId = $_SESSION['user_id'];
        $recommendations = $this->getFriendRecommendations($userId, 20); // Get more to filter
        
        // Get existing friends and pending requests to filter out
        $friendshipModel = new FriendShip();
        
        // Get all existing friends using the model's method
        $existingFriends = $friendshipModel->listFriend($userId);
        
        // Get pending sent requests
        $pendingSent = $friendshipModel->listSend($userId);
        
        // Get pending received requests
        $pendingReceived = $friendshipModel->listReceive($userId);
        
        // Create a set of user IDs to exclude
        $excludeIds = [$userId]; // Exclude self
        
        // Add existing friends to exclude list
        foreach ($existingFriends as $friend) {
            $excludeIds[] = $friend['id'];
        }
        
        // Add pending sent requests to exclude list
        foreach ($pendingSent as $request) {
            $excludeIds[] = $request['id'];
        }
        
        // Add pending received requests to exclude list
        foreach ($pendingReceived as $request) {
            $excludeIds[] = $request['id'];
        }
        
        $excludeIds = array_unique($excludeIds);
        
        // Get user details for each recommendation and filter
        $userModel = new User();
        $recommendedUsers = [];
        
        foreach ($recommendations as $rec) {
            // Skip if user should be excluded
            if (in_array($rec['user_id'], $excludeIds)) {
                continue;
            }
            
            $user = $userModel->find($rec['user_id']);
            if ($user) {
                $user['score'] = $rec['score'];
                $user['reason'] = $rec['reason'] ?? 'Recommended for you';
                $recommendedUsers[] = $user;
                
                // Stop when we have enough recommendations
                if (count($recommendedUsers) >= 10) {
                    break;
                }
            }
        }
        
        $data = [
            'title' => 'Friend Recommendations',
            'recommendations' => $recommendedUsers
        ];
        
        $this->view('recommendations/friends', $data);
    }
    
    /**
     * Get post recommendations for a user
     * 
     * @param int $userId
     * @param array $posts Available posts to recommend from
     * @param int $topK Number of recommendations (default: 20)
     * @return array
     */
    public function getPostRecommendations($userId, $posts, $topK = 20)
    {
        try {
            $url = $this->apiBaseUrl . "/recommendations/posts/{$userId}";
            
            $requestData = [
                'posts' => $posts,
                'top_k' => $topK
            ];
            
            $response = $this->makeApiRequest($url, 'POST', $requestData);
            
            if ($response && $response['success']) {
                return $response['recommendations'];
            }
            
            return [];
        } catch (\Exception $e) {
            error_log("Error getting post recommendations: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get group recommendations for a user
     * 
     * @param int $userId
     * @param array $groups Available groups to recommend from
     * @param int $topK Number of recommendations (default: 10)
     * @return array
     */
    public function getGroupRecommendations($userId, $groups, $topK = 10)
    {
        try {
            $url = $this->apiBaseUrl . "/recommendations/groups/{$userId}";
            
            $requestData = [
                'groups' => $groups,
                'top_k' => $topK
            ];
            
            $response = $this->makeApiRequest($url, 'POST', $requestData);
            
            if ($response && $response['success']) {
                return $response['recommendations'];
            }
            
            return [];
        } catch (\Exception $e) {
            error_log("Error getting group recommendations: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Update the recommendation engine with latest data
     */
    public function updateGraph()
    {
        try {
            // Fetch all users
            $userModel = new User();
            $users = $userModel->all();
            
            // Transform user data for the recommender
            $userData = [];
            foreach ($users as $user) {
                $userData[] = [
                    'id' => $user['id'],
                    'age' => $this->calculateAge($user['birth_date'] ?? null),
                    'account_age_days' => $this->getAccountAgeDays($user['created_at']),
                    'post_count' => $this->getPostCount($user['id']),
                    'friend_count' => $this->getFriendCount($user['id']),
                    'comment_count' => $this->getCommentCount($user['id']),
                    'like_count' => $this->getLikeCount($user['id']),
                    'is_verified' => $user['is_verified'] ?? false,
                    'has_profile_pic' => !empty($user['profile_picture']),
                    'gender' => $user['gender'] ?? 'other',
                    'interests' => $this->getUserInterests($user['id'])
                ];
            }
            
            // Fetch all friendships
            $friendshipModel = new FriendShip();
            $friendships = $friendshipModel->all();
            
            // Transform friendship data
            $friendshipData = [];
            foreach ($friendships as $friendship) {
                if ($friendship['status'] === 'accepted') {
                    $friendshipData[] = [$friendship['user_id'], $friendship['friend_id']];
                }
            }
            
            // Send update to API
            $url = $this->apiBaseUrl . "/update-graph";
            $requestData = [
                'users' => $userData,
                'friendships' => $friendshipData
            ];
            
            $response = $this->makeApiRequest($url, 'POST', $requestData);
            
            return $response;
        } catch (\Exception $e) {
            error_log("Error updating graph: " . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
    
    /**
     * Make HTTP request to the Python API
     * 
     * @param string $url
     * @param string $method HTTP method (GET, POST, etc.)
     * @param array $data Request data for POST requests
     * @return array|null
     */
    private function makeApiRequest($url, $method = 'GET', $data = null)
    {
        $ch = curl_init();
        
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        
        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            if ($data) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
                curl_setopt($ch, CURLOPT_HTTPHEADER, [
                    'Content-Type: application/json',
                    'Content-Length: ' . strlen(json_encode($data))
                ]);
            }
        }
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
        curl_close($ch);
        
        if ($httpCode === 200 && $response) {
            return json_decode($response, true);
        }
        
        return null;
    }
    
    /**
     * Helper functions to get user statistics
     */
    
    private function calculateAge($birthDate)
    {
        if (!$birthDate) return 25; // Default age
        $dob = new \DateTime($birthDate);
        $now = new \DateTime();
        return $now->diff($dob)->y;
    }
    
    private function getAccountAgeDays($createdAt)
    {
        if (!$createdAt) return 0;
        $created = new \DateTime($createdAt);
        $now = new \DateTime();
        return $now->diff($created)->days;
    }
    
    private function getPostCount($userId)
    {
        $postModel = new \App\Models\Post();
        return $postModel->countPostUser($userId);
    }
    
    private function getFriendCount($userId)
    {
        $friendshipModel = new FriendShip();
        return $friendshipModel->where('(requester_id = "'.$userId.'" OR receiver_id = "'.$userId.'") AND status = "accepted"')->countAllResults();
    }
    
    private function getCommentCount($userId)
    {
        try {
            $mongo = new \App\Libraries\MongoDB();
            $collection = $mongo->getCollection('comments');
            return $collection->countDocuments(['user_id' => $userId]);
        } catch (\Exception $e) {
            return 0;
        }
    }
    
    private function getLikeCount($userId)
    {
        $db = \Config\Database::connect();
        return $db->table('interations')->where('user_id', $userId)->countAllResults();
    }
    
    private function getUserInterests($userId)
    {
        return []; 
    }
}
