<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
    /**
     * Display a listing of posts with user details.
     */
    public function index()
    {
        $posts = Post::with(['user:id,name,avatar'])
            ->latest()
            ->get();

        return view('timeline', compact('posts'));
    }

    /**
     * Store a newly created post.
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

        return redirect()
            ->route('timeline')
            ->with('success', 'Post created!');
    }
}
