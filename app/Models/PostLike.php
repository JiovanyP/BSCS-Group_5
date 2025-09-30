<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PostLike extends Model
{
    use HasFactory;

    protected $table = 'post_likes'; // ✅ make sure your DB table is named correctly

    protected $fillable = ['user_id', 'post_id', 'vote_type'];

    /**
     * A like belongs to a post.
     */
    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    /**
     * A like belongs to a user.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}