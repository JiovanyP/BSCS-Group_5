<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'content'];

    /**
     * A post belongs to a user.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * A post has many likes.
     */
    public function likes()
    {
        return $this->hasMany(PostLike::class);
    }

    /**
     * A post has many top-level comments (replies handled inside Comment model).
     */
    public function comments()
    {
        return $this->hasMany(Comment::class)
                    ->whereNull('parent_id')   // only top-level comments
                    ->with(['user', 'replies']); // eager load user + replies
    }
}
