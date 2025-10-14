<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Validation\Rule;
use App\Models\User;
use App\Models\Post;

class UserController extends Controller
{
    /**
     * Handle registration.
     */
    public function register(Request $request)
    {
        $incoming = $request->validate([
            'name'      => ['required', 'max:100'],
            'email'     => ['required', 'email', 'max:100', 'unique:users,email'],
            'location'  => ['nullable', 'string', 'max:255'],
            'password'  => ['required', 'min:8', 'max:200', 'confirmed'],
        ]);

        $incoming['password'] = bcrypt($incoming['password']);
        $user = User::create($incoming);

        // Reset session + login
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        Auth::login($user);
        $request->session()->regenerate();

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

        return redirect()->route('dashboard')
            ->with('success', 'Account created and logged in!');
    }

    /**
     * Handle login (email or username + password).
     */
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required',
            'password' => 'required',
        ]);

        $loginField = filter_var($request->email, FILTER_VALIDATE_EMAIL) ? 'email' : 'name';
        $credentials = [
            $loginField => $request->email,
            'password'  => $request->password,
        ];

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

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

            return redirect()->route('dashboard')->with('success', 'Welcome back!');
        }

        return back()->withErrors([
            'email' => 'Invalid login credentials.',
        ])->withInput();
    }

    /**
     * Logout.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();
        Cookie::queue(Cookie::forget(config('session.cookie')));

        return redirect()->route('login')->with('success', 'Logged out successfully.');
    }

    /**
     * Update profile.
     */
    public function update(Request $request, User $user)
    {
        $this->authorize('update', $user);

        $validated = $request->validate([
            'name'     => ['required', 'min:1', 'max:100', Rule::unique('users')->ignore($user->id)],
            'email'    => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'location' => ['nullable', 'max:100'],
            'password' => ['nullable', 'min:8', 'max:200', 'confirmed'],
        ]);

        if ($request->filled('password')) {
            $validated['password'] = bcrypt($request->password);
        }

        $user->update($validated);

        return back()->with('success', 'Profile updated successfully.');
    }

    /**
     * Timeline page.
     */
    public function timeline()
    {
        $posts = Post::with('user', 'comments.user')->latest()->get();
        return view('timeline', compact('posts'));
    }

    // Optional AJAX helpers
    public function checkEmail(Request $request)
    {
        $exists = User::where('email', $request->email)->exists();
        return response()->json(['exists' => $exists]);
    }

    public function checkUsername(Request $request)
    {
        $exists = User::where('name', $request->name)->exists();
        return response()->json(['exists' => $exists]);
    }
}
