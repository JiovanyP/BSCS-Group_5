<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Report extends Model
{
    // Allow mass assignment for these attributes
    protected $fillable = [
        'user_id',
        'post_id',
        'reason',
    ];

    /**
     * The user who filed the report.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    /**
     * The post that was reported.
     */
    public function post(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Post::class);
    }
}