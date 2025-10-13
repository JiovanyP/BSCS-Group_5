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
| Public Routes (Available to everyone)
|--------------------------------------------------------------------------
*/

// Landing page - accessible to all
Route::get('/', function () {
    // If user is already logged in, redirect to dashboard
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }
    return view('welcome');
})->name('welcome');

// Contact page
Route::view('/contact', 'contact')->name('contact');

/*
|--------------------------------------------------------------------------
| Guest-only Routes (Only for non-logged in users)
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

    // ✅ Google OAuth - MOVED INSIDE GUEST GROUP
    Route::get('/auth/google', function () {
        return Socialite::driver('google')->redirect();
    })->name('google.login');

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

        // Redirect to dashboard
        return redirect()->route('dashboard')->with('success', 'Logged in with Google!');
    });
});

/*
|--------------------------------------------------------------------------
| Protected Routes (Only for logged in users)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    // Dashboard - main page after login
    Route::get('/dashboard', [PostController::class, 'index'])->name('dashboard');
    
    // Accident report routes
    Route::get('/report-accident', [AccidentReportController::class, 'create'])->name('accidents.create');
    Route::post('/report-accident', [AccidentReportController::class, 'store'])->name('accidents.store');
    
    // Logout
    Route::post('/logout', [UserController::class, 'logout'])->name('logout');
});