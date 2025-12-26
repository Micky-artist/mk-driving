<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $fillable = [
        'type',
        'title',
        'message',
        'url',
        'is_read',
        'notified_at',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'notified_at' => 'datetime',
    ];

    /**
     * Scope to get unread notifications.
     */
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    /**
     * Scope to get notifications from the last 24 hours.
     */
    public function scopeRecent($query)
    {
        return $query->where('notified_at', '>=', now()->subHours(24));
    }
}
