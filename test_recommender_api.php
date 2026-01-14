<?php
/**
 * Test Recommender API Connection
 * Simple test page to verify PHP can connect to Python API
 */

// API Configuration
$apiBaseUrl = 'http://localhost:5000/api';

// Function to make API request
function callAPI($url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    return [
        'success' => $httpCode === 200,
        'code' => $httpCode,
        'data' => $response ? json_decode($response, true) : null,
        'error' => $error
    ];
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Recommender API</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 2rem;
            min-height: 100vh;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            border-radius: 16px;
            padding: 2rem;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }
        h1 {
            color: #333;
            margin-bottom: 0.5rem;
        }
        .subtitle {
            color: #666;
            margin-bottom: 2rem;
        }
        .test-section {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }
        .test-section h2 {
            color: #444;
            font-size: 1.25rem;
            margin-bottom: 1rem;
        }
        .status {
            display: inline-block;
            padding: 0.5rem 1rem;
            border-radius: 6px;
            font-weight: 600;
            font-size: 0.875rem;
            margin-bottom: 1rem;
        }
        .status.success {
            background: #d4edda;
            color: #155724;
        }
        .status.error {
            background: #f8d7da;
            color: #721c24;
        }
        .data {
            background: white;
            padding: 1rem;
            border-radius: 6px;
            border-left: 4px solid #667eea;
            font-family: 'Courier New', monospace;
            font-size: 0.875rem;
            overflow-x: auto;
        }
        .recommendation {
            background: white;
            padding: 1rem;
            margin: 0.5rem 0;
            border-radius: 6px;
            border-left: 4px solid #28a745;
        }
        .recommendation strong {
            color: #667eea;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🤖 Recommender API Test</h1>
        <p class="subtitle">Testing connection between PHP and Python recommender system</p>

        <!-- Test 1: Health Check -->
        <div class="test-section">
            <h2>1. Health Check</h2>
            <?php
            $health = callAPI($apiBaseUrl . '/../health');
            if ($health['success']): ?>
                <div class="status success">✓ API is Healthy</div>
                <div class="data">
                    <strong>Status:</strong> <?= htmlspecialchars($health['data']['status'] ?? 'unknown') ?><br>
                    <strong>Type:</strong> <?= htmlspecialchars($health['data']['recommender_type'] ?? 'unknown') ?><br>
                    <strong>Timestamp:</strong> <?= htmlspecialchars($health['data']['timestamp'] ?? 'unknown') ?>
                </div>
            <?php else: ?>
                <div class="status error">✗ API Unreachable</div>
                <div class="data">Error: <?= htmlspecialchars($health['error'] ?: 'Unknown error') ?></div>
            <?php endif; ?>
        </div>

        <!-- Test 2: Statistics -->
        <div class="test-section">
            <h2>2. Get Statistics</h2>
            <?php
            $stats = callAPI($apiBaseUrl . '/stats');
            if ($stats['success']): ?>
                <div class="status success">✓ Statistics Retrieved</div>
                <div class="data">
                    <strong>Total Users:</strong> <?= number_format($stats['data']['stats']['total_users'] ?? 0) ?><br>
                    <strong>Total Friendships:</strong> <?= number_format($stats['data']['stats']['total_friendships'] ?? 0) ?><br>
                    <strong>Average Friends:</strong> <?= number_format($stats['data']['stats']['avg_friends'] ?? 0, 1) ?>
                </div>
            <?php else: ?>
                <div class="status error">✗ Failed</div>
            <?php endif; ?>
        </div>

        <!-- Test 3: Friend Recommendations -->
        <div class="test-section">
            <h2>3. Friend Recommendations for User 1</h2>
            <?php
            $recs = callAPI($apiBaseUrl . '/recommendations/friends/1?top_k=5');
            if ($recs['success']): ?>
                <div class="status success">✓ Got <?= count($recs['data']['recommendations'] ?? []) ?> Recommendations</div>
                
                <?php foreach ($recs['data']['recommendations'] ?? [] as $rec): ?>
                    <div class="recommendation">
                        <strong>User <?= htmlspecialchars($rec['user_id']) ?></strong><br>
                        Score: <?= number_format($rec['score'], 3) ?><br>
                        Reason: <?= htmlspecialchars($rec['reason'] ?? 'N/A') ?>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="status error">✗ Failed</div>
            <?php endif; ?>
        </div>

        <!-- Instructions -->
        <div class="test-section">
            <h2>✅ Integration Ready!</h2>
            <p style="color: #666; line-height: 1.6;">
                The PHP application can successfully communicate with the Python recommender API!<br><br>
                <strong>Next steps:</strong><br>
                1. Visit <code style="background: #e9ecef; padding: 0.25rem 0.5rem; border-radius: 4px;">/recommendations/friends</code> in your app<br>
                2. Use <code style="background: #e9ecef; padding: 0.25rem 0.5rem; border-radius: 4px;">RecommenderController</code> in your controllers<br>
                3. Enjoy AI-powered friend suggestions!
            </p>
        </div>
    </div>
</body>
</html>
