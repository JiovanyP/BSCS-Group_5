<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function login(Request $request) {
        $incomingFields = $request->validate([
            'loginname' => 'required',
            'loginpassword' => 'required'
        ]);

        if (auth() ->attempt(['name'=> $incomingFields['loginname'], 'password' => $incomingFields['loginpassword']])) {
            $request->session()->regenerate();
    
        }

        // return redirect('/');
    }

    // public function register(Request $request) {
    // $incomingFields = $request->validate([
    //     'name' => ['required', 'min:3', 'max:10', Rule::unique('users', 'name')],
    //     'email' => ['required', 'email', Rule::unique('users', 'email')],
    //     'password' => ['required', 'min:8', 'max:200', 'confirmed'] // Add 'confirmed' rule
    // ]);

    // $incomingFields['password'] = bcrypt($incomingFields['password']);
    // $user = User::create($incomingFields);

    // auth()->login($user);

    // return redirect('/')->with('success', 'You are successfully registered!');
    // }
    public function register(Request $request) {
        $incomingFields = $request->validate([
            'name' => ['required', 'min:1', 'max:100', Rule::unique('users', 'name')],
            'email' => ['required', 'email', Rule::unique('users', 'email')],
            'location' => ['required', 'min:1', 'max:100'], // Added location validation
            'password' => ['required', 'min:8', 'max:200', 'confirmed']
        ]);

        // \Log::info('Validated fields:', $incomingFields); 

        $incomingFields['password'] = bcrypt($incomingFields['password']);
        $user = User::create($incomingFields);
        
        // Debug: Check if user was created
        if ($user) {
            \Log::info('User created successfully:', $user->toArray());
        } else {
            \Log::error('User creation failed');
        }

        auth()->login($user);

        // return redirect('/')->with('success', 'You are successfully registered!');

        // Stay on the registration page with success message
        return back()->with('success', 'You are successfully registered!');
    }
}