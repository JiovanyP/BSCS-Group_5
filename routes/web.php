<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\AccidentReportController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ReportController;
use App\Models\User;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| All web routes for both users and admins.
|
*/

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }
    return view('welcome');
})->name('welcome');

Route::view('/contact', 'contact')->name('contact');

/*
|--------------------------------------------------------------------------
| Guest Routes (Users + Admins)
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    // 🧍 Normal User Authentication
    Route::get('/login', fn() => view('login'))->name('login');
    Route::post('/login', [UserController::class, 'login'])->name('login.post');

    Route::get('/register', fn() => view('register'))->name('register');
    Route::post('/register', [UserController::class, 'register'])->name('register.post');

    // 👨‍💼 Admin Authentication
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/login', [AdminController::class, 'showLoginForm'])->name('login');
        Route::post('/login', [AdminController::class, 'login'])->name('login.post');

        // Optional password reset
        Route::get('/forgot-password', function () {
            return view('admin.forgot-password');
        })->name('password.request');
    });

    // ✅ AJAX Validation for Registration
    Route::post('/check-email', [UserController::class, 'checkEmail'])->name('check.email');
    Route::post('/check-username', [UserController::class, 'checkUsername'])->name('check.username');

    // 🌐 Google OAuth
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
        return redirect()->route('timeline')->with('success', 'Logged in with Google!');
    })->name('google.callback');

    // 🧩 Forgot / Reset Password (Users)
    Route::get('/forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])
        ->name('password.request');
    Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])
        ->name('password.email');

    Route::get('/reset-password/{token}', [ResetPasswordController::class, 'showResetForm'])
        ->name('password.reset');
    Route::post('/reset-password', [ResetPasswordController::class, 'reset'])
        ->name('password.update');
});

/*
|--------------------------------------------------------------------------
| User Authenticated Routes
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {

    // 🏠 Dashboard / Timeline
    Route::get('/dashboard', [PostController::class, 'index'])->name('dashboard');
    Route::get('/timeline', [PostController::class, 'timeline'])->name('timeline');
    Route::post('/timeline', [PostController::class, 'store'])->name('timeline.store');

    // 📝 Post CRUD
    Route::get('/posts/create', [PostController::class, 'create'])->name('posts.create');
    Route::post('/posts', [PostController::class, 'store'])->name('posts.store');
    Route::get('/posts/{post}/edit', [PostController::class, 'edit'])->name('posts.edit');
    Route::put('/posts/{post}', [PostController::class, 'update'])->name('posts.update');
    Route::delete('/posts/{post}', [PostController::class, 'destroy'])->name('posts.destroy');

    // 👍 Votes and Comments
    Route::post('/posts/{id}/vote', [PostController::class, 'vote'])->name('posts.vote');
    Route::post('/posts/{id}/comments', [PostController::class, 'addComment'])->name('posts.comment');
    Route::post('/posts/{post}/report', [PostController::class, 'report'])->name('posts.report');
    Route::post('/comments/{comment}/reply', [PostController::class, 'reply'])->name('comments.reply');

    // 🚨 Accident Reports
    Route::get('/report-accident', [AccidentReportController::class, 'create'])->name('accidents.create');
    Route::post('/report-accident', [AccidentReportController::class, 'store'])->name('accidents.store');
    Route::get('/report-accident/{id}', [AccidentReportController::class, 'showReportDetails'])->name('accidents.details');

    // 👤 Profile
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
    Route::patch('/profile/update', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/remove-avatar', [ProfileController::class, 'removeAvatar'])->name('profile.remove');

    // 🔔 Notifications
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications');
    Route::post('/notifications/{id}/mark-read', [NotificationController::class, 'markAsRead'])->name('notifications.markAsRead');
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.markAllRead');
    Route::delete('/notifications/{id}', [NotificationController::class, 'destroy'])->name('notifications.destroy');
    Route::get('/notifications/unread-count', [NotificationController::class, 'getUnreadCount'])->name('notifications.unreadCount');

    // 🚪 Logout (Normal User)
    Route::post('/logout', [UserController::class, 'logout'])->name('logout');
});

/*
|--------------------------------------------------------------------------
| Admin Authenticated Routes
|--------------------------------------------------------------------------
*/
Route::prefix('admin')
    ->name('admin.')
    ->middleware('auth:admin')
    ->group(function () {
        Route::get('/dashboard', [PostController::class, 'adminDashboard'])->name('dashboard');
        Route::post('/reports/{post}/resolve', [ReportController::class, 'resolve'])->name('reports.resolve');
        Route::post('/posts/{post}/remove', [PostController::class, 'adminRemove'])->name('posts.remove');

        // 👋 Admin logout
        Route::post('/logout', [AdminController::class, 'logout'])->name('logout');
    });
