<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
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
            'name'     => ['required', 'min:1', 'max:100', Rule::unique('users', 'name')],
            'email'    => ['required', 'email', Rule::unique('users', 'email')],
            'location' => ['required', 'min:1', 'max:100'],
            'password' => ['required', 'min:8', 'max:200', 'confirmed'],
        ]);

        // Create user (password auto-hashed in User model)
        $user = User::create($incomingFields);

        // Reset any old session + log in new user
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        Auth::login($user);
        $request->session()->regenerate();

        // ⚡ Force fresh cookie
        Cookie::queue(Cookie::make(
            config('session.cookie'),
            $request->session()->getId(),
            config('session.lifetime')
        ));

        return redirect()->route('timeline')
            ->with('success', 'Your account has been created and you are now logged in!');
    }

    /**
     * Handle user login (email + password).
     */
    public function login(Request $request)
    {
        // ✅ Ensure old ghost sessions don't cause 419
        if (!Auth::check()) {
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        // 🔍 Debug incoming credentials
        \Log::info('Login attempt', $credentials);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate(); // prevent session fixation

            // ⚡ Force fresh cookie after login
            Cookie::queue(Cookie::make(
                config('session.cookie'),
                $request->session()->getId(),
                config('session.lifetime')
            ));

            // 🔍 Debug success
            \Log::info('Login successful', ['user_id' => Auth::id(), 'session_id' => $request->session()->getId()]);

            return redirect()->route('timeline')->with('success', 'Welcome back!');
        }

        // 🔍 Debug failure
        \Log::warning('Login failed for email: ' . $request->email);

        return back()->withErrors([
            'email' => 'Invalid login credentials.',
        ])->withInput();
    }

    /**
     * Handle user logout.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // ⚡ Clear session cookie
        Cookie::queue(Cookie::forget(config('session.cookie')));

        return redirect()->route('login');
    }
}
