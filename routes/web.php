<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PostController;
use Laravel\Socialite\Facades\Socialite;

// Homepage (redirect guests to login, later you can show feed if logged in)
Route::get('/', function () {
    return view('login');
})->name('login');

// Routes for GUESTS only (not logged-in users)
Route::middleware('guest')->group(function () {
    // Login page
    Route::get('/login', function () {
        return view('login');  // resources/views/login.blade.php
    })->name('login');

    // Register page
    Route::get('/register', function () {
        return view('register'); 
    })->name('register');

    // Auth POST routes
    Route::post('/register', [UserController::class, 'register'])->name('register.post'); 
    Route::post('/login', [UserController::class, 'login'])->name('login.post');

    // Google OAuth
    Route::get('/auth/google', function () {
        return Socialite::driver('google')->redirect();
    })->name('google.login');

    Route::get('/auth/google/callback', function () {
        $googleUser = Socialite::driver('google')->user();
        dd($googleUser); // TODO: replace with proper login/registration logic
    });
});
