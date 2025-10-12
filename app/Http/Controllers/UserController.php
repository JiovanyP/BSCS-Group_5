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

        // Hash password
        $incomingFields['password'] = bcrypt($incomingFields['password']);

        // Create user
        $user = User::create($incomingFields);

        // Reset session + log in new user
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        Auth::login($user);
        $request->session()->regenerate();

        // Force fresh cookie
        Cookie::queue(Cookie::make(config('session.lifetime')));

        // Redirect to HOMEPAGE instead of timeline
        return redirect()->route('homepage')->with('success', 'Your account has been created and you are now logged in!');
    }

    /**
     * Handle user login (email or username + password).
     */
    public function login(Request $request)
    {
        // ✅ Debug: check if controller is reached
        // dd('Login controller reached', $request->all());

        // Validate input
        $request->validate([
            'email' => 'required',
            'password' => 'required',
        ]);

        // Determine login field
        $loginField = filter_var($request->email, FILTER_VALIDATE_EMAIL) ? 'email' : 'name';
        $credentials = [
            $loginField => $request->email,
            'password' => $request->password,
        ];

        // Attempt login
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            // Redirect to HOMEPAGE instead of timeline
            return redirect()->route('homepage')->with('success', 'Welcome back!');
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
        return redirect()->route('login')->with('success', 'You have been logged out.');
    }

    /**
     * Update user profile (optional).
     */
    public function update(Request $request, User $user)
    {
        $this->authorize('update', $user);

        $request->validate([
            'name'     => ['required', 'min:1', 'max:100', Rule::unique('users')->ignore($user->id)],
            'email'    => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'location' => ['nullable', 'max:100'],
            'password' => ['nullable', 'min:8', 'max:200', 'confirmed'],
        ]);

        $data = $request->only(['name', 'email', 'location']);
        if ($request->filled('password')) {
            $data['password'] = bcrypt($request->password);
        }

        $user->update($data);

        return redirect()->back()->with('success', 'Profile updated successfully.');
    }
}
