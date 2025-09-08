<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
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

        $incomingFields['password'] = bcrypt($incomingFields['password']);
        $user = User::create($incomingFields);

        Auth::login($user);

        return redirect()->route('timeline')->with('success', 'You are successfully registered!');
    }

    /**
     * Handle user login.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->route('timeline');
        }

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

        return redirect()->route('home');
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
