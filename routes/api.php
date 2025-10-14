<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| These routes are stateless. Do NOT return Blade views or call session-dependent
| controllers here. Place web routes in routes/web.php.
|
*/

/**
 * Optional safety redirect:
 * Temporary: If something still requests GET /api/login, redirect to /login (web)
 * so the correct middleware runs. Remove this once all references are fixed.
 */
Route::get('/login', function () {
    return redirect()->route('login');
});

/**
 * Example API route (stateless); protect with tokens (Sanctum) if needed:
 */
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

/*
 * Add other JSON-only API routes below.
 */
