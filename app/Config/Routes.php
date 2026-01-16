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

// Search
$routes->group('search', ['filter' => 'auth'], function($routes){
    $routes->get('/', 'SearchController::index');
    $routes->get('live', 'SearchController::liveSearch');
});
// Message Routes
$routes->group('message', ['filter' => 'auth'], function($routes){
    $routes->get('/', 'MessageController::index');
    $routes->get('conversations', 'MessageController::getConversations');
    $routes->get('chat/(:any)', 'MessageController::getMessages/$1');
    $routes->post('send', 'MessageController::send');
    $routes->post('mark-read/(:any)', 'MessageController::markAsRead/$1');
});

// Notifications
$routes->group('notifications', ['filter' => 'auth'], function($routes){
    $routes->get('/', 'NotificationController::index');
    $routes->post('read/(:any)', 'NotificationController::markRead/$1');

});


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
    $routes->post('update_post/(:any)', 'PostController::update/$1');
    $routes->post('delete_post/(:any)', 'PostController::delete/$1');
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

// Dedicated Friends Page Routes
$routes->group('friends', ['filter' => 'auth'], function($routes){
    $routes->get('/', 'FriendsController::index');
    $routes->get('requests', 'FriendsController::requests');
    $routes->get('suggestions', 'FriendsController::suggestions');
    $routes->get('list', 'FriendsController::list');
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
    
    // API route for getting members data (must come before view route)
    $routes->get('(:any)/members/data', 'GroupController::getMembers/$1');
    
    $routes->get('(:any)/feed', 'GroupController::feed/$1');
    $routes->get('(:any)/members', 'GroupController::membersView/$1');
    $routes->get('(:any)/about', 'GroupController::aboutView/$1');
    // Manage Routes (Admin/Owner)
    $routes->get('(:any)/manage/overview', 'GroupManageController::overview/$1');
    $routes->get('(:any)/manage/members', 'GroupManageController::members/$1');
    $routes->get('(:any)/manage/posts', 'GroupManageController::posts/$1');
    $routes->get('(:any)/manage/requirements', 'GroupManageController::requirements/$1');
    $routes->get('(:any)/manage', 'GroupManageController::manage/$1');
    
    $routes->get('(:any)/settings', 'GroupManageController::settings/$1');
    $routes->post('(:any)/update', 'GroupManageController::update/$1');
    $routes->post('update_privacy/(:any)', 'GroupManageController::updatePrivacy/$1');
    $routes->post('update_post_requirements/(:any)', 'GroupManageController::updatePostRequirements/$1');
    $routes->post('(:any)/requirements', 'GroupManageController::setRequire/$1');
    $routes->post('(:any)/delete', 'GroupManageController::delete/$1'); // POST for body payload support
    
    $routes->post('(:any)/promote/(:any)', 'GroupManageController::promote/$1/$2');
    $routes->post('(:any)/kick/(:any)', 'GroupManageController::kickMember/$1/$2');
    $routes->post('(:any)/approve_member/(:any)', 'GroupManageController::approveMember/$1/$2');
    $routes->post('(:any)/reject_member/(:any)', 'GroupManageController::rejectMember/$1/$2');
    
    $routes->post('approve_post/(:any)', 'GroupManageController::approvePost/$1');
    $routes->post('reject_post/(:any)', 'GroupManageController::rejectPost/$1');
    
    $routes->post('(:any)/join', 'GroupController::join/$1');
    $routes->post('(:any)/leave', 'GroupController::leave/$1');

    $routes->get('(:any)', 'GroupController::show/$1'); // Redirects to feed
});

//Super Admin
$routes->group('admin', ['filter' => 'auth'], function($routes){
    $routes->get('profile/(:any)', 'UserController::profile/$1');
    $routes->get('dashboard', 'AdminController::index');
    $routes->get('list_user', 'AdminController::listUser');
    $routes->get('list_post', 'AdminController::listPost');
    $routes->get('list_group', 'AdminController::listGroup');
    $routes->get('list_chat', 'AdminController::listChat');
    
    // Admin Actions
    $routes->post('ban_user/(:any)', 'AdminController::banUser/$1');
    $routes->post('unban_user/(:any)', 'AdminController::unbanUser/$1');
    $routes->post('warn_user/(:any)', 'AdminController::warnUser/$1');
    $routes->post('delete_post/(:any)', 'AdminController::deletePost/$1');
    $routes->post('delete_group/(:any)', 'AdminController::deleteGroup/$1');
    
    // Ban Appeal System
    $routes->get('appeals', 'AdminController::listAppeals');
    $routes->post('accept_appeal/(:any)', 'AdminController::acceptAppeal/$1');
    $routes->post('reject_appeal/(:any)', 'AdminController::rejectAppeal/$1');
    $routes->post('check_expired_bans', 'AdminController::checkExpiredBans');
    $routes->get('check_triggers', 'AdminController::checkTriggers');
});

// User Appeal Route (for banned users)
$routes->get('appeal/view', 'UserController::appealView');
$routes->post('appeal/submit', 'AdminController::submitAppeal');

$routes->group('post', ['filter' => 'auth'], function($routes){
    // $routes->get('/', 'PostController::index');
    $routes->post('create', 'PostController::create');
    $routes->get('delete/(:any)', 'PostController::delete/$1');
    $routes->get('reactors/(:any)', 'PostController::getReactors/$1');
    
    $routes->post('react', 'PostController::react');         
    $routes->post('comment/add', 'PostController::addComment'); 
    $routes->get('comments/(:any)', 'PostController::getComments/$1');
    $routes->post('update/(:any)', 'PostController::update/$1'); 
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




