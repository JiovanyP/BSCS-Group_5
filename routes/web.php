<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PostController;

// Homepage (Workwise feed + login/register forms if you want)
Route::get('/', function () {
    return view('home');
})->name('home');

// Instead of separate views, point login/register here
Route::get('/login', function () {
    return view('home'); 
})->name('login');

Route::get('/register', function () {
    return view('register'); 
})->name('register');

// Dashboard (only for logged in users)
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware('auth')->name('dashboard');

// Auth routes (POST)
Route::post('/register', [UserController::class, 'register']); 
Route::post('/login', [UserController::class, 'login']); 
Route::post('/logout', [UserController::class, 'logout'])->name('logout');

// Timeline routes (only for logged in users)
Route::middleware(['auth'])->group(function () {
    Route::get('/timeline', [PostController::class, 'index'])->name('timeline');
    Route::post('/timeline', [PostController::class, 'store'])->name('timeline.store');
});
