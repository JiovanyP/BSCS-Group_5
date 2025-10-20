<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Post;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    // Show the profile page
    public function index()
    {
        $user = Auth::user();

        // Get user's posts with pagination
        $posts = Post::where('user_id', $user->id)
                     ->latest()
                     ->with(['comments.user', 'comments.replies.user'])
                     ->paginate(10);

        return view('profile', compact('user', 'posts'));
    }

    // Update avatar
    public function update(Request $request)
    {
        $request->validate([
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $user = Auth::user();

        if ($request->hasFile('avatar')) {
            // Delete old avatar if exists
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }

            // Store new avatar
            $path = $request->file('avatar')->store('avatars', 'public');
            $user->avatar = $path;
            $user->save();
        }

        return redirect()->back()->with('success', 'Profile updated!');
    }

    // Remove avatar
    public function removeAvatar()
    {
        $user = Auth::user();

        if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
            Storage::disk('public')->delete($user->avatar);
        }

        $user->avatar = null;
        $user->save();

        return response()->json(['success' => true]);
    }

    // Optional: Accessor for avatar URL
    public function getAvatarUrlAttribute()
    {
        return $this->avatar ? asset('storage/' . $this->avatar) : asset('images/default-avatar.png');
    }
}
