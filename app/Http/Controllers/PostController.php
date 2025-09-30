<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::with(['user', 'comments.user', 'comments.replies.user'])
            ->withCount([
                'likes as upvotes_count'   => fn($q) => $q->where('vote_type', 'up'),
                'likes as downvotes_count' => fn($q) => $q->where('vote_type', 'down'),
                'comments'
            ])
            ->latest()
            ->get()
            ->map(function($post){
                $post->userVote = $post->likes()->where('user_id', auth()->id())->value('vote_type');
                return $post;
            });

        return view('timeline', compact('posts'));
    }

    public function store(Request $request)
    {
        $request->validate(['content' => 'required|max:500']);

        Post::create([
            'user_id' => Auth::id(),
            'content' => $request->content,
        ]);

        return redirect()->route('timeline')->with('success', 'Post created!');
    }

    public function edit(Post $post)
    {
        $this->authorize('update', $post);
        return view('posts.edit', compact('post'));
    }

    public function update(Request $request, Post $post)
    {
        $this->authorize('update', $post);
        $request->validate(['content' => 'required|max:500']);
        $post->update(['content' => $request->content]);
        return redirect()->route('timeline')->with('success', 'Post updated!');
    }

    public function destroy(Post $post)
    {
        $this->authorize('delete', $post);
        $post->delete();
        return redirect()->route('timeline')->with('success', 'Post deleted!');
    }

    public function vote(Request $request, Post $post)
    {
        $request->validate(['vote' => 'required|in:upvote,downvote']);
        $user = auth()->user();
        $type = $request->vote === 'upvote' ? 'up' : 'down';

        $like = $post->likes()->where('user_id', $user->id)->first();

        if ($like && $like->vote_type === $type) {
            $like->delete();
            $status = 'removed';
            $userVote = 'none';
        } else {
            $post->likes()->updateOrCreate(
                ['user_id' => $user->id],
                ['vote_type' => $type]
            );
            $status = $type === 'up' ? 'upvoted' : 'downvoted';
            $userVote = $type;
        }

        return response()->json([
            'status'    => $status,
            'user_vote' => $userVote,
            'upvotes_count'   => $post->likes()->where('vote_type', 'up')->count(),
            'downvotes_count' => $post->likes()->where('vote_type', 'down')->count(),
        ]);
    }
}