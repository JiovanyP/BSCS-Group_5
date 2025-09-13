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

    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Replies to this comment
    public function replies()
    {
        return $this->hasMany(Comment::class, 'parent_id')->with('user');
    }

    // Parent comment (if this is a reply)
    public function parent()
    {
        return $this->belongsTo(Comment::class, 'parent_id');
    }

    // ✅ Helper: is this a reply or top-level comment?
    public function isReply(): bool
    {
        return !is_null($this->parent_id);
    }
}
