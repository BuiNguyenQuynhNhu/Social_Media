<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

//Authentication
$routes->get('login', 'AuthController::loginView');
$routes->post('login', 'AuthController::login');
$routes->get('logout', 'AuthController::logout');
$routes->post('register', 'AuthController::register');

$routes->group('user', ['filter' => 'auth'], function($routes){
    $routes->get('profile', 'UserController::index');
    $routes->get('profile/(:segment)', 'UserController::index/$1');
    $routes->post('update_profile', 'UserController::updateProfile');
    $routes->post('change_password', 'UserController::changePassword');
    // $routes->post('upload_avatar', 'UserController::updateAvatar');
    $routes->post('delete_account', 'UserController::deleteAccount');
});

$routes->group('admin', ['filter' => 'auth'], function($routes){
    $routes->get('list_user', 'UserController::listAll');
    
});

$routes->group('post', ['filter' => 'auth'], function($routes){
    // $routes->get('/', 'PostController::index');
    $routes->post('create', 'PostController::create');
    $routes->get('delete/(:num)', 'PostController::delete/$1');
    $routes->get('reactors/(:num)', 'PostController::getReactors/$1');
    
    $routes->post('react', 'PostController::react');         
    $routes->post('comment/add', 'PostController::addComment'); 
    $routes->get('comments/(:num)', 'PostController::getComments/$1');
    $routes->post('update/(:num)', 'PostController::update/$1'); 
    $routes->post('comment/delete', 'PostController::deleteComment');
    $routes->post('comment/update', 'PostController::updateComment');
    $routes->get('get_posts_paging', 'PostController::getPostsPaging');
});

$routes->group('friend', ['filter' => 'auth'], function($routes){
    $routes->post('add', 'FriendController::addFriend');       
    $routes->post('accept', 'FriendController::acceptFriend'); 
    $routes->post('remove', 'FriendController::removeFriend'); 
});
$routes->get('friends', 'FriendController::index', ['filter' => 'auth']);
$routes->get('search', 'UserController::search', ['filter' => 'auth']);
$routes->get('search/live', 'UserController::liveSearch', ['filter' => 'auth']);

$routes->get('dashboard', 'DashboardController::index');

$routes->get('chat', 'ChatController::index');
$routes->get('chat/with/(:num)', 'ChatController::chatWith/$1');

$routes->get('notifications/get', 'NotificationController::getLatest');
$routes->post('notifications/read', 'NotificationController::markRead');

$routes->post('notifications/read-item/(:any)', 'NotificationController::markItemRead/$1');
