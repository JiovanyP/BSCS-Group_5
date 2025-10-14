<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function update(Request $request)
{
    $request->validate([
        'avatar' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
    ]);

    $user = Auth::user();

    $path = $request->file('avatar')->store('avatars', 'public'); 
    // stores in storage/app/public/avatars

    $user->avatar = '/storage/' . $path;
    $user->save();

    return redirect()->back()->with('success', 'Profile updated!');
}
}
