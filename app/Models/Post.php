<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    protected $table = 'posts';

    protected $fillable = [
        'user_id',
        'content',
    ];

    protected $casts = [
        'user_id' => 'integer',
    ];

    /**
     * A post belongs to a user.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * A post can have many votes (likes).
     */
    public function likes()
    {
        return $this->hasMany(PostLike::class);
    }

    /**
     * Get only upvotes.
     */
    public function upvotes()
    {
        return $this->likes()->where('vote_type', 'up');
    }

    /**
     * Get only downvotes.
     */
    public function downvotes()
    {
        return $this->likes()->where('vote_type', 'down');
    }

    /**
     * A post can have many comments (top-level only).
     */
    public function comments()
    {
        return $this->hasMany(Comment::class)
                    ->whereNull('parent_id')
                    ->with(['user', 'replies.user']); // Load user + nested replies
    }

    /**
     * Get the total number of comments (including replies).
     */
    public function getTotalCommentsCountAttribute()
    {
        // Include all comments, even nested ones
        return Comment::where('post_id', $this->id)->count();
    }

    /**
     * Get the post score (upvotes - downvotes).
     */
    public function getScoreAttribute()
    {
        return $this->upvotes()->count() - $this->downvotes()->count();
    }

    /**
     * Check if a specific user has voted on this post.
     */
    public function userVote($userId)
    {
        return $this->likes()->where('user_id', $userId)->value('vote_type');
    }
}
