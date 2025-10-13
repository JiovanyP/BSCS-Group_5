<?php

use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

// Public routes
Route::get('/', function () {
    return view('welcome');
})->name('welcome');

Route::get('/test-session', function () {
    session(['test' => 'Session is working!']);
    return session('test');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', function () {
        return view('login');
    })->name('login');

    Route::get('/register', function () {
        return view('register');
    })->name('register');

    // Google OAuth routes
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
                'password' => bcrypt(str()->random(16)),
            ]
        );

        // Log them in
        Auth::login($user);

        // Regenerate session to prevent session fixation
        request()->session()->regenerate();

        // Redirect to homepage
        return redirect()->route('homepage');
    });
});

// Authentication routes
Route::post('/register', [UserController::class, 'register']);
Route::post('/login', [UserController::class, 'login']);
Route::post('/logout', [UserController::class, 'logout']);

// Protected routes
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::get('/timeline', [App\Http\Controllers\PostController::class, 'index'])->name('timeline');
    Route::post('/timeline', [App\Http\Controllers\PostController::class, 'store'])->name('timeline.store');
    Route::patch('/profile/{user}', [App\Http\Controllers\UserController::class, 'update'])->name('profile.update');
    Route::get('/posts/{post}/edit', [App\Http\Controllers\PostController::class, 'edit'])->name('posts.edit');
    Route::patch('/posts/{post}', [App\Http\Controllers\PostController::class, 'update'])->name('posts.update');
    Route::delete('/posts/{post}', [App\Http\Controllers\PostController::class, 'destroy'])->name('posts.destroy');

    Route::get('/newsfeed', [App\Http\Controllers\PostController::class, 'newsfeed'])->name('newsfeed');
});
