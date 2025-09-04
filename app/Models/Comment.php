<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    protected $fillable = ['post_id', 'user_id', 'content', 'parent_id'];

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
     * A comment can have many replies (children).
     */
    public function replies()
    {
        return $this->hasMany(Comment::class, 'parent_id')
                    ->with('user'); // eager load user for replies
    }

    /**
     * A comment may have a parent (for nested replies).
     */
    public function parent()
    {
        return $this->belongsTo(Comment::class, 'parent_id');
    }

    /**
     * Scope: Only top-level comments (not replies).
     */
    public function scopeTopLevel($query)
    {
        return $query->whereNull('parent_id');
    }
}
