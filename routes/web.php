<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\AccidentReportController;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

// Landing page
Route::get('/', function () {
    return view('home'); // resources/views/home.blade.php
})->name('home');

// Contact (public)
Route::view('/contact', 'contact')->name('contact');

// Guest-only routes (login/register)
Route::middleware('guest')->group(function () {
    // Login
    Route::get('/login', fn() => view('login'))->name('login');
    Route::post('/login', [UserController::class, 'login'])->name('login.post');

    // Register
    Route::get('/register', fn() => view('register'))->name('register');
    Route::post('/register', [UserController::class, 'register'])->name('register.post');

    // AJAX checks (optional for live validation)
    Route::post('/check-email', [UserController::class, 'checkEmail'])->name('check.email');
    Route::post('/check-username', [UserController::class, 'checkUsername'])->name('check.username');
});

// Logout
Route::post('/logout', [UserController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| Google OAuth
|--------------------------------------------------------------------------
*/
Route::get('/auth/google', fn() => Socialite::driver('google')->redirect())
    ->name('google.login');

Route::get('/auth/google/callback', function () {
    $googleUser = Socialite::driver('google')->user();

    // Find or create the user
    $user = User::firstOrCreate(
        ['email' => $googleUser->getEmail()],
        [
            'name' => $googleUser->getName(),
            'password' => bcrypt(str()->random(16)),
        ]
    );

    // Log the user in
    Auth::login($user);

    // Redirect to timeline
    return redirect()->route('timeline');
});

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {

    // Dashboard (optional)
    Route::get('/dashboard', fn() => view('dashboard'))->name('dashboard');

    // Timeline / Feed
    Route::get('/timeline', [PostController::class, 'index'])->name('timeline');
    Route::post('/timeline', [PostController::class, 'store'])->name('timeline.store');

    // Post Interactions
    Route::prefix('posts/{post}')->group(function () {
        Route::post('/upvote', [PostController::class, 'upvote'])->name('posts.upvote');
        Route::post('/downvote', [PostController::class, 'downvote'])->name('posts.downvote');
        Route::post('/comment', [PostController::class, 'comment'])->name('posts.comment');
    });

    // Post CRUD (Edit/Delete)
    Route::resource('posts', PostController::class)->except(['index', 'store']);

    // Accident report routes
    Route::get('/report-accident', [AccidentReportController::class, 'create'])->name('accidents.create');
    Route::post('/report-accident', [AccidentReportController::class, 'store'])->name('accidents.store');

    // Additional pages (optional)
    Route::view('/report', 'report')->name('report');
    Route::view('/verify', 'verify')->name('verify');
    Route::view('/history', 'history')->name('history');
    Route::view('/account', 'account')->name('account');
});
