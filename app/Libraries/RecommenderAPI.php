<?php
/**
 * Friend Recommender API Client
 * Connects to the GNN Recommender API service
 */

namespace App\Libraries;

class RecommenderAPI
{
    private $apiBaseUrl;
    private $timeout = 5;
    
    public function __construct()
    {
        // API server URL (make sure this matches your server.py port)
        $this->apiBaseUrl = 'http://localhost:5000';
    }
    
    /**
     * Check if recommender API is healthy
     */
    public function healthCheck()
    {
        try {
            $response = $this->makeRequest('/health');
            return $response && isset($response['status']) && $response['status'] === 'healthy';
        } catch (Exception $e) {
            log_message('error', 'Recommender API health check failed: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get friend recommendations for a user
     * 
     * @param int $userId User ID to get recommendations for
     * @param int $topK Number of recommendations (default: 5)
     * @return array|null Recommendations or null on error
     */
    public function getFriendRecommendations($userId, $topK = 5)
    {
        try {
            $endpoint = "/api/recommendations/friends/{$userId}?top_k={$topK}";
            $response = $this->makeRequest($endpoint);
            
            if ($response && isset($response['success']) && $response['success']) {
                return $response['recommendations'];
            }
            
            return null;
        } catch (Exception $e) {
            log_message('error', 'Failed to get friend recommendations: ' . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Get API statistics
     */
    public function getStats()
    {
        try {
            $response = $this->makeRequest('/api/stats');
            
            if ($response && isset($response['success']) && $response['success']) {
                return $response['stats'];
            }
            
            return null;
        } catch (Exception $e) {
            log_message('error', 'Failed to get recommender stats: ' . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Update recommender graph with new data
     * 
     * @param array $users Array of user objects
     * @param array $friendships Array of friendship pairs
     * @return bool Success status
     */
    public function updateGraph($users, $friendships)
    {
        try {
            $data = [
                'users' => $users,
                'friendships' => $friendships
            ];
            
            $response = $this->makeRequest('/api/update-graph', 'POST', $data);
            
            return $response && isset($response['success']) && $response['success'];
        } catch (Exception $e) {
            log_message('error', 'Failed to update recommender graph: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Make HTTP request to API
     * 
     * @param string $endpoint API endpoint
     * @param string $method HTTP method (GET/POST)
     * @param array|null $data Request body for POST
     * @return array|null Response data or null on error
     */
    private function makeRequest($endpoint, $method = 'GET', $data = null)
    {
        $url = $this->apiBaseUrl . $endpoint;
        
        $ch = curl_init();
        
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        
        if ($method === 'POST' && $data !== null) {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        
        curl_close($ch);
        
        if ($error) {
            throw new \Exception("cURL error: {$error}");
        }
        
        if ($httpCode !== 200) {
            throw new \Exception("HTTP error {$httpCode}: {$response}");
        }
        
        return json_decode($response, true);
    }
}
