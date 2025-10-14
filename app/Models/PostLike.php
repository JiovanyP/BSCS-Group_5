<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PostLike extends Model
{
    use HasFactory;

    protected $table = 'post_likes'; // ✅ make sure this matches your table name
    protected $fillable = ['post_id', 'user_id', 'vote_type'];

    public function post()
    {
        return $this->belongsTo(\App\Models\Post::class, 'post_id');
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }
}
