<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    protected $table = 'comments';

    protected $fillable = ['post_id', 'user_id', 'content', 'parent_id'];

    protected $casts = [
        'parent_id' => 'integer',
    ];

    /**
     * A comment belongs to a post.
     */
    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    /**
     * A comment belongs to a user.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Replies (children).
     */
    public function replies()
    {
        return $this->hasMany(Comment::class, 'parent_id')
                    ->with(['user', 'replies']); 
        // ✅ Recursive eager load for nested replies
    }

    /**
     * Parent comment (for replies).
     */
    public function parent()
    {
        return $this->belongsTo(Comment::class, 'parent_id');
    }

    /**
     * Scope: Only top-level comments.
     */
    public function scopeTopLevel($query)
    {
        return $query->whereNull('parent_id');
    }

    /**
     * ✅ Helper: Determine if this comment is a reply or top-level.
     */
    public function isReply(): bool
    {
        return !is_null($this->parent_id);
    }
}
