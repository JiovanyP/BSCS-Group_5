<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use Laravel\Socialite\Facades\Socialite;

// Root shows login page
Route::get('/', function () {
    return view('login');   // resources/views/login.blade.php
})->name('login');

// Show login page
Route::get('/login', function () {
    return view('login');
})->name('login');

// Show register page
Route::get('/register', function () {
    return view('register');   // teammates already made register.blade.php
})->name('register');

// Dashboard page (from teammate’s version)
Route::get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard');

// Handle form submissions
Route::post('/register', [UserController::class, 'register'])->name('register.post'); 
Route::post('/logout', [UserController::class, 'logout'])->name('logout');
Route::post('/login', [UserController::class, 'login'])->name('login.post');

// Google login
Route::get('/auth/google', function () {
    return Socialite::driver('google')->redirect();
})->name('google.login');

Route::get('/auth/google/callback', function () {
    $googleUser = Socialite::driver('google')->user();

    // For now just dump the user details to test
    dd($googleUser);
});
