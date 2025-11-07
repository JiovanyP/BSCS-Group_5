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

        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            $request->validate([
                'avatar' => 'image|max:2048', // 2MB max
            ]);

            // Delete old avatar if exists
            if ($user->avatar && Storage::exists('public/' . $user->avatar)) {
                Storage::delete('public/' . $user->avatar);
            }

            // Store new avatar
            $path = $request->file('avatar')->store('avatars', 'public');
            $user->avatar = $path;
            $user->save();

            // ✅ Return JSON if AJAX request
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Profile picture updated successfully!',
                    'avatar' => $user->avatar_url, // Use the accessor
                ]);
            }

            // ✅ Fallback for normal form submission
            return redirect()->back()->with('success', 'Profile picture updated successfully!');
        }

        // Handle personal info update (optional)
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'address' => 'nullable|string|max:255',
        ]);

        $user->update($request->only('name', 'email', 'address'));

        return redirect()->back()->with('success', 'Personal information updated successfully!');
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