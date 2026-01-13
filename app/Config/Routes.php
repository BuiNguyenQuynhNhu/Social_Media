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
    $routes->get('get/(:any)', 'MessageController::get/$1');
    $routes->post('send/(:any)', 'MessageController::sendMessage/$1');
    $routes->post('react/(:any)', 'MessageController::react/$1');
});

// Chats (Group Chats)
$routes->group('chat', ['filter' => 'auth'], function($routes){
    $routes->get('/', 'ChatController::index');
    $routes->get('list', 'ChatController::list');
    $routes->get('members/(:any)', 'ChatController::getMembers/$1');
    $routes->post('create', 'ChatController::create');
    $routes->post('invite/(:any)', 'ChatController::invite/$1');
    $routes->post('update/(:any)', 'ChatController::update/$1');
    $routes->post('nickname/(:any)', 'ChatController::updateNickname/$1');
    $routes->post('leave/(:any)', 'ChatController::leave/$1');
});

// Groups
$routes->group('group', ['filter' => 'auth'], function($routes){
    $routes->get('/', 'GroupController::index');
    $routes->get('create-view', 'GroupController::createView'); // View page
    $routes->get('my-groups', 'GroupController::myGroups');
    $routes->get('suggested', 'GroupController::suggested');
    $routes->get('(:any)', 'GroupController::show/$1');
    $routes->get('(:any)/members', 'GroupController::getMembers/$1');
    
    $routes->post('create', 'GroupController::create');
    $routes->post('(:any)/join', 'GroupController::join/$1');
    $routes->post('(:any)/leave', 'GroupController::leave/$1');
    $routes->post('(:any)/update', 'GroupController::update/$1');
    $routes->post('(:any)/privacy', 'GroupController::updatePrivacy/$1');
    $routes->post('(:any)/requirements', 'GroupController::setRequire/$1');
    $routes->delete('(:any)', 'GroupController::delete/$1');
    $routes->post('(:any)/promote/(:any)', 'GroupController::promote/$1/$2');
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
