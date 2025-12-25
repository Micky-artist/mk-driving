<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserActivity extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'data',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'data' => 'array',
    ];

    /**
     * Get the user that performed the activity.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Activity types constants
     */
    const TYPE_LOGIN = 'login';
    const TYPE_QUIZ_ATTEMPT = 'quiz_attempt';
    const TYPE_FORUM_POST = 'forum_post';
    const TYPE_FORUM_ANSWER = 'forum_answer';
    const TYPE_SUBSCRIPTION = 'subscription';
    const TYPE_REGISTRATION = 'registration';

    /**
     * Log user activity
     */
    public static function log(int $userId, string $type, array $data = [], ?string $ipAddress = null, ?string $userAgent = null): self
    {
        return self::create([
            'user_id' => $userId,
            'type' => $type,
            'data' => $data,
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent,
        ]);
    }
}
