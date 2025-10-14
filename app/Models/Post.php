<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Post extends Model
{
    use HasFactory;

    protected $table = 'posts';

    protected $fillable = [
        'user_id',
        'content',
        'views',
    ];

    protected $casts = [
        'user_id' => 'integer',
        'views' => 'integer',
    ];

    /*
    |----------------------------------------------------------------------
    | Relationships
    |----------------------------------------------------------------------
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
    |----------------------------------------------------------------------
    | Accessors
    |----------------------------------------------------------------------
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

    /*
    |----------------------------------------------------------------------
    | Utility Methods
    |----------------------------------------------------------------------
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

    // Delete related comments and likes when deleting a post
    protected static function booted()
    {
        static::deleting(function ($post) {
            $post->comments()->delete();
            $post->likes()->delete();
        });
    }
}
