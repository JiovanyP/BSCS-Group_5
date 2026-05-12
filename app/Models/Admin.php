<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Admin extends Authenticatable
{
    use Notifiable;

    /**
     * Explicitly specify the guard for this model
     * to avoid ambiguity in multi-auth configurations.
     */
    protected $guard = 'admin';

    /**
     * Mass assignable attributes.
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'settings', // ✅ allows mass assignment of settings JSON
    ];

    /**
     * Attributes that should be hidden for arrays and JSON.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Attribute casting.
     * Ensures JSON settings are automatically cast to arrays.
     */
    protected $casts = [
        'settings' => 'array',
        'email_verified_at' => 'datetime',
    ];
}
