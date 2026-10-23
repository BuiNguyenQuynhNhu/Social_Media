<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;

#Home
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/profile', function () { return view('pages.profile'); })->name('profile');

#Group
Route::get('/groups', function () { return view('pages.group.group'); })->name('groups');

#Authentication
Route::get('/register', function(){return view('pages.auth.register');})->name('register.form');
Route::post('/register', [RegisterController::class, 'store'])->name('register');
Route::get('/login', function(){return view('pages.auth.login');})->name('login.form');
Route::post('/login', function(){return redirect()->route('login.form');})->name('login');
Route::get('/forgot-password', function(){return view('pages.auth.forgot-password');})->name('password.request');