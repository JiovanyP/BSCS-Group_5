<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckUserStatus
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        if ($user && Auth::getDefaultDriver() === 'web') {

            if ($user->status === 'suspended') {
                Auth::logout();
                return redirect()->route('login')
                    ->withErrors(['error' => 'Your account has been suspended by an administrator.']);
            }

            if ($user->status === 'banned') {
                Auth::logout();
                return redirect()->route('login')
                    ->withErrors(['error' => 'Your account has been banned by an administrator.']);
            }
        }

        return $next($request);
    }
}
