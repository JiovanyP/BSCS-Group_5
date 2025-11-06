<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use App\Models\Comment;
use App\Models\PostLike;
use App\Models\Report;

class Post extends Model
{
    use HasFactory;

    protected $table = 'posts';

    protected $fillable = [
        'user_id',
        'content',
        'location',
        'accident_type',
        'views',
        'image',
        'media_type',
    ];

    protected $casts = [
        'user_id' => 'integer',
        'views' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function likes()
    {
        return $this->hasMany(PostLike::class, 'post_id');
    }

    public function upvotes()
    {
        return $this->likes()->where('vote_type', 'up');
    }

    public function downvotes()
    {
        return $this->likes()->where('vote_type', 'down');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class)
            ->whereNull('parent_id')
            ->with(['user', 'replies.user']);
    }

    public function reports()
    {
        return $this->hasMany(Report::class);
    }

    public function getTotalCommentsCountAttribute()
    {
        return Comment::where('post_id', $this->id)->count();
    }

    public function getScoreAttribute()
    {
        return $this->upvotes()->count() - $this->downvotes()->count();
    }

    public function getUserVoteTypeAttribute()
    {
        if (!Auth::check()) return 'none';
        return $this->likes()->where('user_id', Auth::id())->value('vote_type') ?? 'none';
    }

    public function getViewsCountAttribute()
    {
        return $this->views ?? 0;
    }

    public function getImageUrlAttribute()
    {
        if (!$this->image) {
            return null;
        }

        if (Storage::disk('public')->exists($this->image)) {
            return asset('storage/' . $this->image);
        }

        return $this->image;
    }

    public function isOwnedByCurrentUser()
    {
        return Auth::check() && $this->user_id === Auth::id();
    }

    public function userVote($userId = null)
    {
        $userId = $userId ?? Auth::id();
        if (!$userId) return 'none';
        return $this->likes()->where('user_id', $userId)->value('vote_type') ?? 'none';
    }

    protected static function booted()
    {
        static::deleting(function ($post) {
            // delete related comments and likes
            $post->comments()->delete();
            $post->likes()->delete();

            // delete stored media file if it exists in public disk
            if ($post->image && Storage::disk('public')->exists($post->image)) {
                Storage::disk('public')->delete($post->image);
            }
        });
    }
}
