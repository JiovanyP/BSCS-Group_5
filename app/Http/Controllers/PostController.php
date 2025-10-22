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
            ->having('reports_count', '>', 0)
            ->orderBy('reports_count', 'desc')
            ->paginate(20);

        return view('dashboard', compact('posts', 'accidentCounts', 'topLocations', 'reportedPosts'));
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
            'accident_type' => 'required|string|max:100', // ADD THIS VALIDATION
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

        // ✅ Create the post record WITH accident_type
        $post = Post::create([
            'user_id'    => Auth::id(),
            'content'    => $request->content,
            'location'   => $request->location,
            'accident_type' => $request->accident_type, // ADD THIS LINE
            'image'      => $imagePath,
            'media_type' => $mediaType,
        ]);

        // 🚨 Trigger location-based notifications
        \App\Http\Controllers\NotificationController::createLocationNotifications($post);

        return redirect()->route('timeline')->with('success', 'Post created successfully!');
    }

    /**
     * Vote on post - WITH NOTIFICATION
     */
    public function vote(Request $request, $id)
    {
        $request->validate(['vote' => 'required|in:up,down']);

        $post = Post::findOrFail($id);
        
        // Get existing vote if any
        $existingVote = $post->likes()->where('user_id', Auth::id())->first();
        
        // Update or create the vote
        $post->likes()->updateOrCreate(
            ['user_id' => Auth::id()],
            ['vote_type' => $request->vote]
        );

        // Create notification ONLY if voting on someone else's post
        if ($post->user_id !== Auth::id()) {
            // Delete old notification if vote type changed
            if ($existingVote && $existingVote->vote_type !== $request->vote) {
                Notification::where('user_id', $post->user_id)
                    ->where('actor_id', Auth::id())
                    ->where('post_id', $post->id)
                    ->whereIn('type', ['upvote', 'downvote'])
                    ->delete();
            }

            // Create new notification
            Notification::updateOrCreate(
                [
                    'user_id' => $post->user_id,
                    'actor_id' => Auth::id(),
                    'post_id' => $post->id,
                    'type' => $request->vote === 'up' ? 'upvote' : 'downvote',
                ],
                [
                    'is_read' => false,
                    'created_at' => now(),
                ]
            );
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

        // Create notification
        if ($request->parent_id) {
            // This is a reply - notify the comment owner
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
            // This is a top-level comment - notify the post owner
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

    /**
     * Report a post.
     */
    public function report(Request $request, Post $post)
    {
        // Prevent users from reporting their own posts
        if (Auth::id() === $post->user_id) {
            return response()->json(['error' => 'Cannot report your own post'], 403);
        }

        $request->validate([
            'reason' => 'required|in:spam,violence,hate_speech,misinformation,other'
        ]);

        // Check if user has already reported this post
        $existingReport = Report::where('user_id', Auth::id())
            ->where('post_id', $post->id)
            ->first();

        if ($existingReport) {
            return response()->json(['error' => 'You have already reported this post'], 409);
        }

        // Create the report
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

        // Get reported posts with reports
        $reportedPosts = Post::with(['user', 'reports.user'])
            ->join('reports', 'posts.id', '=', 'reports.post_id')
            ->select('posts.*', \DB::raw('COUNT(reports.id) as reports_count'))
            ->groupBy('posts.id')
            ->orderBy('reports_count', 'desc')
            ->paginate(20);

        return view('dashboard', compact('accidentCounts', 'topLocations', 'reportedPosts'));
    }

    /**
     * Admin remove post
     */
    public function adminRemove(Post $post)
    {
        // Delete file from storage if exists
        if ($post->image && Storage::disk('public')->exists($post->image)) {
            Storage::disk('public')->delete($post->image);
        }

        $post->delete();

        return redirect()->back()->with('success', 'Post removed successfully!');
    }
}
