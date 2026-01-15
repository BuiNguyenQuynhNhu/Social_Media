<?php
// Quick test - place this in your web root and access it
// Test: http://your-site.com/test_api_connection.php

echo "<h1>Testing Recommender API Connection</h1>";

$apiUrl = 'http://localhost:5000/health';

echo "<p><strong>Testing:</strong> $apiUrl</p>";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $apiUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 5);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
$info = curl_getinfo($ch);
curl_close($ch);

echo "<h2>Results:</h2>";
echo "<pre>";
echo "HTTP Code: $httpCode\n";
echo "Error: " . ($error ?: 'None') . "\n";
echo "Response: " . ($response ?: 'Empty') . "\n\n";
echo "Connection Info:\n";
print_r($info);
echo "</pre>";

if ($httpCode === 200) {
    echo "<p style='color: green; font-size: 20px;'>✓ SUCCESS! API is reachable from PHP!</p>";
    echo "<p>The issue might be with session/user_id. Check if you're logged in.</p>";
} else {
    echo "<p style='color: red; font-size: 20px;'>✗ FAILED! Cannot reach API from PHP</p>";
    echo "<h3>Possible solutions:</h3>";
    echo "<ol>";
    echo "<li>Make sure API server is running: <code>python simple_api_server.py</code></li>";
    echo "<li>Check if your PHP allows connections to localhost</li>";
    echo "<li>Try changing localhost to 127.0.0.1 in RecommenderController.php</li>";
    echo "<li>Check if firewall is blocking port 5000</li>";
    echo "</ol>";
}

// Test if user is logged in
echo "<h2>Session Check:</h2>";
session_start();
if (isset($_SESSION['user_id'])) {
    echo "<p style='color: green;'>✓ User is logged in: ID = {$_SESSION['user_id']}</p>";
} else {
    echo "<p style='color: red;'>✗ No user_id in session</p>";
    echo "<p>Make sure you're logged in to see recommendations!</p>";
}
?>
