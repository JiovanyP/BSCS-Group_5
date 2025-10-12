<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\AccidentReportController;
use App\Http\Controllers\ProfileController;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Profile Update
|--------------------------------------------------------------------------
*/
Route::patch('/profile/update', [ProfileController::class, 'update'])->name('profile.update');

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

// Landing page
Route::get('/', fn() => view('home'))->name('home');

// Contact page
Route::view('/contact', 'contact')->name('contact');

/*
|--------------------------------------------------------------------------
| Guest-only Routes (Login / Register)
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    // Login
    Route::get('/login', fn() => view('login'))->name('login');
    Route::post('/login', [UserController::class, 'login'])->name('login.post');

    // Register
    Route::get('/register', fn() => view('register'))->name('register');
    Route::post('/register', [UserController::class, 'register'])->name('register.post');

    // AJAX Validation
    Route::post('/check-email', [UserController::class, 'checkEmail'])->name('check.email');
    Route::post('/check-username', [UserController::class, 'checkUsername'])->name('check.username');
});

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    // Logout
    Route::post('/logout', [UserController::class, 'logout'])->name('logout');

    // Dashboard
    Route::get('/dashboard', fn() => view('dashboard'))->name('dashboard');

    /*
    |--------------------------------------------------------------------------
    | Timeline (Posts Feed)
    |--------------------------------------------------------------------------
    */
    Route::get('/timeline', [PostController::class, 'index'])->name('timeline');
    Route::post('/timeline', [PostController::class, 'store'])->name('timeline.store');

    /*
    |--------------------------------------------------------------------------
    | Post Interactions (Upvote / Downvote / Comment)
    |--------------------------------------------------------------------------
    */
    Route::prefix('posts')->group(function () {
        Route::post('/{post}/upvote', [PostController::class, 'upvote'])->name('posts.upvote');
        Route::post('/{post}/downvote', [PostController::class, 'downvote'])->name('posts.downvote');
        Route::post('/{post}/comment', [PostController::class, 'comment'])->name('posts.comment');
    });

    /*
    |--------------------------------------------------------------------------
    | Post CRUD (Edit / Delete)
    |--------------------------------------------------------------------------
    */
    Route::resource('posts', PostController::class)->except(['index', 'store']);

    /*
    |--------------------------------------------------------------------------
    | Accident Reports
    |--------------------------------------------------------------------------
    */
    Route::get('/report-accident', [AccidentReportController::class, 'create'])->name('accidents.create');
    Route::post('/report-accident', [AccidentReportController::class, 'store'])->name('accidents.store');

    /*
    |--------------------------------------------------------------------------
    | Additional Pages
    |--------------------------------------------------------------------------
    */
    Route::view('/report', 'report')->name('report');
    Route::view('/verify', 'verify')->name('verify');
    Route::view('/history', 'history')->name('history');
    Route::view('/account', 'account')->name('account');

    /*
    |--------------------------------------------------------------------------
    | Homepage (Alternative to Dashboard)
    |--------------------------------------------------------------------------
    */
    Route::get('/homepage', [PostController::class, 'index'])->name('homepage');
});

/*
|--------------------------------------------------------------------------
| Google OAuth
|--------------------------------------------------------------------------
*/
Route::get('/auth/google', fn() => Socialite::driver('google')->redirect())->name('google.login');

Route::get('/auth/google/callback', function () {
    $googleUser = Socialite::driver('google')->user();

    // Find or create user
    $user = User::firstOrCreate(
        ['email' => $googleUser->getEmail()],
        [
            'name'     => $googleUser->getName(),
            'password' => bcrypt(str()->random(16)),
        ]
    );

    // Log in user
    Auth::login($user);

    // Redirect to timeline (from main)
    return redirect()->route('timeline');
});