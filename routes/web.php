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
| Web Routes
|--------------------------------------------------------------------------
|
| Public, guest-only, and authenticated routes live here (web middleware).
|
*/

/*
|--------------------------------------------------------------------------
| Public Routes (available to everyone)
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }
    return view('welcome');
})->name('welcome');

// Contact (single definition)
Route::view('/contact', 'contact')->name('contact');

/*
|--------------------------------------------------------------------------
| Guest-only Routes (non-authenticated users)
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    // 🔐 Authentication
    Route::get('/login', fn() => view('login'))->name('login');
    Route::post('/login', [UserController::class, 'login'])->name('login.post');

    Route::get('/register', fn() => view('register'))->name('register');
    Route::post('/register', [UserController::class, 'register'])->name('register.post');

    // ⚙️ AJAX validation
    Route::post('/check-email', [UserController::class, 'checkEmail'])->name('check.email');
    Route::post('/check-username', [UserController::class, 'checkUsername'])->name('check.username');

    // 🌐 Google OAuth (guest only)
    Route::get('/auth/google', fn() => Socialite::driver('google')->redirect())->name('google.login');

    Route::get('/auth/google/callback', function () {
        $googleUser = Socialite::driver('google')->user();

        $user = User::firstOrCreate(
            ['email' => $googleUser->getEmail()],
            [
                'name'     => $googleUser->getName(),
                'password' => bcrypt(str()->random(16)),
            ]
        );

        Auth::login($user);

        return redirect()->route('dashboard')->with('success', 'Logged in with Google!');
    })->name('google.callback');
});

/*
|--------------------------------------------------------------------------
| Authenticated Routes (logged-in users)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {

    // 📊 Dashboard
    Route::get('/dashboard', [PostController::class, 'index'])->name('dashboard');

    // 🧍 Timeline
    Route::get('/timeline', [PostController::class, 'timeline'])->name('timeline');
    Route::post('/timeline', [PostController::class, 'store'])->name('timeline.store');

    // ⚡ Votes and Comments (AJAX)
    Route::post('/posts/{id}/vote', [PostController::class, 'vote'])->name('posts.vote');
    Route::post('/posts/{id}/comments', [PostController::class, 'addComment'])->name('posts.comment');

    // ✏️ Post CRUD
    Route::get('/posts/{post}/edit', [PostController::class, 'edit'])->name('posts.edit');
    Route::put('/posts/{post}', [PostController::class, 'update'])->name('posts.update');
    Route::delete('/posts/{post}', [PostController::class, 'destroy'])->name('posts.destroy');

    // 🚗 Accident Reports
    Route::get('/report-accident', [AccidentReportController::class, 'create'])->name('accidents.create');
    Route::post('/report-accident', [AccidentReportController::class, 'store'])->name('accidents.store');

    // 👤 Profile
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
    Route::patch('/profile/update', [ProfileController::class, 'update'])->name('profile.update');

    // 🔔 Notifications page (view-only for now)
    Route::get('/notifications', function () {
        return view('notifications');
    })->name('notifications');

    // 🚪 Logout
    Route::post('/logout', [UserController::class, 'logout'])->name('logout');
});
