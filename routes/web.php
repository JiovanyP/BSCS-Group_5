<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PostController;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AccidentReportController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Landing page (public)
Route::get('/', function () {
    return view('welcome');
})->name('welcome');

// Guest-only routes
Route::middleware('guest')->group(function () {
    // Login page
    Route::get('/login', function () {
        return view('login');
    })->name('login');

    // Login POST - handles form submission
    Route::post('/login', [UserController::class, 'login'])->name('login.post');
    
    // Register page
    Route::get('/register', function () {
        return view('register');
    })->name('register');

    // Register POST - handles form submission
    Route::post('/register', [UserController::class, 'register'])->name('register.post');
});

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
            'password' => bcrypt(str()->random(16)),
        ]
    );

    // Log them in
    Auth::login($user);

    // Redirect to homepage
    return redirect()->route('homepage');
});

// Protected routes (logged in users only)
Route::middleware('auth')->group(function () {
    // REMOVED DASHBOARD ROUTE - Using homepage instead
    
    // Homepage - main page after login
    Route::get('/homepage', [PostController::class, 'index'])->name('homepage');
    
    // Timeline post creation
    // Route::post('/timeline', [PostController::class, 'store'])->name('timeline.store');
    
    // Accident report routes
    Route::get('/report-accident', [AccidentReportController::class, 'create'])->name('accidents.create');
    Route::post('/report-accident', [AccidentReportController::class, 'store'])->name('accidents.store');
    
    // Other protected routes (replace YourController with actual controller)
    Route::get('/report', [ReportController::class, 'index'])->name('report');
    Route::get('/verify', [VerificationController::class, 'index'])->name('verify');
    Route::get('/history', [HistoryController::class, 'index'])->name('history');
    Route::get('/account', [AccountController::class, 'index'])->name('account');
    Route::get('/account', [AccountController::class, 'index'])->name('account');
});

// Logout
Route::post('/logout', [UserController::class, 'logout'])->name('logout');

// Contact page (public)
Route::get('/contact', function () {
    return view('contact');
})->name('contact');

// // Login page
// Route::get('/login', function () {
//     dd('Login page reached'); // This will show a debug message
//     return view('login');
// })->name('login');

// // Register page
// Route::get('/register', function () {
//     dd('Register page reached'); // This will show a debug message
//     return view('register');
// })->name('register');