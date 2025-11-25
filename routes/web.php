<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\AdminPostController; // ADDED
use App\Http\Controllers\PostController;
use App\Http\Controllers\AccidentReportController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ReportController;
use App\Models\User;

/*
|--------------------------------------------------------------------------
| WEB ROUTES
|--------------------------------------------------------------------------
| Unified routes for both normal users and administrators.
| Separated via guards: "auth" (user) and "auth:admin" (admin).
|--------------------------------------------------------------------------
*/

/*
|--------------------------------------------------------------------------
| PUBLIC (Guest) ROUTES
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return Auth::check()
        ? redirect()->route('timeline')
        : view('welcome');
})->name('welcome');

Route::view('/contact', 'contact')->name('contact');

/** -----------------------
 * TERMS & PRIVACY PAGES
 * ---------------------- */
Route::get('/terms', function () {
    return view('terms');
})->name('terms');

Route::get('/privacy', function () {
    return view('privacy');
})->name('privacy');

/*
|--------------------------------------------------------------------------
| AUTHENTICATION - GUEST ONLY
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {

    /** -----------------------
     * USER LOGIN & REGISTER
     * ---------------------- */
    Route::get('/login', fn() => view('login'))->name('login');
    Route::post('/login', [UserController::class, 'login'])->name('login.post');

    // Registration with verification flow
    Route::get('/register', [RegisterController::class, 'showForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register'])->name('register.post');
    Route::post('/register/send-code', [RegisterController::class, 'sendCode'])->name('register.sendCode');
    Route::post('/register/verify-code', [RegisterController::class, 'verifyCode'])->name('register.verifyCode');
    Route::get('/verify', [RegisterController::class, 'showVerifyForm'])->name('verify.show');

    /** -----------------------
     * ADMIN LOGIN
     * ---------------------- */
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/login', [AdminController::class, 'showLoginForm'])->name('login');
        Route::post('/login', [AdminController::class, 'login'])->name('login.post');
        Route::get('/forgot-password', fn() => view('admin.forgot-password'))->name('password.request');
    });

    /** -----------------------
     * AJAX CHECKS FOR USER REGISTRATION
     * ---------------------- */
    Route::post('/check-email', [UserController::class, 'checkEmail'])->name('check.email');
    Route::post('/check-username', [UserController::class, 'checkUsername'])->name('check.username');

    /** -----------------------
     * GOOGLE OAUTH LOGIN
     * ---------------------- */
    /** GOOGLE LOGIN */
    Route::get('/auth/google', function () {
        return Socialite::driver('google')->stateless()->redirect();
    })->name('google.login');

    Route::get('/auth/google/callback', function () {
        $googleUser = Socialite::driver('google')->stateless()->user();

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

    /** -----------------------
     * PASSWORD RESET (USER)
     * ---------------------- */
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
| USER AUTHENTICATED ROUTES
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {

    /** -----------------------
     * DASHBOARD & TIMELINE
     * ---------------------- */
    Route::get('/dashboard', [PostController::class, 'index'])->name('dashboard');
    Route::get('/timeline', [PostController::class, 'timeline'])->name('timeline');
    Route::post('/timeline', [PostController::class, 'store'])->name('timeline.store');

    /** -----------------------
     * POSTS (CRUD)
     * ---------------------- */
    Route::get('/posts/create', [PostController::class, 'create'])->name('posts.create');
    Route::post('/posts', [PostController::class, 'store'])->name('posts.store');
    Route::get('/posts/{post}/edit', [PostController::class, 'edit'])->name('posts.edit');
    Route::put('/posts/{post}', [PostController::class, 'update'])->name('posts.update');
    Route::delete('/posts/{post}', [PostController::class, 'destroy'])->name('posts.destroy');

    // ✅ Main post view route
    Route::get('/posts/{id}/view', [PostController::class, 'viewPost'])->name('posts.view');

    // ✅ Legacy alias — safely redirects to posts.view
    Route::get('/viewpost/{id}', function ($id) {
        return redirect()->route('posts.view', ['id' => $id]);
    })->name('viewpost');

    /** -----------------------
     * POST INTERACTIONS
     * ---------------------- */
    Route::post('/posts/{id}/vote', [PostController::class, 'vote'])->name('posts.vote');
    Route::post('/posts/{id}/comments', [PostController::class, 'addComment'])->name('posts.comment');
    Route::post('/comments/{comment}/reply', [PostController::class, 'reply'])->name('comments.reply');
    Route::post('/posts/{post}/report', [PostController::class, 'report'])->name('posts.report');

    /** -----------------------
     * ACCIDENT REPORTS
     * ---------------------- */
    Route::get('/report-accident', [AccidentReportController::class, 'create'])->name('accidents.create');
    Route::post('/report-accident', [AccidentReportController::class, 'store'])->name('accidents.store');
    Route::get('/report-accident/{id}', [AccidentReportController::class, 'showReportDetails'])
        ->name('accidents.details');

    /** -----------------------
     * USER PROFILE
     * ---------------------- */
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
    Route::get('/profile/modal', [ProfileController::class, 'getEditModal'])->name('profile.modal');
    Route::patch('/profile/update', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/remove-avatar', [ProfileController::class, 'removeAvatar'])->name('profile.remove');

    /** -----------------------
     * NOTIFICATIONS
     * ---------------------- */
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications');
    Route::post('/notifications/{id}/mark-read', [NotificationController::class, 'markAsRead'])
        ->name('notifications.markAsRead');
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])
        ->name('notifications.markAllRead');
    Route::delete('/notifications/{id}', [NotificationController::class, 'destroy'])
        ->name('notifications.destroy');
    Route::get('/notifications/unread-count', [NotificationController::class, 'getUnreadCount'])
        ->name('notifications.unreadCount');

    /** -----------------------
     * LOGOUT
     * ---------------------- */
    Route::post('/logout', [UserController::class, 'logout'])->name('logout');
});

/*
|--------------------------------------------------------------------------
| ADMIN AUTHENTICATED ROUTES (guard: admin)
|--------------------------------------------------------------------------
*/
Route::prefix('admin')
    ->name('admin.')
    ->middleware('auth:admin')
    ->group(function () {

        /** -----------------------
         * ADMIN DASHBOARD
         * ---------------------- */
        Route::get('/dashboard', [PostController::class, 'adminDashboard'])->name('dashboard');

        /** -----------------------
         * ADMIN POSTS - ADDED NEW ROUTES
         * ---------------------- */
        Route::get('/posts/create', [AdminPostController::class, 'create'])->name('posts.create');
        Route::post('/posts', [AdminPostController::class, 'store'])->name('posts.store');
        Route::get('/posts', [AdminPostController::class, 'index'])->name('posts.index');
        Route::delete('/posts/{post}', [AdminPostController::class, 'destroy'])->name('posts.destroy');
        
        // View posts (existing routes)
        Route::get('/posts/{id}/view', [PostController::class, 'viewPost'])->name('posts.view');
        Route::get('/viewpost/{id}', function ($id) {
            return redirect()->route('admin.posts.view', ['id' => $id]);
        })->name('viewpost');
        Route::post('/posts/{post}/remove', [PostController::class, 'adminRemove'])->name('posts.remove');

        /** -----------------------
         * ADMIN REPORTS
         * ---------------------- */
        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
        Route::post('/reports/{post}/resolve', [ReportController::class, 'resolve'])->name('reports.resolve');
        Route::post('/reports/resolve-orphan', [ReportController::class, 'resolveOrphan'])->name('reports.resolveOrphan');

        /** -----------------------
         * ADMIN USERS (new AdminUserController)
         * ---------------------- */
        Route::get('/users-old', [AdminController::class, 'usersIndex'])->name('users.old');
        Route::post('/users/{id}/ban', [AdminController::class, 'banUser'])->name('users.old.ban');
        Route::post('/users/{id}/unban', [AdminController::class, 'unbanUser'])->name('users.old.unban');

        Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
        Route::get('/users/{user}', [AdminUserController::class, 'show'])->name('users.show');
        Route::post('/users/{user}/suspend', [AdminUserController::class, 'suspend'])->name('users.suspend');
        Route::post('/users/{user}/ban', [AdminUserController::class, 'ban'])->name('users.ban');
        Route::post('/users/{user}/restore', [AdminUserController::class, 'restore'])->name('users.restore');
        Route::delete('/users/{user}', [AdminUserController::class, 'destroy'])->name('users.destroy');

        /** -----------------------
         * ADMIN ANALYTICS / SETTINGS
         * ---------------------- */
        Route::get('/analytics', [AdminController::class, 'analytics'])->name('analytics');
        Route::get('/posts/accident/{type}', [AdminController::class, 'postsByAccidentType'])->name('posts.byAccidentType');

        // Settings (GET already present)
        Route::get('/settings', [AdminController::class, 'settings'])->name('settings');

        // Settings actions (non-invasive, per-admin JSON persistence + password change)
        Route::post('/settings/password', [AdminController::class, 'updatePassword'])->name('settings.updatePassword');
        Route::post('/settings/theme', [AdminController::class, 'updateTheme'])->name('settings.updateTheme');

        /** -----------------------
         * ADMIN NOTIFICATIONS
         * ---------------------- */
        Route::get('/notifications', [NotificationController::class, 'adminIndex'])->name('notifications.index');

        /** -----------------------
         * ADMIN PROFILE
         * ---------------------- */
        Route::get('/profile', [AdminController::class, 'profile'])->name('profile');

        /** -----------------------
         * ADMIN LOGOUT
         * ---------------------- */
        Route::post('/logout', [AdminController::class, 'logout'])->name('logout');
    });


// Redirect /admin to /admin/login if not authenticated
Route::get('/admin', function () {
    if (Auth::guard('admin')->check()) {
        return redirect()->route('admin.dashboard');
    }
    return redirect()->route('admin.login');
});

/*
|--------------------------------------------------------------------------
| FALLBACK / 404
|--------------------------------------------------------------------------
*/
Route::fallback(function () {
    return response()->view('errors.404', [], 404);
});