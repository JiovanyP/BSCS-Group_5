<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\Comment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    /**
     * Show dashboard.
     */
    public function index()
    {
        $posts = Post::with(['user', 'comments.user', 'comments.replies.user', 'likes'])
            ->latest()
            ->paginate(10);

        return view('dashboard', compact('posts'));
    }

    /**
     * Show timeline.
     */
    public function timeline()
    {
        $posts = Post::with(['user', 'comments.user', 'comments.replies.user', 'likes'])
            ->latest()
            ->paginate(10);

        return view('timeline', compact('posts'));
    }

    /**
     * Store new post (with optional image/video/gif).
     */
    public function store(Request $request)
    {
        // Validation - content is required OR image is required (at least one)
        $request->validate([
            'content' => 'nullable|string|max:5000',
            'image' => 'nullable|file|mimes:jpeg,png,jpg,gif,mp4,mov,avi,webm|max:51200', // 50MB
        ]);

        // Check if at least content or image is provided
        if (!$request->filled('content') && !$request->hasFile('image')) {
            return redirect()->back()->withErrors(['content' => 'Please provide either text content or attach a media file.']);
        }

        $imagePath = null;
        $mediaType = null;

        // Handle file upload
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $extension = strtolower($file->getClientOriginalExtension());

            if (in_array($extension, ['jpg', 'jpeg', 'png'])) {
                $mediaType = 'image';
            } elseif ($extension === 'gif') {
                $mediaType = 'gif';
            } elseif (in_array($extension, ['mp4', 'mov', 'avi', 'webm'])) {
                $mediaType = 'video';
            }

            // Store file to storage/app/public/posts
            $imagePath = $file->store('posts', 'public');
        }

        // Create the post record
        Post::create([
            'user_id' => Auth::id(),
            'content' => $request->content ?? '',
            'image' => $imagePath,
            'media_type' => $mediaType,
        ]);

        return redirect()->route('timeline')->with('success', 'Post created successfully!');
    }

    /**
     * Vote on a post (AJAX).
     */
    public function vote(Request $request, $id)
    {
        $request->validate(['vote' => 'required|in:up,down']);

        $post = Post::findOrFail($id);
        
        // Check if user already voted
        $existingVote = $post->likes()->where('user_id', Auth::id())->first();

        if ($existingVote) {
            // If same vote, remove it (toggle off)
            if ($existingVote->vote_type === $request->vote) {
                $existingVote->delete();
                $userVote = null;
            } else {
                // Change vote
                $existingVote->update(['vote_type' => $request->vote]);
                $userVote = $request->vote;
            }
        } else {
            // Create new vote
            $post->likes()->create([
                'user_id' => Auth::id(),
                'vote_type' => $request->vote
            ]);
            $userVote = $request->vote;
        }

        return response()->json([
            'success' => true,
            'user_vote' => $userVote,
            'upvotes_count' => $post->upvotes()->count(),
            'downvotes_count' => $post->downvotes()->count(),
        ]);
    }

    /**
     * Add a comment (AJAX).
     */
    public function addComment(Request $request, $id)
    {
        $request->validate([
            'content' => 'required|string|max:1000',
            'parent_id' => 'nullable|exists:comments,id'
        ]);

        $post = Post::findOrFail($id);

        $comment = Comment::create([
            'user_id' => Auth::id(),
            'post_id' => $id,
            'content' => $request->content,
            'parent_id' => $request->parent_id ?? null,
        ]);

        $comment->load('user');

        return response()->json([
            'success' => true,
            'id' => $comment->id,
            'parent_id' => $comment->parent_id,
            'user' => $comment->user->name,
            'avatar' => $comment->user->avatar_url ?? asset('images/default-avatar.png'),
            'comment' => $comment->content,
            'comments_count' => $post->total_comments_count,
        ]);
    }

    /**
     * Edit a post (show edit page if needed).
     */
    public function edit(Post $post)
    {
        // Check authorization
        if (Auth::id() !== $post->user_id) {
            abort(403, 'Unauthorized action.');
        }

        return view('posts.edit', compact('post'));
    }

    /**
     * Update a post (AJAX for modal).
     */
    public function update(Request $request, Post $post)
    {
        // Check authorization
        if (Auth::id() !== $post->user_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $request->validate([
            'content' => 'required|string|max:5000',
        ]);

        $post->update([
            'content' => $request->content,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Post updated successfully',
            'content' => $post->content
        ]);
    }

    /**
     * Delete a post (AJAX).
     */
    public function destroy(Post $post)
    {
        // Check authorization
        if (Auth::id() !== $post->user_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Delete associated image/video if exists
        if ($post->image && Storage::disk('public')->exists($post->image)) {
            Storage::disk('public')->delete($post->image);
        }

        // Delete post (cascade will handle comments and likes via model boot)
        $post->delete();

        return response()->json([
            'success' => true,
            'message' => 'Post deleted successfully'
        ]);
    }
}