<?php
header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = explode('/', trim($uri, '/')); 
$input = json_decode(file_get_contents('php://input'), true);

// ROUTING
if ($uri[0] !== 'users') {
    http_response_code(404);
    echo json_encode(['message' => 'Not Found']);
    exit;
}

// GET /users
if ($method === 'GET' && count($uri) === 1) {
    echo json_encode([
        'message' => 'Returning list of users'
    ]);
    exit;
}

// POST /users
if ($method === 'POST' && count($uri) === 1) {
    echo json_encode([
        'message' => 'User created',
        'user' => $input
    ]);
    exit;
}

// PUT /users/{id}
if ($method === 'PUT' && count($uri) === 2) {
    echo json_encode([
        'message' => "User with ID {$uri[1]} updated",
        'updatedUser' => $input
    ]);
    exit;
}

// DELETE /users/{id}
if ($method === 'DELETE' && count($uri) === 2) {
    echo json_encode([
        'message' => "User with ID {$uri[1]} deleted"
    ]);
    exit;
}

// Không match route
http_response_code(405);
echo json_encode(['message' => 'Method Not Allowed']);
