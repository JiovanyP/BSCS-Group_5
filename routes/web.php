<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PostController;
use Laravel\Socialite\Facades\Socialite;

// Homepage (feed + login/register options)
Route::get('/', function () {
    return view('home');   // resources/views/home.blade.php
})->name('home');

// Login page
Route::get('/login', function () {
    return view('login');  // resources/views/login.blade.php
})->name('login');

// Register page
Route::get('/register', function () {
    return view('register'); 
})->name('register');

// Dashboard page (protected)
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware('auth')->name('dashboard');

// Auth routes
Route::post('/register', [UserController::class, 'register'])->name('register.post'); 
Route::post('/login', [UserController::class, 'login'])->name('login.post'); 
Route::post('/logout', [UserController::class, 'logout'])->name('logout');

// Timeline routes (only for logged in users)
Route::middleware(['auth'])->group(function () {
    Route::get('/timeline', [PostController::class, 'index'])->name('timeline');
    Route::post('/timeline', [PostController::class, 'store'])->name('timeline.store');
});

// Extra checks
Route::post('/check-email', [UserController::class, 'checkEmail']);
Route::post('/check-username', [UserController::class, 'checkUsername']);

// Google OAuth
Route::get('/auth/google', function () {
    return Socialite::driver('google')->redirect();
})->name('google.login');

Route::get('/auth/google/callback', function () {
    $googleUser = Socialite::driver('google')->user();
    dd($googleUser); // replace with proper login/registration logic
});

