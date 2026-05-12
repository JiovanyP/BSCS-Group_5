<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use App\Models\Comment;
use App\Models\PostLike;
use App\Models\Report;

class Post extends Model
{
    use HasFactory;

    protected $table = 'posts';

    /**
     * Make image & media_type fillable so controller can save them.
     * UPDATED: Changed 'image' to 'image_url', removed 'admin_name'
     */
    protected $fillable = [
        'user_id',
        'is_admin_post',
        'content',
        'location',
        'accident_type',
        'views',
        'image_url',      // Changed from 'image'
        'media_type',
    ];

    protected $casts = [
        'user_id' => 'integer',
        'views' => 'integer',
        'is_admin_post' => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    // Each post belongs to one user
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // ADDED: Get the admin that created the post (if admin post)
    public function admin()
    {
        return $this->belongsTo(\App\Models\Admin::class, 'user_id');
    }

    // Each post can have many likes (upvotes/downvotes)
    public function likes()
    {
        return $this->hasMany(PostLike::class, 'post_id');
    }

    // Only upvotes
    public function upvotes()
    {
        return $this->likes()->where('vote_type', 'up');
    }

    // Only downvotes
    public function downvotes()
    {
        return $this->likes()->where('vote_type', 'down');
    }

    // A post can have many top-level comments with nested replies
    public function comments()
    {
        return $this->hasMany(Comment::class)
            ->whereNull('parent_id')
            ->with(['user', 'replies.user']); // eager load nested replies
    }

    // A post can have many reports
    public function reports()
    {
        return $this->hasMany(Report::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    // Total number of comments (including replies)
    public function getTotalCommentsCountAttribute()
    {
        return $this->comments()->count() + $this->comments()->with('replies')->get()->sum(function ($comment) {
            return $comment->replies->count();
        });
    }

    // Post score = upvotes - downvotes
    public function getScoreAttribute()
    {
        return $this->upvotes()->count() - $this->downvotes()->count();
    }

    // Current user's vote type ('up', 'down', or 'none')
    public function getUserVoteTypeAttribute()
    {
        if (!Auth::check()) return 'none';
        return $this->likes()->where('user_id', Auth::id())->value('vote_type') ?? 'none';
    }

    // Number of views
    public function getViewsCountAttribute()
    {
        return $this->views ?? 0;
    }

    /**
     * Public URL for the post media (image/video/gif).
     * UPDATED: Now properly handles image_url with multiple formats
     */
    public function getImageUrlAttribute($value)
    {
        // If no value, return null
        if (empty($value)) {
            return null;
        }

        // If it's already a full URL (starts with http), return as is
        if (str_starts_with($value, 'http://') || str_starts_with($value, 'https://')) {
            return $value;
        }

        // If it starts with '/storage/', remove the leading slash and return asset URL
        if (str_starts_with($value, '/storage/')) {
            return asset(ltrim($value, '/'));
        }

        // If it already starts with 'storage/', return asset URL
        if (str_starts_with($value, 'storage/')) {
            return asset($value);
        }

        // Otherwise, prepend 'storage/' and return asset URL
        return asset('storage/' . $value);
    }

    // ADDED: Get the display name for the post author
    public function getAuthorNameAttribute()
    {
        if ($this->is_admin_post) {
            return 'Admin'; // Always return "Admin" for admin posts
        }
        
        return $this->user->name ?? 'Unknown User';
    }

    /*
    |--------------------------------------------------------------------------
    | Utility Methods
    |--------------------------------------------------------------------------
    */

    // Check if current user owns this post
    public function isOwnedByCurrentUser()
    {
        return Auth::check() && $this->user_id === Auth::id();
    }

    // Helper: get vote type ('up', 'down', 'none') for a specific user
    public function userVote($userId = null)
    {
        $userId = $userId ?? Auth::id();
        if (!$userId) return 'none';
        return $this->likes()->where('user_id', $userId)->value('vote_type') ?? 'none';
    }

    // ADDED: Check if post is by admin
    public function isAdminPost()
    {
        return $this->is_admin_post;
    }

    /*
    |--------------------------------------------------------------------------
    | Model Events (Cascade Deletes)
    |--------------------------------------------------------------------------
    */

    protected static function booted()
    {
        static::deleting(function ($post) {
            // Delete all comments (including replies) related to this post.
            Comment::where('post_id', $post->id)->delete();

            // Delete likes related to this post
            $post->likes()->delete();

            // Delete reports related to this post (prevent orphaned reports)
            $post->reports()->delete();

            // Delete stored media file if it exists
            // Check both image_url and legacy image column
            $imagePath = null;
            
            // Get the raw attribute value (not the accessor)
            if (!empty($post->attributes['image_url'])) {
                $imagePath = $post->attributes['image_url'];
                
                // If it starts with http, skip deletion (external URL)
                if (str_starts_with($imagePath, 'http://') || str_starts_with($imagePath, 'https://')) {
                    return;
                }
                
                // Remove 'storage/' prefix if present
                $imagePath = str_replace('storage/', '', $imagePath);
            } elseif (!empty($post->attributes['image'])) {
                // Legacy column support
                $imagePath = $post->attributes['image'];
            }

            if ($imagePath && Storage::disk('public')->exists($imagePath)) {
                try {
                    Storage::disk('public')->delete($imagePath);
                } catch (\Throwable $e) {
                    // Prevent fatal if file delete fails
                    \Log::warning("Failed to delete post image: {$imagePath}", ['error' => $e->getMessage()]);
                }
            }
        });
    }
}