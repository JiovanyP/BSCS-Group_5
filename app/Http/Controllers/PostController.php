<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\Comment;
use App\Models\Notification;
use App\Models\Report;
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

        // Get accident counts by type
        $accidentCounts = Post::selectRaw('accident_type, COUNT(*) as total')
            ->whereNotNull('accident_type')
            ->groupBy('accident_type')
            ->get();

        // Get top locations by reported incidents
        $topLocations = Post::selectRaw('location, COUNT(*) as total')
            ->whereNotNull('location')
            ->groupBy('location')
            ->orderBy('total', 'desc')
            ->limit(10)
            ->get();

        // Get reported posts with reports (for admin view)
        $reportedPosts = Post::with(['user', 'reports.user'])
            ->withCount('reports')
            ->whereHas('reports')
            ->orderBy('reports_count', 'desc')
            ->paginate(20);

        return view('newsfeed', compact('posts', 'accidentCounts', 'topLocations', 'reportedPosts'));
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
            'accident_type' => 'required|string|max:100',
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

            $imagePath = $file->store('posts', 'public');
        }

        $post = Post::create([
            'user_id'    => Auth::id(),
            'content'    => $request->content,
            'location'   => $request->location,
            'accident_type' => $request->accident_type,
            'image'      => $imagePath,
            'media_type' => $mediaType,
        ]);

        \App\Http\Controllers\NotificationController::createLocationNotifications($post);

        return redirect()->route('timeline')->with('success', 'Post created successfully!');
    }

    public function viewPost($id)
    {
        $post = Post::with(['user', 'comments.user', 'comments.replies.user'])
                    ->findOrFail($id);

        return view('posts.viewpost', compact('post'));
    }

    /**
     * Vote on post - WITH UNDO FUNCTIONALITY
     */
    public function vote(Request $request, $id)
    {
        $request->validate(['vote' => 'required|in:up,down']);

        $post = Post::findOrFail($id);

        $existingVote = $post->likes()->where('user_id', Auth::id())->first();

        if ($existingVote && $existingVote->vote_type === $request->vote) {
            $existingVote->delete();

            if ($post->user_id !== Auth::id()) {
                Notification::where('user_id', $post->user_id)
                    ->where('actor_id', Auth::id())
                    ->where('post_id', $post->id)
                    ->whereIn('type', ['upvote', 'downvote'])
                    ->delete();
            }

            return response()->json([
                'success'          => true,
                'user_vote'        => null,
                'upvotes_count'    => $post->upvotes()->count(),
                'downvotes_count'  => $post->downvotes()->count(),
            ]);
        }

        $post->likes()->updateOrCreate(
            ['user_id' => Auth::id()],
            ['vote_type' => $request->vote]
        );

        if ($post->user_id !== Auth::id()) {
            Notification::where('user_id', $post->user_id)
                ->where('actor_id', Auth::id())
                ->where('post_id', $post->id)
                ->whereIn('type', ['upvote', 'downvote'])
                ->delete();

            Notification::create([
                'user_id' => $post->user_id,
                'actor_id' => Auth::id(),
                'post_id' => $post->id,
                'type' => $request->vote === 'up' ? 'upvote' : 'downvote',
                'is_read' => false,
            ]);
        }

        return response()->json([
            'success'          => true,
            'user_vote'        => $request->vote,
            'upvotes_count'    => $post->upvotes()->count(),
            'downvotes_count'  => $post->downvotes()->count(),
        ]);
    }

    /**
     * Add comment - WITH NOTIFICATION
     */
    public function addComment(Request $request, $id)
    {
        $request->validate(['content' => 'required|string|max:300']);

        $post = Post::findOrFail($id);

        $comment = Comment::create([
            'user_id'   => Auth::id(),
            'post_id'   => $id,
            'content'   => $request->content,
            'parent_id' => $request->parent_id ?? null,
        ]);

        if ($request->parent_id) {
            $parentComment = Comment::find($request->parent_id);

            if ($parentComment && $parentComment->user_id !== Auth::id()) {
                Notification::create([
                    'user_id' => $parentComment->user_id,
                    'actor_id' => Auth::id(),
                    'post_id' => $id,
                    'comment_id' => $comment->id,
                    'type' => 'reply',
                    'is_read' => false,
                ]);
            }
        } else {
            if ($post->user_id !== Auth::id()) {
                Notification::create([
                    'user_id' => $post->user_id,
                    'actor_id' => Auth::id(),
                    'post_id' => $id,
                    'comment_id' => $comment->id,
                    'type' => 'comment',
                    'is_read' => false,
                ]);
            }
        }

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

        if ($post->image && Storage::disk('public')->exists($post->image)) {
            Storage::disk('public')->delete($post->image);
        }

        $post->delete();

        return redirect()->route('timeline')->with('success', 'Post deleted successfully!');
    }

    /**
     * Report a post.
     */
    public function report(Request $request, Post $post)
    {
        if (Auth::id() === $post->user_id) {
            return response()->json(['error' => 'Cannot report your own post'], 403);
        }

        $request->validate([
            'reason' => 'required|in:spam,violence,hate_speech,misinformation,other'
        ]);

        $existingReport = Report::where('user_id', Auth::id())
            ->where('post_id', $post->id)
            ->first();

        if ($existingReport) {
            return response()->json(['error' => 'You have already reported this post'], 409);
        }

        Report::create([
            'user_id' => Auth::id(),
            'post_id' => $post->id,
            'reason'  => $request->reason
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Thank you for your report. We will review it shortly.'
        ]);
    }

    /**
     * Admin Dashboard
     */
    public function adminDashboard()
    {
        $accidentCounts = Post::selectRaw('accident_type, COUNT(*) as total')
            ->whereNotNull('accident_type')
            ->groupBy('accident_type')
            ->get();

        $topLocations = Post::selectRaw('location, COUNT(*) as total')
            ->whereNotNull('location')
            ->groupBy('location')
            ->orderBy('total', 'desc')
            ->limit(10)
            ->get();

        $reportedPosts = Post::with(['user', 'reports.user'])
            ->withCount('reports')
            ->whereHas('reports')
            ->orderBy('reports_count', 'desc')
            ->paginate(20);

        return view('admin.dashboard', compact('accidentCounts', 'topLocations', 'reportedPosts'));
    }

    /**
     * Admin remove post
     *
     * NOTE: returns JSON (suitable for AJAX). Keep route as POST and name = admin.posts.remove.
     */
    public function adminRemove(Post $post)
    {
        // delete associated media if present
        if ($post->image && Storage::disk('public')->exists($post->image)) {
            Storage::disk('public')->delete($post->image);
        }

        $post->delete();

        // Return JSON for AJAX call (do NOT redirect)
        return response()->json([
            'success' => true,
            'message' => 'Post removed successfully!',
            'post_id' => $post->id,
        ]);
    }
}
