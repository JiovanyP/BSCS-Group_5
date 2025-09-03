<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
    // In PostController.php
    public function index()
    {
        $posts = Post::with('user')->latest()->get();
        $posts->load('user:id,name'); // ✅ Removed avatar for now
        return view('timeline', compact('posts'));
    }

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
}