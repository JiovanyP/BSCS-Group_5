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
| Public Routes
|--------------------------------------------------------------------------
*/

// 🏠 Landing page
Route::get('/', function () {
    return view('welcome');
})->name('welcome');

// 📞 Contact page
Route::view('/contact', 'contact')->name('contact');

/*
|--------------------------------------------------------------------------
| Guest Routes
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
});

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {

    // 📊 Dashboard (Statistics)
    Route::get('/dashboard', [PostController::class, 'index'])->name('dashboard');

    // 🧍 Timeline (User Posts)
    Route::get('/timeline', [PostController::class, 'timeline'])->name('timeline');
    Route::post('/timeline/store', [PostController::class, 'store'])->name('timeline.store');

    // ⚡ Upvote, Downvote, Comments (AJAX)
    Route::post('/posts/{id}/vote', [PostController::class, 'vote'])->name('posts.vote');
    Route::post('/posts/{id}/comments', [PostController::class, 'addComment'])->name('posts.comment');

    // ✏️ Post Edit / Update / Delete
    Route::get('/posts/{post}/edit', [PostController::class, 'edit'])->name('posts.edit');
    Route::put('/posts/{post}', [PostController::class, 'update'])->name('posts.update');
    Route::delete('/posts/{post}', [PostController::class, 'destroy'])->name('posts.destroy');

    // 🚗 Accident Reports
    Route::get('/report-accident', [AccidentReportController::class, 'create'])->name('accidents.create');
    Route::post('/report-accident', [AccidentReportController::class, 'store'])->name('accidents.store');

    // 👤 Profile Update
    Route::patch('/profile/update', [ProfileController::class, 'update'])->name('profile.update');

    // 🚪 Logout
    Route::post('/logout', [UserController::class, 'logout'])->name('logout');
});

/*
|--------------------------------------------------------------------------
| Google OAuth
|--------------------------------------------------------------------------
*/
Route::get('/auth/google', fn() => Socialite::driver('google')->redirect())->name('google.login');

Route::get('/auth/google/callback', function () {
    $googleUser = Socialite::driver('google')->user();

    $user = User::firstOrCreate(
        ['email' => $googleUser->getEmail()],
        [
            'name' => $googleUser->getName(),
            'password' => bcrypt(str()->random(16)),
        ]
    );

    Auth::login($user);

    return redirect()->route('dashboard')->with('success', 'Logged in with Google!');
});
