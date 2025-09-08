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
        $posts = Post::with(['user', 'comments.user', 'comments.replies.user'])
            ->withCount([
                'likes as upvotes_count'   => fn($q) => $q->where('vote_type', 'up'),
                'likes as downvotes_count' => fn($q) => $q->where('vote_type', 'down'),
                'comments'
            ])
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
     * Edit a post.
     */
    public function edit(Post $post)
    {
        $this->authorize('update', $post);
        return view('posts.edit', compact('post'));
    }

    /**
     * Update a post.
     */
    public function update(Request $request, Post $post)
    {
        $this->authorize('update', $post);

        $request->validate([
            'content' => 'required|max:500',
        ]);

        $post->update([
            'content' => $request->content,
        ]);

        return redirect()->route('timeline')->with('success', 'Post updated!');
    }

    /**
     * Delete a post.
     */
    public function destroy(Post $post)
    {
        $this->authorize('delete', $post);

        $post->delete();

        return redirect()->route('timeline')->with('success', 'Post deleted!');
    }

    /**
     * Handle voting (upvote/downvote).
     */
    private function handleVote(Post $post, string $type)
    {
        $user = auth()->user();
        $like = $post->likes()->where('user_id', $user->id)->first();

        if ($like && $like->vote_type === $type) {
            // Toggle off
            $like->delete();
            $status = 'removed';
            $userVote = 'none';
        } else {
            // Update or create vote
            $post->likes()->updateOrCreate(
                ['user_id' => $user->id],
                ['vote_type' => $type]
            );
            $status = $type === 'up' ? 'upvoted' : 'downvoted';
            $userVote = $type;
        }

        return response()->json([
            'status'           => $status,
            'user_vote'        => $userVote,
            'upvotes_count'    => $post->likes()->where('vote_type', 'up')->count(),
            'downvotes_count'  => $post->likes()->where('vote_type', 'down')->count(),
        ]);
    }

    /**
     * Upvote a post.
     */
    public function upvote(Post $post)
    {
        return $this->handleVote($post, 'up');
    }

    /**
     * Downvote a post.
     */
    public function downvote(Post $post)
    {
        return $this->handleVote($post, 'down');
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
            'parent_id' => $request->parent_id,
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
