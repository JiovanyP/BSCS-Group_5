<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    /**
     * Update the user's avatar (profile picture).
     *
     * Stores files on the 'public' disk under storage/app/public/avatars
     * and saves the relative path (e.g. "avatars/abc.jpg") to the DB.
     */
    public function update(Request $request)
    {
        $request->validate([
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $user = Auth::user();

        if ($request->hasFile('avatar')) {
            // Store under storage/app/public/avatars
            $path = $request->file('avatar')->store('avatars', 'public');

            // Store relative path only (avatars/xxx.jpg)
            $user->avatar = $path;
            $user->save();
        }

        return redirect()->back()->with('success', 'Profile updated!');
    }
}
