<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    protected $table = 'comments';

    protected $fillable = [
        'post_id',
        'user_id',
        'content',
        'parent_id',
    ];

    protected $casts = [
        'post_id' => 'integer',
        'user_id' => 'integer',
        'parent_id' => 'integer',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    // Comment belongs to a user
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Comment belongs to a post
    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    // Replies (children) - eager-load recursively
    public function replies()
    {
        return $this->hasMany(Comment::class, 'parent_id')
                    ->with(['user', 'replies']);
    }

    // Parent comment
    public function parent()
    {
        return $this->belongsTo(Comment::class, 'parent_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes & Helpers
    |--------------------------------------------------------------------------
    */

    // Only top-level comments
    public function scopeTopLevel($query)
    {
        return $query->whereNull('parent_id');
    }

    // Check if comment is a reply
    public function isReply(): bool
    {
        return !is_null($this->parent_id);
    }

    // Count all nested replies
    public function allRepliesCount(): int
    {
        return $this->replies()->withCount('replies')->get()->sum(function ($r) {
            return 1 + $r->replies_count;
        });
    }
}
