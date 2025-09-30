<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\CommentController;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

// Homepage (feed + login/register options)
Route::get('/', function () {
    return view('home');
})->name('home');

// Guest-only routes
Route::middleware('guest')->group(function () {
    // Login page + action
    Route::view('/login', 'login')->name('login');
    Route::post('/login', [UserController::class, 'login'])->name('login.post');

    // Register page + action
    Route::view('/register', 'register')->name('register');
    Route::post('/register', [UserController::class, 'register'])->name('register.post');
});

// Logout (for logged-in users)
Route::post('/logout', [UserController::class, 'logout'])->name('logout');

// AJAX checks
Route::post('/check-email', [UserController::class, 'checkEmail'])->name('check.email');
Route::post('/check-username', [UserController::class, 'checkUsername'])->name('check.username');

/*
|--------------------------------------------------------------------------
| Timeline / Post Routes (Protected)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/dashboard', fn() => view('dashboard'))->name('dashboard');

    // Timeline (feed)
    Route::get('/timeline', [PostController::class, 'index'])->name('timeline');
    Route::post('/timeline', [PostController::class, 'store'])->name('timeline.store');

    // Post interactions
    Route::post('/posts/{post}/vote', [PostController::class, 'vote'])->name('posts.vote');
    Route::post('/posts/{post}/comments', [CommentController::class, 'store'])->name('posts.comments.store');

    // Post CRUD
    Route::resource('posts', PostController::class)->except(['index', 'store']);
});

/*
|--------------------------------------------------------------------------
| Google OAuth
|--------------------------------------------------------------------------
*/
Route::get('/auth/google', function () {
    return Socialite::driver('google')->redirect();
})->name('google.login');

Route::get('/auth/google/callback', function () {
    $googleUser = Socialite::driver('google')->user();

    // ✅ Option 1: Debug (for testing only)
    // dd($googleUser);

    // ✅ Option 2: Actual login/registration logic
    $user = User::firstOrCreate(
        ['email' => $googleUser->getEmail()],
        [
            'name' => $googleUser->getName(),
            'password' => bcrypt(str()->random(16)), // random password for OAuth users
        ]
    );

    // Log in the user
    Auth::login($user);

    // Redirect to timeline
    return redirect()->route('timeline');
});
