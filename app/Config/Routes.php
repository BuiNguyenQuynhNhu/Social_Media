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
    $routes->put('update_profile', 'UserController::updateProfile');
    $routes->put('change_password', 'UserController::changePassword');
    $routes->get('list', 'UserController::listAll');
    // $routes->post('upload_avatar', 'UserController::updateAvatar');
    $routes->delete('delete_account', 'UserController::deleteAccount');
});
