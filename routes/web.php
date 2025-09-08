<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PostController;
use Laravel\Socialite\Facades\Socialite;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

// Homepage (feed + login/register options)
Route::get('/', function () {
    return view('home');   // resources/views/home.blade.php
})->name('home');

// Login + Register pages (GET)
Route::view('/login', 'login')->name('login');
Route::view('/register', 'register')->name('register');

// Auth actions (POST)
Route::post('/register', [UserController::class, 'register'])->name('register.post'); 
Route::post('/login', [UserController::class, 'login'])->name('login.post'); 
Route::post('/logout', [UserController::class, 'logout'])->name('logout');

// AJAX checks (optional for live validation)
Route::post('/check-email', [UserController::class, 'checkEmail'])->name('check.email');
Route::post('/check-username', [UserController::class, 'checkUsername'])->name('check.username');

/*
|--------------------------------------------------------------------------
| Timeline / Post Routes (Protected)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {

    // Dashboard (optional)
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // Timeline (feed)
    Route::get('/timeline', [PostController::class, 'index'])->name('timeline');
    Route::post('/timeline', [PostController::class, 'store'])->name('timeline.store');

    // Post interactions
    Route::prefix('posts/{post}')->group(function () {
        Route::post('/upvote', [PostController::class, 'upvote'])->name('posts.upvote');
        Route::post('/downvote', [PostController::class, 'downvote'])->name('posts.downvote');
        Route::post('/comment', [PostController::class, 'comment'])->name('posts.comment');
    });

    // ⚡ Post CRUD (Edit/Delete)
    Route::resource('posts', PostController::class)->except(['index', 'store']);
    // This generates:
    // GET    /posts/{post}/edit   → posts.edit
    // PUT    /posts/{post}        → posts.update
    // DELETE /posts/{post}        → posts.destroy
});

/*
|--------------------------------------------------------------------------
| Google OAuth
|--------------------------------------------------------------------------
*/
Route::get('/auth/google', fn() => Socialite::driver('google')->redirect())
    ->name('google.login');

Route::get('/auth/google/callback', function () {
    $googleUser = Socialite::driver('google')->user();
    // TODO: Replace with proper login/registration logic
    dd($googleUser);
});
