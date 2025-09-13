<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    protected $table = 'posts';

    protected $fillable = ['user_id', 'content'];

    protected $casts = [
        'user_id' => 'integer',
    ];

    /**
     * Post belongs to a user.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * ✅ Post has many votes (PostLike model).
     */
    public function likes()
    {
        return $this->hasMany(PostLike::class);
    }

    public function upvotes()
    {
        return $this->likes()->where('vote_type', 'up');
    }

    public function downvotes()
    {
        return $this->likes()->where('vote_type', 'down');
    }

    /**
     * ✅ Post has many top-level comments.
     */
    public function comments()
    {
        return $this->hasMany(Comment::class)
                    ->whereNull('parent_id')
                    ->with(['user', 'replies.user']); // ✅ load replies' user too
    }

    /**
     * ✅ Post score (upvotes - downvotes).
     */
    public function getScoreAttribute()
    {
        return $this->upvotes()->count() - $this->downvotes()->count();
    }
}
