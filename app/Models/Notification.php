<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'actor_id',
        'post_id',
        'comment_id',
        'type',
        'is_read',
        'notification_type',
        'accident_type',
        'distance_km',
        'data', // ✅ added for JSON admin notifications
    ];

    protected $casts = [
        'is_read'       => 'boolean',
        'user_id'       => 'integer',
        'actor_id'      => 'integer',
        'post_id'       => 'integer',
        'comment_id'    => 'integer',
        'distance_km'   => 'decimal:2',
        'data'          => 'array', // ✅ ensures proper JSON encoding/decoding
    ];

    /**
     * Relationships
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function actor()
    {
        return $this->belongsTo(User::class, 'actor_id');
    }

    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    public function comment()
    {
        return $this->belongsTo(Comment::class);
    }

    /**
     * Scopes
     */
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    public function scopeRead($query)
    {
        return $query->where('is_read', true);
    }

    public function scopePriority($query)
    {
        return $query->where('notification_type', 'priority');
    }

    public function scopeGeneral($query)
    {
        return $query->where('notification_type', 'general');
    }

    public function scopeSocial($query)
    {
        return $query->where('notification_type', 'social');
    }

    public function scopeByAccidentType($query, $type)
    {
        if ($type && $type !== 'all') {
            return $query->where('accident_type', $type);
        }
        return $query;
    }

    /**
     * Helper Methods
     */
    public function markAsRead()
    {
        $this->update(['is_read' => true]);
    }

    public function getNotificationMessage()
    {
        $actorName = $this->actor ? $this->actor->name : 'Someone';

        switch ($this->type) {
            case 'upvote':
                return "{$actorName} upvoted your post";
            case 'downvote':
                return "{$actorName} downvoted your post";
            case 'comment':
                return "{$actorName} commented on your post";
            case 'reply':
                return "{$actorName} replied to your comment";
            case 'location_alert':
                if ($this->post && $this->post->user) {
                    return "<strong>{$this->post->user->name}</strong> reported a {$this->accident_type} incident";
                }
                return "New {$this->accident_type} report in your area";

            // ✅ added support for admin system notifications
            case 'account_suspended':
                return "Your account has been suspended by an administrator.";
            case 'account_banned':
                return "Your account has been permanently banned by an administrator.";
            case 'account_restored':
                return "Your account access has been restored by an administrator.";

            default:
                return "New notification";
        }
    }

    public function getIcon()
    {
        switch ($this->type) {
            case 'upvote':
                return 'fas fa-thumbs-up';
            case 'downvote':
                return 'fas fa-thumbs-down';
            case 'comment':
            case 'reply':
                return 'fas fa-comment';
            case 'account_suspended':
            case 'account_banned':
                return 'fas fa-user-slash';
            case 'account_restored':
                return 'fas fa-user-check';
            default:
                if ($this->notification_type === 'priority') {
                    return 'fas fa-exclamation-triangle';
                } elseif ($this->notification_type === 'general') {
                    return 'fas fa-map-marker-alt';
                }
                return 'fas fa-bell';
        }
    }

    public function getIconColor()
    {
        switch ($this->type) {
            case 'upvote':
                return '#4CAF50';
            case 'downvote':
                return '#FF0B55';
            case 'comment':
            case 'reply':
                return '#2196F3';
            case 'account_suspended':
            case 'account_banned':
                return '#E53935';
            case 'account_restored':
                return '#43A047';
            default:
                if ($this->notification_type === 'priority') {
                    return '#FF5722';
                } elseif ($this->notification_type === 'general') {
                    return '#9C27B0';
                }
                return '#999';
        }
    }

    /**
     * Distance / Demo helpers
     */
    public static function calculateDistance($userLocation, $postLocation)
    {
        if ($userLocation === $postLocation) {
            return 0;
        }
        return rand(1, 50);
    }

    public static function createLocationNotification($user, $post)
    {
        $distance = self::calculateDistance($user->location, $post->location);
        $notificationType = $distance <= 10 ? 'priority' : 'general';

        return self::create([
            'user_id'          => $user->id,
            'actor_id'         => $post->user_id,
            'post_id'          => $post->id,
            'type'             => 'location_alert',
            'notification_type'=> $notificationType,
            'accident_type'    => $post->accident_type,
            'distance_km'      => $distance,
            'is_read'          => false,
        ]);
    }
}
