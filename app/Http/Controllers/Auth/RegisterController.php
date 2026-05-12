<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use App\Models\User;

class RegisterController extends Controller
{
    // Show registration form
    public function showForm()
    {
        return view('register');
    }

    // Step 1: Send verification code when user clicks "Register"
    public function sendCode(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'location' => 'required|string|max:255',
            'password' => 'required|confirmed|min:6',
        ]);

        // Generate a 6-digit code
        $code = rand(100000, 999999);

        // Store code + user data temporarily in session
        session([
            'verify_code' => $code,
            'user_data' => $request->only('name', 'email', 'location', 'password')
        ]);

        // Send verification code to user's email
        Mail::raw("Your verification code is: $code", function ($message) use ($request) {
            $message->to($request->email)
                    ->subject('Email Verification Code');
        });

        return response()->json([
            'success' => true,
            'message' => 'Verification code sent successfully.'
        ]);
    }

    // Step 2: Show verification input page
    public function showVerifyForm()
    {
        return view('verify');
    }

    // Step 3: Handle code verification and create user
    public function verifyCode(Request $request)
    {
        $request->validate(['code' => 'required|numeric']);

        if (session('verify_code') != $request->code) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid verification code.'
            ]);
        }

        $userData = session('user_data');
        if (!$userData) {
            return response()->json([
                'success' => false,
                'message' => 'Session expired. Please register again.'
            ]);
        }

        // Create user
        $user = User::create([
            'name' => $userData['name'],
            'email' => $userData['email'],
            'location' => $userData['location'],
            'password' => Hash::make($userData['password']),
        ]);

        // Clear session data
        session()->forget(['verify_code', 'user_data']);

        // Auto login the user
        auth()->login($user);

        return response()->json([
            'success' => true,
            'message' => 'Email verified successfully! Account created.'
        ]);
    }

    // Fallback registration (not used anymore)
    public function register(Request $request)
    {
        return redirect()->route('register')->withErrors('Use verification flow.');
    }
}
