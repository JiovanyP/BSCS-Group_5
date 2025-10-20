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
     * Show dashboard (paginated posts).
     */
    public function index()
    {
        $posts = Post::with(['user', 'comments', 'likes'])
            ->latest()
            ->paginate(10);

        return view('dashboard', compact('posts'));
    }

    /**
     * Show timeline (paginated posts).
     */
    public function timeline()
    {
        $posts = Post::with(['user', 'comments.user', 'likes'])
            ->latest()
            ->paginate(10);

        return view('timeline', compact('posts'));
    }

    /**
     * Show create post form.
     */
    public function create()
    {
        return view('posts.create');
    }

    /**
     * Store new post (with optional image/video/gif and location).
     */
    public function store(Request $request)
    {
        $request->validate([
            'content'  => 'nullable|string|max:1000',
            'location' => 'nullable|string|max:255',
            'image'    => 'nullable|file|mimes:jpeg,png,jpg,gif,mp4,mov,avi,webm|max:20480',
        ]);

        $imagePath = null;
        $mediaType = null;

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

            // store in storage/app/public/posts
            $imagePath = $file->store('posts', 'public');
        }

        Post::create([
            'user_id'    => Auth::id(),
            'content'    => $request->content,
            'location'   => $request->location,
            'image'      => $imagePath,
            'media_type' => $mediaType,
        ]);

        return redirect()->route('timeline')->with('success', 'Post created successfully!');
    }

    /**
     * Vote on post (up/down).
     */
    public function vote(Request $request, $id)
    {
        $request->validate(['vote' => 'required|in:up,down']);

        $post = Post::findOrFail($id);
        $post->likes()->updateOrCreate(
            ['user_id' => Auth::id()],
            ['vote_type' => $request->vote]
        );

        return response()->json([
            'success'          => true,
            'user_vote'        => $request->vote,
            'upvotes_count'    => $post->upvotes()->count(),
            'downvotes_count'  => $post->downvotes()->count(),
        ]);
    }

    /**
     * Add comment (top-level or reply).
     */
    public function addComment(Request $request, $id)
    {
        $request->validate(['content' => 'required|string|max:300']);

        $comment = Comment::create([
            'user_id'   => Auth::id(),
            'post_id'   => $id,
            'content'   => $request->content,
            'parent_id' => $request->parent_id ?? null,
        ]);

        return response()->json([
            'success'        => true,
            'id'             => $comment->id,
            'parent_id'      => $comment->parent_id,
            'user'           => Auth::user()->name,
            'avatar'         => Auth::user()->avatar ?? 'https://bootdey.com/img/Content/avatar/avatar1.png',
            'comment'        => $comment->content,
            'comments_count' => Comment::where('post_id', $id)->count(),
        ]);
    }

    /**
     * Show the edit form for a post.
     */
    public function edit(Post $post)
    {
        $this->authorize('update', $post);
        return view('posts.edit', compact('post'));
    }

    /**
     * Update a post (content, location, and optional new media).
     */
    public function update(Request $request, Post $post)
    {
        $this->authorize('update', $post);

        $request->validate([
            'content'  => 'required|string|max:1000',
            'location' => 'nullable|string|max:255',
            'image'    => 'nullable|file|mimes:jpeg,png,jpg,gif,mp4,mov,avi,webm|max:20480',
        ]);

        $imagePath = $post->image;
        $mediaType = $post->media_type;

        if ($request->hasFile('image')) {
            // delete old file if present
            if ($post->image && Storage::disk('public')->exists($post->image)) {
                Storage::disk('public')->delete($post->image);
            }

            $file = $request->file('image');
            $extension = strtolower($file->getClientOriginalExtension());

            if (in_array($extension, ['jpg', 'jpeg', 'png'])) {
                $mediaType = 'image';
            } elseif ($extension === 'gif') {
                $mediaType = 'gif';
            } elseif (in_array($extension, ['mp4', 'mov', 'avi', 'webm'])) {
                $mediaType = 'video';
            }

            $imagePath = $file->store('posts', 'public');
        }

        $post->update([
            'content'    => $request->input('content'),
            'location'   => $request->input('location'),
            'image'      => $imagePath,
            'media_type' => $mediaType,
        ]);

        return redirect()->route('timeline')->with('success', 'Post updated successfully!');
    }

    /**
     * Delete a post and its associated media file.
     */
    public function destroy(Post $post)
    {
        $this->authorize('delete', $post);

        // Delete file from storage if exists
        if ($post->image && Storage::disk('public')->exists($post->image)) {
            Storage::disk('public')->delete($post->image);
        }

        $post->delete();

        return redirect()->route('timeline')->with('success', 'Post deleted successfully!');
    }
}
