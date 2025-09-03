<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PostController;
use Laravel\Socialite\Facades\Socialite;

// Homepage (feed + login/register options)
Route::get('/', function () {
    return view('login');
})->name('login');

// Routes for GUESTS only (not logged in users)
Route::middleware('guest')->group(function () {
    // Login page
    Route::get('/login', function () {
        return view('login');
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
        dd($googleUser); // replace with proper login/registration logic
    });
});

// Routes for AUTHENTICATED users only
Route::middleware('auth')->group(function () {
    // Dashboard page
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // Timeline routes
    Route::get('/timeline', [PostController::class, 'index'])->name('timeline');
    Route::post('/timeline', [PostController::class, 'store'])->name('timeline.store');

    // Logout
    Route::post('/logout', [UserController::class, 'logout'])->name('logout');
});

// Extra checks (these can be accessed by anyone)
Route::post('/check-email', [UserController::class, 'checkEmail']);
Route::post('/check-username', [UserController::class, 'checkUsername']);