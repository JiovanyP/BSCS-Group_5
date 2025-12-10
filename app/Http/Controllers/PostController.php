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

        // Accident counts by type
        $accidentCounts = Post::selectRaw('accident_type, COUNT(*) as total')
            ->whereNotNull('accident_type')
            ->groupBy('accident_type')
            ->get();

        // Top 10 locations by reports
        $topLocations = Post::selectRaw('location, COUNT(*) as total')
            ->whereNotNull('location')
            ->groupBy('location')
            ->orderBy('total', 'desc')
            ->limit(10)
            ->get();

        // Reported posts for admin
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
     * Store new post (with optional media and location).
     */
        public function store(Request $request)
    {
        $request->validate([
            'content'             => 'required|string|max:1000',
            'final_accident_type' => 'required|string|max:100',
            'final_location'      => 'required|string|max:255',
            'image'               => 'nullable|file|mimes:jpeg,png,jpg,gif,mp4,mov,avi,webm|max:20480',
        ]);

        $imagePath = null;
        $mediaType = null;

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $extension = strtolower($file->getClientOriginalExtension());

            if (in_array($extension, ['jpg', 'jpeg', 'png'])) $mediaType = 'image';
            elseif ($extension === 'gif') $mediaType = 'gif';
            elseif (in_array($extension, ['mp4', 'mov', 'avi', 'webm'])) $mediaType = 'video';

            $imagePath = $file->store('posts', 'public');
        }

        $post = Post::create([
            'user_id'       => Auth::id(),
            'content'       => $request->content,
            'accident_type' => $request->final_accident_type,
            'location'      => $request->final_location,
            'image_url'     => $imagePath, // ✅ changed from 'image'
            'media_type'    => $mediaType,
        ]);

        \App\Http\Controllers\NotificationController::createLocationNotifications($post);

        return redirect()->route('timeline')->with('success', 'Post created successfully!');
    }
    /**
     * Show a single post view.
     */
    public function viewPost($id)
    {
        $post = Post::with([
            'user',
            'comments.user',
            'comments.replies.user',
            'upvotes',
            'downvotes'
        ])->findOrFail($id);

        return view('posts.viewpost', compact('post'));
    }

    /**
     * Vote on a post (with undo functionality).
     */
    public function vote(Request $request, $id)
    {
        $request->validate(['vote' => 'required|in:up,down']);
        $post = Post::findOrFail($id);

        if (!Auth::check()) {
            return response()->json(['error' => 'You must be logged in to vote.'], 401);
        }

        $existingVote = $post->likes()->where('user_id', Auth::id())->first();

        // Undo vote
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

        // Create/update vote
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
                'user_id'  => $post->user_id,
                'actor_id' => Auth::id(),
                'post_id'  => $post->id,
                'type'     => $request->vote === 'up' ? 'upvote' : 'downvote',
                'is_read'  => false,
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
                    'user_id'    => $parentComment->user_id,
                    'actor_id'   => Auth::id(),
                    'post_id'    => $id,
                    'comment_id' => $comment->id,
                    'type'       => 'reply',
                    'is_read'    => false,
                ]);
            }
        } elseif ($post->user_id !== Auth::id()) {
            Notification::create([
                'user_id'    => $post->user_id,
                'actor_id'   => Auth::id(),
                'post_id'    => $id,
                'comment_id' => $comment->id,
                'type'       => 'comment',
                'is_read'    => false,
            ]);
        }

        return response()->json([
            'success'        => true,
            'id'             => $comment->id,
            'parent_id'      => $comment->parent_id,
            'user'           => Auth::user()->name,
            'avatar'         => Auth::user()->avatar_url,
            'comment'        => $comment->content,
            'comments_count' => Comment::where('post_id', $id)->count(),
        ]);
    }

    /**
     * Reply to a comment
     */
   /**
 * Reply to a comment
 */
    public function reply(Request $request, $commentId)
    {
        $request->validate(['content' => 'required|string|max:300']);

        $parentComment = Comment::findOrFail($commentId);

        $reply = Comment::create([
            'user_id'   => Auth::id(),
            'post_id'   => $parentComment->post_id,
            'content'   => $request->content,
            'parent_id' => $commentId,
        ]);

        if ($parentComment->user_id !== Auth::id()) {
            Notification::create([
                'user_id'    => $parentComment->user_id,
                'actor_id'   => Auth::id(),
                'post_id'    => $parentComment->post_id,
                'comment_id' => $reply->id,
                'type'       => 'reply',
                'is_read'    => false,
            ]);
        }

        // ✅ CHANGED: Return 'content' instead of 'comment' for consistency
        return response()->json([
            'success'        => true,
            'id'             => $reply->id,
            'parent_id'      => $reply->parent_id,
            'user'           => Auth::user()->name,
            'avatar'         => Auth::user()->avatar_url,
            'content'        => $reply->content,  // ✅ Changed from 'comment' to 'content'
            'comments_count' => Comment::where('post_id', $parentComment->post_id)->count(),
        ]);
    }

    /**
     * Edit post view.
     */
    public function edit(Post $post)
    {
        $this->authorize('update', $post);
        return view('posts.edit', compact('post'));
    }

    /**
     * Update post.
     */
public function update(Request $request, Post $post)
    {
        $this->authorize('update', $post);

        // 1. Combine Validation Rules
        $request->validate([
            'content'        => 'required|string|max:1000',
            'accident_type'  => 'required|string|max:100',
            'location'       => 'required|string|max:255',
            'other_type'     => 'nullable|string|max:100', 
            'other_location' => 'nullable|string|max:255',
            'image'          => 'nullable|file|mimes:jpeg,png,jpg,gif,mp4,mov,avi,webm|max:20480',
        ]);

        // 2. Image Handling (Using logic from feat/ui for cleanup + correct column name)
        $imagePath = $post->image_url; // consistent with store() method
        $mediaType = $post->media_type;

        if ($request->hasFile('image')) {
            // Delete old image if it exists
            if ($post->image_url && Storage::disk('public')->exists($post->image_url)) {
                Storage::disk('public')->delete($post->image_url);
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

        // 3. Business Logic for "Others" (From main branch)
        $finalAccidentType = ($request->accident_type === 'Others') 
            ? $request->other_type 
            : $request->accident_type;

        $finalLocation = ($request->location === 'Others') 
            ? $request->other_location 
            : $request->location;

        // 4. Update the Post
        $post->update([
            'content'       => $request->content,
            'accident_type' => $finalAccidentType,
            'location'      => $finalLocation,
            'image_url'     => $imagePath, // Corrected to image_url
            'media_type'    => $mediaType,
        ]);

        return redirect()->route('timeline')->with('success', 'Post updated successfully!');
    }
    /**
     * Delete a post (uses policy for auth + model cascade cleanup).
     */
    public function destroy(Post $post)
    {
        $this->authorize('delete', $post);
        
        // Delete associated media if exists
        if ($post->image && Storage::disk('public')->exists($post->image)) {
            Storage::disk('public')->delete($post->image);
        }
        
        $post->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Post deleted successfully!'
        ]);
    }

    /**
     * Report a post (predefined enum-safe reasons).
     */
    public function report(Request $request, Post $post)
    {
        if (Auth::id() === $post->user_id) {
            return response()->json([
                'success' => false,
                'message' => 'You cannot report your own post.'
            ], 403);
        }

        $request->validate([
            'reason' => 'required|in:spam,violence,hate_speech,misinformation,other'
        ]);

        $existingReport = Report::where('user_id', Auth::id())
            ->where('post_id', $post->id)
            ->first();

        if ($existingReport) {
            return response()->json([
                'success' => false,
                'message' => 'You have already reported this post.'
            ], 409);
        }

        Report::create([
            'user_id' => Auth::id(),
            'post_id' => $post->id,
            'reason'  => $request->input('reason'),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Thank you for your report. We will review it shortly.',
        ]);
    }


    public function explore(Request $request)
    {
        $query = Post::query();

        // Filters
        if ($request->filled('q')) {
            $search = $request->q;
            $query->where(function ($q) use ($search) {
                $q->where('content', 'like', "%{$search}%")
                ->orWhere('accident_type', 'like', "%{$search}%")
                ->orWhere('location', 'like', "%{$search}%");
            });
        }

        if ($request->filled('location')) {
            $query->where('location', $request->location);
        }

        if ($request->filled('accident_type')) {
            $query->where('accident_type', $request->accident_type);
        }

        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        $posts = $query->latest()->paginate(10);
        $uniqueLocations = Post::pluck('location')->filter()->unique()->values();
        $uniqueAccidents = Post::pluck('accident_type')->filter()->unique()->values();

        return view('userExplore', compact('posts', 'uniqueLocations', 'uniqueAccidents'));
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
     * Admin remove post (via AJAX or backend action).
     */
    public function adminRemove(Post $post)
    {
        if ($post->image && Storage::disk('public')->exists($post->image)) {
            Storage::disk('public')->delete($post->image);
        }

        $post->delete();

        return response()->json([
            'success' => true,
            'message' => 'Post removed successfully!',
            'post_id' => $post->id,
        ]);
    }
}
