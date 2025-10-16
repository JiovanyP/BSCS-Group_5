<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use App\Models\Comment;
use App\Models\PostLike;

class Post extends Model
{
    use HasFactory;

    protected $table = 'posts';

    /**
     * Make image & media_type fillable so controller can save them.
     */
    protected $fillable = [
        'user_id',
        'content',
        'views',
        'image',
        'media_type',
    ];

    protected $casts = [
        'user_id' => 'integer',
        'views' => 'integer',
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

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    // Total number of comments (including replies)
    public function getTotalCommentsCountAttribute()
    {
        return Comment::where('post_id', $this->id)->count();
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
     * Use this in blade: $post->image_url
     */
    public function getImageUrlAttribute()
    {
        if (!$this->image) {
            return null; // no media attached
        }

        // If file exists in public storage, return storage path
        if (Storage::disk('public')->exists($this->image)) {
            return asset('storage/' . $this->image);
        }

        // Fallback: if we stored absolute/other path, try returning it directly
        return $this->image;
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

    // Automatically delete related comments, likes and media file when deleting a post
    protected static function booted()
    {
        static::deleting(function ($post) {
            // delete related comments and likes (keeps current behaviour)
            $post->comments()->delete();
            $post->likes()->delete();

            // delete stored media file if it exists in public disk
            if ($post->image && Storage::disk('public')->exists($post->image)) {
                Storage::disk('public')->delete($post->image);
            }
        });
    }
}
