<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class AdminPostController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    /**
     * Show the form for creating a new admin post
     */
    public function create()
    {
        return view('admin.posts.create');
    }

    /**
     * Store a newly created admin post
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'content' => 'required|string|max:5000',
            'accident_type' => 'required|string|max:100',
            'other_type' => 'nullable|string|max:100',
            'location' => 'required|string|max:255',
            'other_location' => 'nullable|string|max:255',
            'image' => 'nullable|file|mimes:jpeg,png,jpg,gif,mp4,mov,avi|max:20480', // 20MB max
        ]);

        try {
            $admin = Auth::guard('admin')->user();

            // Handle "Others" for accident type
            $accidentType = $validated['accident_type'] === "Others"
                ? ($validated['other_type'] ?? null)
                : $validated['accident_type'];

            // Handle "Others" for location
            $location = $validated['location'] === "Others"
                ? ($validated['other_location'] ?? null)
                : $validated['location'];

            $postData = [
                'user_id' => $admin->id,
                'is_admin_post' => true,
                'content' => $validated['content'],
                'accident_type' => $accidentType,
                'location' => $location,
            ];

            // Handle media upload (image or video)
            if ($request->hasFile('image')) {
                $file = $request->file('image');
                // Store without '/storage/' prefix - just the path
                $path = $file->store('posts', 'public');

                // Save ONLY the path (posts/xyz.jpg), NOT the full URL
                $postData['image_url'] = $path;
                
                // Determine media type
                $mimeType = $file->getMimeType();
                if (str_starts_with($mimeType, 'video/')) {
                    $postData['media_type'] = 'video';
                } elseif (str_starts_with($mimeType, 'image/gif')) {
                    $postData['media_type'] = 'gif';
                } else {
                    $postData['media_type'] = 'image';
                }
            }

            // Create post
            $post = Post::create($postData);

            return redirect()->route('admin.dashboard')
                ->with('success', 'Post created successfully!');

        } catch (\Throwable $e) {
            Log::error('Admin post creation failed', [
                'admin_id' => Auth::guard('admin')->id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()->withInput()
                ->withErrors(['error' => 'Failed to create post: ' . $e->getMessage()]);
        }
    }

    /**
     * Show list of admin posts
     */
    public function index()
    {
        $admin = Auth::guard('admin')->user();

        $posts = Post::where('user_id', $admin->id)
            ->where('is_admin_post', true)
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.posts.index', compact('posts'));
    }

    /**
     * Delete admin post
     */
    public function destroy(Post $post)
    {
        // Only admin posts can be deleted here
        if (!$post->is_admin_post) {
            return back()->withErrors(['error' => 'This is not an admin post.']);
        }

        try {
            // Delete media file if exists
            if ($post->attributes['image_url']) {
                $path = $post->attributes['image_url'];
                // Remove '/storage/' if present
                $path = str_replace('/storage/', '', $path);
                $path = str_replace('storage/', '', $path);
                
                if (Storage::disk('public')->exists($path)) {
                    Storage::disk('public')->delete($path);
                }
            }

            $post->delete();

            return back()->with('success', 'Post deleted successfully.');

        } catch (\Throwable $e) {
            Log::error('Admin post deletion failed', [
                'post_id' => $post->id,
                'error' => $e->getMessage()
            ]);

            return back()->withErrors(['error' => 'Failed to delete post.']);
        }
    }
}