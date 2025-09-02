<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

// Homepage -> redirect to login form
Route::get('/', function () {
    return view('home'); // This will show the login page
});

// Add these new GET routes:
Route::get('/login', function () {
    return view('home'); // Shows the login form
})->name('login');

Route::get('/register', function () {
    return view('register'); // Shows the registration form
})->name('register');

// Dashboard
Route::get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard');

// Auth routes (your existing POST routes)
Route::post("/register", [UserController::class, 'register']); 
Route::post("/logout", [UserController::class, 'logout']);
Route::post("/login", [UserController::class, 'login']);

// Add these routes to your routes/web.php file
// Route::post('/check-email', [UserController::class, 'checkEmail']);
// Route::post('/check-username', [UserController::class, 'checkUsername']);