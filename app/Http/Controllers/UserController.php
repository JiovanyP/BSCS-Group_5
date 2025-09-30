<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use App\Models\User;

class UserController extends Controller
{
    /**
     * Handle user registration.
     */
    public function register(Request $request)
    {
        $incomingFields = $request->validate([
            'name' => ['required', 'max:100'],
            'email' => ['required', 'email', 'max:100', 'unique:users,email'],
            'password' => ['required', 'min:8', 'max:200', 'confirmed'],
        ]);

        // Hash password
        $incomingFields['password'] = bcrypt($incomingFields['password']);

        // Create user
        $user = User::create($incomingFields);

        // Reset session + log in new user
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        Auth::login($user);
        $request->session()->regenerate();

        // ✅ Force fresh session cookie
        Cookie::queue(Cookie::make(
            config('session.cookie'),
            $request->session()->getId(),
            config('session.lifetime'),
            config('session.path'),
            config('session.domain'),
            config('session.secure'),
            config('session.http_only'),
            false,
            config('session.same_site', 'lax')
        ));

        // Redirect to dashboard instead of timeline
        return redirect()->route('dashboard')->with('success', 'Your account has been created and you are now logged in!');
    }

    /**
     * Handle user login (email or username + password).
     */
    public function login(Request $request)
    {
        // Validate input
        $request->validate([
            'email' => 'required',
            'password' => 'required',
        ]);

        // Support email OR username login
        $loginField = filter_var($request->email, FILTER_VALIDATE_EMAIL) ? 'email' : 'name';
        $credentials = [
            $loginField => $request->email,
            'password' => $request->password,
        ];

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            // ✅ Force fresh session cookie after login
            Cookie::queue(Cookie::make(
                config('session.cookie'),
                $request->session()->getId(),
                config('session.lifetime'),
                config('session.path'),
                config('session.domain'),
                config('session.secure'),
                config('session.http_only'),
                false,
                config('session.same_site', 'lax')
            ));

            // Redirect to dashboard
            return redirect()->route('dashboard')->with('success', 'Welcome back!');
        }

        // Failed login
        return back()->withErrors([
            'email' => 'Invalid login credentials.',
        ]);
    }

    /**
     * Handle user logout.
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // ✅ Clear session cookie
        Cookie::queue(Cookie::forget(config('session.cookie')));

        return redirect()->route('login')->with('success', 'You have been logged out.');
    }
}
