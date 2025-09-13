<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\CommentController;
use Laravel\Socialite\Facades\Socialite;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

// Homepage (feed + login/register options)
Route::get('/', function () {
    return view('home');
})->name('home');

// Login + Register pages (GET)
Route::view('/login', 'login')->name('login');
Route::view('/register', 'register')->name('register');

// Auth actions (POST)
Route::post('/register', [UserController::class, 'register'])->name('register.post'); 
Route::post('/login', [UserController::class, 'login'])->name('login.post'); 
Route::post('/logout', [UserController::class, 'logout'])->name('logout');

// AJAX checks (optional)
Route::post('/check-email', [UserController::class, 'checkEmail'])->name('check.email');
Route::post('/check-username', [UserController::class, 'checkUsername'])->name('check.username');

/*
|--------------------------------------------------------------------------
| Timeline / Post Routes (Protected)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {

    // Dashboard
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // Timeline (feed)
    Route::get('/timeline', [PostController::class, 'index'])->name('timeline');
    Route::post('/timeline', [PostController::class, 'store'])->name('timeline.store');

    // Post interactions (vote/comments)
    Route::post('/posts/{post}/vote', [PostController::class, 'vote'])->name('posts.vote');
    Route::post('/posts/{post}/comments', [CommentController::class, 'store'])->name('posts.comments.store');

    // Post CRUD (edit/delete)
    Route::resource('posts', PostController::class)->except(['index', 'store']);
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
    dd($googleUser); // Replace with login/registration logic
});
