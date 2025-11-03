<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\Comment;
use App\Models\Notification;
use App\Models\Report;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class PostController extends Controller
{
    /**
     * USER: Main Dashboard (User Feed)
     * Displays paginated posts and stats (used for /dashboard or /newsfeed).
     */
    public function index()
    {
        // 📰 Regular posts for feed
        $posts = Post::with(['user', 'comments.user', 'likes'])
            ->latest()
            ->paginate(10);

        // 📊 Accident counts summary
        $accidentCounts = Post::selectRaw('accident_type, COUNT(*) as total')
            ->whereNotNull('accident_type')
            ->groupBy('accident_type')
            ->get();

        // 📍 Top locations
        $topLocations = Post::selectRaw('location, COUNT(*) as total')
            ->whereNotNull('location')
            ->groupBy('location')
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        // 🚨 Reported posts (for admin viewing convenience)
        $reportedPosts = Post::with(['user', 'reports.user'])
            ->withCount('reports')
            ->whereHas('reports')
            ->orderByDesc('reports_count')
            ->paginate(20);

        return view('newsfeed', compact('posts', 'accidentCounts', 'topLocations', 'reportedPosts'));
    }

    /**
     * USER: Public Timeline (feed)
     */
    public function timeline()
    {
        $posts = Post::with(['user', 'comments.user', 'likes'])
            ->latest()
            ->paginate(10);

        return view('timeline', compact('posts'));
    }

    /**
     * USER: Show Create Form
     */
    public function create()
    {
        return view('posts.create');
    }

    /**
     * USER: Store New Post
     */
    public function store(Request $request)
    {
        $request->validate([
            'content'        => 'nullable|string|max:1000',
            'location'       => 'nullable|string|max:255',
            'accident_type'  => 'required|string|max:100',
            'image'          => 'nullable|file|mimes:jpeg,png,jpg,gif,mp4,mov,avi,webm|max:20480',
        ]);

        $mediaType = null;
        $imagePath = null;

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $ext = strtolower($file->getClientOriginalExtension());

            // Identify media type
            if (in_array($ext, ['jpg', 'jpeg', 'png'])) $mediaType = 'image';
            elseif ($ext === 'gif') $mediaType = 'gif';
            elseif (in_array($ext, ['mp4', 'mov', 'avi', 'webm'])) $mediaType = 'video';

            $imagePath = $file->store('posts', 'public');
        }

        // Create the post
        $post = Post::create([
            'user_id'       => Auth::id(),
            'content'       => $request->content,
            'location'      => $request->location,
            'accident_type' => $request->accident_type,
            'image'         => $imagePath,
            'media_type'    => $mediaType,
        ]);

        // Optional: trigger location notifications
        if (class_exists(\App\Http\Controllers\NotificationController::class)) {
            \App\Http\Controllers\NotificationController::createLocationNotifications($post);
        }

        return redirect()->route('timeline')->with('success', 'Post created successfully!');
    }

    /**
     * USER: View Individual Post
     */
    public function viewPost($id)
    {
        $post = Post::with(['user', 'comments.user', 'comments.replies.user'])->findOrFail($id);
        return view('posts.viewpost', compact('post'));
    }

    /**
     * USER: Vote on Post (Upvote/Downvote)
     */
    public function vote(Request $request, $id)
    {
        $request->validate(['vote' => 'required|in:up,down']);
        $post = Post::findOrFail($id);
        $existing = $post->likes()->where('user_id', Auth::id())->first();

        // Undo same vote
        if ($existing && $existing->vote_type === $request->vote) {
            $existing->delete();

            // Remove related notification
            if ($post->user_id !== Auth::id()) {
                Notification::where('user_id', $post->user_id)
                    ->where('actor_id', Auth::id())
                    ->where('post_id', $post->id)
                    ->whereIn('type', ['upvote', 'downvote'])
                    ->delete();
            }

            return response()->json([
                'success' => true,
                'user_vote' => null,
                'upvotes_count' => $post->upvotes()->count(),
                'downvotes_count' => $post->downvotes()->count(),
            ]);
        }

        // Create or update vote
        $post->likes()->updateOrCreate(
            ['user_id' => Auth::id()],
            ['vote_type' => $request->vote]
        );

        // Send notification if not self-voting
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
            'success' => true,
            'user_vote' => $request->vote,
            'upvotes_count' => $post->upvotes()->count(),
            'downvotes_count' => $post->downvotes()->count(),
        ]);
    }

    /**
     * USER: Add Comment (or Reply)
     */
    public function addComment(Request $request, $id)
    {
        $request->validate(['content' => 'required|string|max:300']);
        $post = Post::findOrFail($id);

        $comment = Comment::create([
            'user_id' => Auth::id(),
            'post_id' => $id,
            'content' => $request->content,
            'parent_id' => $request->parent_id ?? null,
        ]);

        // Create Notification (reply or comment)
        if ($request->parent_id) {
            $parent = Comment::find($request->parent_id);
            if ($parent && $parent->user_id !== Auth::id()) {
                Notification::create([
                    'user_id' => $parent->user_id,
                    'actor_id' => Auth::id(),
                    'post_id' => $id,
                    'comment_id' => $comment->id,
                    'type' => 'reply',
                    'is_read' => false,
                ]);
            }
        } elseif ($post->user_id !== Auth::id()) {
            Notification::create([
                'user_id' => $post->user_id,
                'actor_id' => Auth::id(),
                'post_id' => $id,
                'comment_id' => $comment->id,
                'type' => 'comment',
                'is_read' => false,
            ]);
        }

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
     * USER: Edit Post Form
     */
    public function edit(Post $post)
    {
        $this->authorize('update', $post);
        return view('posts.edit', compact('post'));
    }

    /**
     * USER: Update Post
     */
    public function update(Request $request, Post $post)
    {
        $this->authorize('update', $post);

        $request->validate([
            'content'  => 'required|string|max:1000',
            'location' => 'nullable|string|max:255',
            'image'    => 'nullable|file|mimes:jpeg,png,jpg,gif,mp4,mov,avi,webm|max:20480',
        ]);

        $mediaType = $post->media_type;
        $imagePath = $post->image;

        if ($request->hasFile('image')) {
            if ($post->image && Storage::disk('public')->exists($post->image)) {
                Storage::disk('public')->delete($post->image);
            }

            $file = $request->file('image');
            $ext = strtolower($file->getClientOriginalExtension());
            if (in_array($ext, ['jpg', 'jpeg', 'png'])) $mediaType = 'image';
            elseif ($ext === 'gif') $mediaType = 'gif';
            elseif (in_array($ext, ['mp4', 'mov', 'avi', 'webm'])) $mediaType = 'video';

            $imagePath = $file->store('posts', 'public');
        }

        $post->update([
            'content'    => $request->content,
            'location'   => $request->location,
            'image'      => $imagePath,
            'media_type' => $mediaType,
        ]);

        return redirect()->route('timeline')->with('success', 'Post updated successfully!');
    }

    /**
     * USER: Delete Post
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
     * USER: Report Post
     */
    public function report(Request $request, Post $post)
    {
        if (Auth::id() === $post->user_id) {
            return response()->json(['error' => 'Cannot report your own post'], 403);
        }

        $request->validate([
            'reason' => ['required', Rule::in(['spam', 'violence', 'hate_speech', 'misinformation', 'other'])],
        ]);

        $existing = Report::where('user_id', Auth::id())->where('post_id', $post->id)->first();
        if ($existing) {
            return response()->json(['error' => 'You have already reported this post'], 409);
        }

        Report::create([
            'user_id' => Auth::id(),
            'post_id' => $post->id,
            'reason' => $request->reason,
        ]);

        return response()->json(['success' => true, 'message' => 'Thank you for your report.']);
    }

    /**
     * ADMIN: Dashboard (Option 2)
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
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        $reportedPosts = Post::with(['user', 'reports.user'])
            ->withCount('reports')
            ->whereHas('reports')
            ->orderByDesc('reports_count')
            ->paginate(20);

        return view('admin.dashboard', compact('accidentCounts', 'topLocations', 'reportedPosts'));
    }

    /**
     * ADMIN: Remove Post
     */
    public function adminRemove(Post $post)
    {
        if ($post->image && Storage::disk('public')->exists($post->image)) {
            Storage::disk('public')->delete($post->image);
        }

        $post->delete();

        return redirect()->back()->with('success', 'Post removed successfully!');
    }
}
