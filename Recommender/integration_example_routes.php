<?php
/**
 * Add these routes to your app/Config/Routes.php
 */

// Dashboard with AI recommendations
$routes->get('dashboard', 'DashboardController::index');

// API endpoint for AJAX requests
$routes->get('api/friend-suggestions', 'DashboardController::api_getFriendSuggestions');

// Optional: Endpoint to manually refresh recommendations
$routes->post('api/recommendations/refresh', 'RecommenderController::refreshRecommendations');
