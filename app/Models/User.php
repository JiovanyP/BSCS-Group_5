<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'address',
        'bio',
        'avatar',
        'location',
        'status',
        'suspended_at',
        'banned_at',
        'last_login_at',
    ];

    /**
     * Hidden attributes for arrays.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Casts.
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login_at' => 'datetime',
        'suspended_at' => 'datetime',
        'banned_at' => 'datetime',
    ];

    /**
     * Accessor: avatar_url
     */
    public function getAvatarUrlAttribute()
    {
        $avatar = $this->avatar;

        // Default fallback
        if (!$avatar) {
            return asset('images/default-avatar.png');
        }

        // If it's already an absolute URL (Google OAuth, CDN, etc.)
        if (Str::startsWith($avatar, ['http://', 'https://'])) {
            return $avatar;
        }

        // Normalize leading slashes
        $avatar = ltrim($avatar, '/');

        // If string already starts with 'storage/'
        if (Str::startsWith($avatar, 'storage/')) {
            return asset($avatar);
        }

        // Otherwise, assume it's under storage/app/public
        return asset('storage/' . $avatar);
    }

    /**
     * Relationships
     */
    public function posts()
    {
        return $this->hasMany(Post::class, 'user_id', 'id');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class, 'user_id', 'id');
    }

    public function votes()
    {
        return $this->hasMany(Vote::class);
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    /**
     * Helper methods
     */
    public function isSuspended()
    {
        return $this->status === 'suspended' && $this->suspended_at !== null;
    }

    public function isBanned()
    {
        return $this->status === 'banned' && $this->banned_at !== null;
    }

    public function isActive()
    {
        return $this->status === 'active';
    }
}