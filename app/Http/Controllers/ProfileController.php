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

    // Update profile (avatar and personal info)
    public function update(Request $request)
    {
        $user = Auth::user();

        // 1. Handle Avatar Upload (Keep existing logic)
        if ($request->hasFile('avatar')) {
            $request->validate(['avatar' => 'image|max:2048']);
            
            if ($user->avatar && Storage::exists('public/' . $user->avatar)) {
                Storage::delete('public/' . $user->avatar);
            }

            $path = $request->file('avatar')->store('avatars', 'public');
            $user->avatar = $path;
            $user->save();

            if ($request->ajax()) {
                return response()->json([
                    'success' => true, 
                    'message' => 'Profile picture updated!', 
                    'avatar' => $user->avatar_url
                ]);
            }
            return redirect()->back()->with('success', 'Profile picture updated successfully!');
        }

        // 2. Handle Text Data Update (Name, Email, Phone, Location)
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|max:255|unique:users,email,' . $user->id,
            'phone'    => 'nullable|string|max:20',  // Matches your DB varchar(20)
            'location' => 'nullable|string|max:255', // Matches your DB varchar(255)
        ]);

        $user->update($request->only('name', 'email', 'phone', 'location'));

        return redirect()->back()->with('success', 'Profile updated successfully!');
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

        return response()->json([
            'success' => true,
            'message' => 'Profile picture removed successfully!'
        ]);
    }

    // Get edit modal content
    public function getEditModal()
    {
        return view('partials.edit-modal')->render();
    }
}