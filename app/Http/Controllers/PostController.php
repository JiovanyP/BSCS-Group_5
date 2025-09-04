<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
    /**
     * Show timeline with posts.
     */
    public function index()
    {
        // ✅ Load posts with user, likes count, and comments (with replies + users)
        $posts = Post::with(['user', 'comments.user', 'comments.replies.user'])
            ->withCount(['likes', 'comments'])
            ->latest()
            ->get();

        return view('timeline', compact('posts'));
    }

    /**
     * Store a new post.
     */
    public function store(Request $request)
    {
        $request->validate([
            'content' => 'required|max:500',
        ]);

        Post::create([
            'user_id' => Auth::id(),
            'content' => $request->content,
        ]);

        return redirect()->route('timeline')->with('success', 'Post created!');
    }

    /**
     * Toggle like on a post.
     */
    public function like(Post $post)
    {
        $user = auth()->user();

        // Toggle like
        $like = $post->likes()->where('user_id', $user->id)->first();
        if ($like) {
            $like->delete();
            $liked = false;
        } else {
            $post->likes()->create(['user_id' => $user->id]);
            $liked = true;
        }

        return response()->json([
            'liked'       => $liked,
            'likes_count' => $post->likes()->count()
        ]);
    }

    /**
     * Add a comment or reply to a post.
     */
    public function comment(Request $request, Post $post)
    {
        $request->validate([
            'content' => 'required|string|max:500',
        ]);

        $comment = $post->comments()->create([
            'user_id'   => auth()->id(),
            'content'   => $request->content,
            'parent_id' => $request->parent_id, // ✅ support replies
        ]);

        return response()->json([
            'id'             => $comment->id,
            'comment'        => $comment->content,
            'user'           => $comment->user->name,
            'parent_id'      => $comment->parent_id,
            'comments_count' => $post->comments()->count(),
        ]);
    }
}
