<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'location',
        'password',
    ];
    
    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * ✅ Relationships
     */
    // A user can have many posts
    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    // A user can have many comments
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    // A user can have many likes/votes
    public function likes()
    {
        return $this->hasMany(PostLike::class);
    }
}
