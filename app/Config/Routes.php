<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

//Authentication
$routes->get('login', 'AuthController::loginView');
$routes->post('login', 'AuthController::login');
$routes->post('register', 'AuthController::register');

$routes->group('user', ['filter' => 'auth'], function($routes){
    $routes->get('profile', 'UserController::index');
    $routes->post('update_profile', 'UserController::updateProfile');
    $routes->post('change_password', 'UserController::changePassword');
    // $routes->post('upload_avatar', 'UserController::updateAvatar');
    $routes->post('delete_account', 'UserController::deleteAccount');
});
