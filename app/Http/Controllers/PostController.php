<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\Comment;
use App\Models\PostLike;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
    /**
     * Show dashboard (optional).
     */
    public function index()
    {
        // Use pagination instead of loading all posts
        $posts = Post::with(['user', 'comments', 'likes'])
            ->latest()
            ->paginate(10);

        return view('dashboard', compact('posts'));
    }

    /**
     * Show timeline page.
     */
    public function timeline()
    {
        $posts = Post::with(['user', 'comments.user', 'likes'])
            ->latest()
            ->paginate(10);

        return view('timeline', compact('posts'));
    }

    /**
     * Store a new post.
     */
    public function store(Request $request)
    {
        $request->validate([
            'content' => 'required|string|max:500',
        ]);

        Post::create([
            'user_id' => Auth::id(),
            'content' => $request->content,
        ]);

        return redirect()->route('timeline')->with('success', 'Post created successfully!');
    }

    /**
     * Handle upvote/downvote
     */
    public function vote(Request $request, $id)
    {
        $request->validate([
            'vote' => 'required|in:up,down',
        ]);

        $post = Post::findOrFail($id);

        $post->likes()->updateOrCreate(
            ['user_id' => Auth::id()],
            ['vote_type' => $request->vote]
        );

        return response()->json([
            'success' => true,
            'user_vote' => $request->vote,
            'upvotes_count' => $post->upvotes()->count(),
            'downvotes_count' => $post->downvotes()->count(),
        ]);
    }

    /**
     * Add comment or reply
     */
    public function addComment(Request $request, $id)
    {
        $request->validate([
            'content' => 'required|string|max:300',
        ]);

        $comment = Comment::create([
            'user_id' => Auth::id(),
            'post_id' => $id,
            'content' => $request->content,
            'parent_id' => $request->parent_id ?? null,
        ]);

        return response()->json([
            'success' => true,
            'id' => $comment->id,
            'parent_id' => $comment->parent_id,
            'user' => Auth::user()->name,
            'avatar' => Auth::user()->avatar ?? 'https://bootdey.com/img/Content/avatar/avatar1.png',
            'comment' => $comment->content,
            'comments_count' => Comment::where('post_id', $id)->count(),
        ]);
    }

    /**
     * Edit post.
     */
    public function edit(Post $post)
    {
        $this->authorize('update', $post);
        return view('posts.edit', compact('post'));
    }

    /**
     * Update post.
     */
    public function update(Request $request, Post $post)
    {
        $this->authorize('update', $post);

        $request->validate([
            'content' => 'required|string|max:500',
        ]);

        $post->update(['content' => $request->content]);

        return redirect()->route('timeline')->with('success', 'Post updated successfully.');
    }

    /**
     * Delete post.
     */
    public function destroy(Post $post)
    {
        // Only allow the owner to delete
        if ($post->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $post->delete();

        return response()->json(['success' => true]);
    }
}
