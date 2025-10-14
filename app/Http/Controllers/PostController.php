<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
    /**
     * Display timeline with posts, votes, and comments.
     */
    public function index()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $posts = Post::with(['user', 'comments.user', 'comments.replies.user'])
            ->withCount([
                'likes as upvotes_count'   => fn($q) => $q->where('vote_type', 'up'),
                'likes as downvotes_count' => fn($q) => $q->where('vote_type', 'down'),
                'comments as total_comments_count'
            ])
            ->latest()
            ->get()
            ->map(function ($post) {
                $post->user_vote = $post->likes()
                    ->where('user_id', Auth::id())
                    ->value('vote_type') ?? 'none';
                return $post;
            });

        return view('dashboard', compact('posts'));
    }

    /**
     * Store a new post.
     */
    public function store(Request $request)
    {
        $request->validate([
            'content' => 'required|max:500',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('posts', 'public');
        }

        Post::create([
            'user_id' => Auth::id(),
            'content' => $request->input('content'),
        ]);

        return redirect()
            ->route('dashboard')
            ->with('success', 'Post created!');
    }

    /**
     * Show the form for editing the specified post.
     */
    public function edit(Post $post)
    {
        $this->authorize('update', $post);
        return view('posts.edit', compact('post'));
    }

    /**
     * Update the specified post in storage.
     */
    public function update(Request $request, Post $post)
    {
        $this->authorize('update', $post);

        $request->validate([
            'content' => 'required|max:500',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $imagePath = $post->image;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('posts', 'public');
        }

        $post->update([
            'content' => $request->input('content'),
            'image' => $imagePath,
        ]);

        return redirect()
            ->route('timeline')
            ->with('success', 'Post updated!');
    }

    /**
     * Remove the specified post from storage.
     */
    public function destroy(Post $post)
    {
        $this->authorize('delete', $post);
        $post->delete();

        return redirect()
            ->route('timeline')
            ->with('success', 'Post deleted!');
    }
}
