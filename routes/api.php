<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;

/*
|--------------------------------------------------------------------------
| API Routes for Mobile (Flutter)
|--------------------------------------------------------------------------
| These endpoints must return JSON only.
| No redirects. No Blade. No sessions.
|--------------------------------------------------------------------------
*/

// PUBLIC ROUTES
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

// api.php

// PROTECTED ROUTES (Require Sanctum Token)
Route::middleware('auth:sanctum')->group(function () {

    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // COMMENT THESE OUT TOO:
    // Route::get('/posts', [PostController::class, 'index']);
    // Route::post('/posts', [PostController::class, 'store']);

    Route::post('/logout', [AuthController::class, 'logout']);
});
