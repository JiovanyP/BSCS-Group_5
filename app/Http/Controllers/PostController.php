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

        return redirect()->route('timeline')->with('success', 'Post created successfully!');
    }

    /**
     * Shared voting logic for upvotes/downvotes.
     */
    protected function handleVote(Post $post, string $type)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        $type = $type === 'up' ? 'up' : 'down';
        $existingVote = $post->likes()->where('user_id', $user->id)->first();

        if ($existingVote && $existingVote->vote_type === $type) {
            // Toggle off if same vote clicked again
            $existingVote->delete();
            $userVote = 'none';
        } else {
            // Create or switch vote
            $post->likes()->updateOrCreate(
                ['user_id' => $user->id],
                ['vote_type' => $type]
            );
            $userVote = $type;
        }

        return response()->json([
            'status'           => 'success',
            'user_vote'        => $userVote,
            'upvotes_count'    => $post->likes()->where('vote_type', 'up')->count(),
            'downvotes_count'  => $post->likes()->where('vote_type', 'down')->count(),
        ]);
    }

    /**
     * Handle upvote action.
     */
    public function upvote(Post $post)
    {
        return $this->handleVote($post, 'up');
    }

    /**
     * Handle downvote action.
     */
    public function downvote(Post $post)
    {
        return $this->handleVote($post, 'down');
    }

    /**
     * Add a comment to a post.
     */
    public function comment(Request $request, Post $post)
    {
        $request->validate([
            'content' => 'required|string|max:500',
        ]);

        $comment = $post->comments()->create([
            'user_id'   => Auth::id(),
            'content'   => $request->content,
            'parent_id' => $request->parent_id,
        ]);

        return response()->json([
            'id'              => $comment->id,
            'comment'         => $comment->content,
            'user'            => $comment->user->name,
            'avatar'          => $comment->user->avatar ?? 'https://bootdey.com/img/Content/avatar/avatar2.png',
            'comments_count'  => $post->comments()->count(),
        ]);
    }
}
