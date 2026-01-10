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


//User
$routes->group('user', ['filter' => 'auth'], function($routes){
    $routes->get('dashboard', 'UserController::index');
    $routes->get('profile/(:any)', 'UserController::profile/$1');
    $routes->post('update_profile/(:any)', 'UserController::updateProfile/$1');
    $routes->post('change_password/(:any)', 'UserController::changePassword/$1');
    $routes->delete('delete_account/(:any)', 'UserController::deleteAccount/$1');
    $routes->get('list_friend/(:any)', 'FriendShipController::listFriend/$1');
    $routes->get('list_receive/(:any)', 'FriendShipController::listReceive/$1');
    $routes->get('list_request/(:any)', 'FriendShipController::listRequest/$1');
});

//Post
$routes->group('post', ['filter' => 'auth'], function($routes){
    $routes->post('create_post', 'PostController::create');
    $routes->delete('delete_post/(:any)', 'PostController::delete/$1');
});
// Friend Routes
$routes->group('friend', ['filter' => 'auth'], function($routes){
    $routes->get('/', 'FriendshipController::index');
    $routes->get('requests', 'FriendshipController::requests');
    $routes->get('sent', 'FriendshipController::sent');
    $routes->get('add/(:any)', 'FriendshipController::send/$1');
    $routes->get('accept/(:any)', 'FriendshipController::accept/$1');
    $routes->get('remove/(:any)', 'FriendshipController::remove/$1');
});
$routes->get('user/friends', 'FriendshipController::index', ['filter' => 'auth']); // Alias

// Interactions
$routes->group('interaction', ['filter' => 'auth'], function($routes){
    $routes->post('react/(:any)', 'InteractionController::react/$1');
    $routes->get('get_likes/(:any)', 'InteractionController::getLikes/$1');
});

// Comments
$routes->group('comment', ['filter' => 'auth'], function($routes){
    $routes->post('add/(:any)', 'CommentController::add/$1');
    $routes->delete('delete/(:any)', 'CommentController::delete/$1');
    $routes->post('like/(:any)', 'CommentController::like/$1');
    $routes->get('get/(:any)', 'CommentController::getComments/$1');
});

// Messages
$routes->group('message', ['filter' => 'auth'], function($routes){
    $routes->get('/', 'MessageController::index');
    $routes->get('conversations', 'MessageController::getConversations');
    $routes->get('get/(:any)', 'MessageController::getMessages/$1');
    $routes->post('send/(:any)', 'MessageController::sendMessage/$1');
    $routes->delete('delete/(:any)', 'MessageController::deleteMessage/$1');
});

//Super Admin
$routes->group('admin', ['filter' => 'auth'], function($routes){
    $routes->get('profile/(:any)', 'UserController::profile/$1');
    $routes->get('dashboard', 'AdminController::index');
    $routes->get('list_user', 'AdminController::listUser');
    $routes->get('list_post', 'AdminController::listPost');
    $routes->get('list_group', 'AdminController::listGroup');
    $routes->get('list_chat', 'AdminController::listChat');
});
