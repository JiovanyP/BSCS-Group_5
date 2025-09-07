<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PostController;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Default homepage = login
Route::get('/', function () {
    return view('login');
})->name('login');

// Guest-only routes
Route::middleware('guest')->group(function () {
    // Login page
    Route::get('/login', function () {
        return view('login');  // resources/views/login.blade.php
    })->name('login');

    // Login POST
    Route::post('/login', [UserController::class, 'login'])->name('login.post');
});

// Register page (accessible even if logged in, so success popup can show)
Route::get('/register', function () {
    return view('register');
})->name('register');

// Register POST
Route::post('/register', [UserController::class, 'register'])->name('register.post');

// ✅ Google OAuth
Route::get('/auth/google', function () {
    return Socialite::driver('google')->redirect();
})->name('google.login');

Route::get('/auth/google/callback', function () {
    $googleUser = Socialite::driver('google')->user();

    // Find or create user
    $user = User::firstOrCreate(
        ['email' => $googleUser->getEmail()],
        [
            'name' => $googleUser->getName(),
            'password' => bcrypt(str()->random(16)), // dummy password
        ]
    );

    // Log them in
    Auth::login($user);

    // Redirect to timeline
    return redirect()->route('timeline');
});

// Timeline (logged in users only)
Route::get('/timeline', [PostController::class, 'index'])
    ->middleware('auth')
    ->name('timeline');

Route::post('/timeline', [PostController::class, 'store'])
    ->middleware('auth')
    ->name('timeline.store');

// Logout
Route::post('/logout', [UserController::class, 'logout'])->name('logout');
