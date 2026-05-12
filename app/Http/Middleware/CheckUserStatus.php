<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckUserStatus
{
    public function handle(Request $request, Closure $next)
    {
        // only check the web (normal user) guard — don't affect admin sessions
        if (Auth::guard('web')->check()) {
            $user = Auth::guard('web')->user();

            if ($user && $user->status === 'suspended') {
                Auth::guard('web')->logout();
                return redirect()->route('login')
                    ->withErrors(['error' => 'Your account has been suspended by an administrator.']);
            }

            if ($user && $user->status === 'banned') {
                Auth::guard('web')->logout();
                return redirect()->route('login')
                    ->withErrors(['error' => 'Your account has been banned by an administrator.']);
            }
        }

        return $next($request);
    }
}
