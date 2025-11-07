<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\User;

class UserController extends Controller
{
    /**
     * Show login form
     */
    public function showLoginForm()
    {
        return view('login');
    }

    /**
     * Handle login
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        // Check if user exists and is not banned/suspended
        $user = User::where('email', $credentials['email'])->first();

        if ($user) {
            if ($user->isBanned()) {
                return back()->withErrors([
                    'email' => 'Your account has been banned. Please contact support.',
                ])->withInput($request->only('email'));
            }

            if ($user->isSuspended()) {
                return back()->withErrors([
                    'email' => 'Your account has been suspended. Please contact support.',
                ])->withInput($request->only('email'));
            }
        }

        // Attempt login
        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();

            // Update last login
            Auth::user()->update(['last_login_at' => now()]);

            return redirect()->intended(route('timeline'))->with('success', 'Welcome back!');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->withInput($request->only('email'));
    }

    /**
     * Handle logout
     */
    public function logout(Request $request)
    {
        \Log::info('Logout method called'); // ✅ Add this line
        
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        \Log::info('Logout completed'); // ✅ Add this line
        
        return redirect()->route('login')->with('success', 'You have been logged out successfully.');
    }
    /**
     * AJAX: Check if email exists
     */
    public function checkEmail(Request $request)
    {
        $exists = User::where('email', $request->email)->exists();
        return response()->json(['exists' => $exists]);
    }

    /**
     * AJAX: Check if username exists
     */
    public function checkUsername(Request $request)
    {
        $exists = User::where('name', $request->username)->exists();
        return response()->json(['exists' => $exists]);
    }

    /**
     * Show user profile
     */
    public function profile()
    {
        return view('profile', [
            'user' => Auth::user()
        ]);
    }

    /**
     * Update user profile
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'bio' => 'nullable|string|max:500',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $data = $request->only(['name', 'email', 'phone', 'address', 'bio']);

        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            // Delete old avatar if exists
            if ($user->avatar && !Str::startsWith($user->avatar, ['http://', 'https://'])) {
                $oldPath = storage_path('app/public/' . ltrim($user->avatar, 'storage/'));
                if (file_exists($oldPath)) {
                    unlink($oldPath);
                }
            }

            // Store new avatar
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
            $data['avatar'] = $avatarPath;
        }

        $user->update($data);

        return back()->with('success', 'Profile updated successfully!');
    }

    /**
     * Change password
     */
    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required',
            'new_password' => 'required|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator);
        }

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }

        $user->update([
            'password' => Hash::make($request->new_password)
        ]);

        return back()->with('success', 'Password changed successfully!');
    }

    /**
     * Delete account
     */
    public function deleteAccount(Request $request)
    {
        $user = Auth::user();

        // Verify password before deletion
        if (!Hash::check($request->password, $user->password)) {
            return back()->withErrors(['password' => 'Password is incorrect.']);
        }

        Auth::logout();
        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Your account has been deleted.');
    }
}