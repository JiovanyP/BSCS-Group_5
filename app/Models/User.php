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
        'location',
        'avatar',
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
    ];

    /**
     * Accessor: avatar_url
     *
     * Returns a valid public URL for the user's avatar regardless of how it's stored:
     * - remote absolute URLs (Google / Socialite) are returned untouched,
     * - storage-relative values (avatars/xxx.jpg or storage/avatars/xxx.jpg) become asset('storage/...')
     * - null/empty returns a default placeholder.
     *
     * Use in Blade: $user->avatar_url
     */
    public function getAvatarUrlAttribute()
    {
        $avatar = $this->avatar;

        // Default fallback
        if (!$avatar) {
            return 'https://bootdey.com/img/Content/avatar/avatar1.png';
        }

        // If it's already an absolute URL (Google OAuth, CDN, etc.), use it as-is
        if (Str::startsWith($avatar, ['http://', 'https://'])) {
            return $avatar;
        }

        // Normalize leading slashes
        $avatar = ltrim($avatar, '/');

        // If string already starts with 'storage/', it's directly usable by asset()
        if (Str::startsWith($avatar, 'storage/')) {
            return asset($avatar);
        }

        // Otherwise, assume it's a path saved under storage/app/public (e.g. avatars/...)
        return asset('storage/' . $avatar);
    }
}
