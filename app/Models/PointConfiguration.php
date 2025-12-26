<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PointConfiguration extends Model
{
    protected $fillable = [
        'activity_type',
        'points',
        'is_active',
        'conditions',
    ];

    protected $casts = [
        'conditions' => 'array',
        'is_active' => 'boolean',
    ];

    public static function getPointsForActivity(string $activityType): int
    {
        $config = static::where('activity_type', $activityType)
            ->where('is_active', true)
            ->first();

        return $config?->points ?? 0;
    }

    public static function getConditionsForActivity(string $activityType): array
    {
        $config = static::where('activity_type', $activityType)
            ->where('is_active', true)
            ->first();

        return $config?->conditions ?? [];
    }

    public static function canAwardPoints(string $activityType, int $userId): bool
    {
        $conditions = static::getConditionsForActivity($activityType);
        
        // Check cooldown if specified
        if (isset($conditions['cooldown_hours'])) {
            $recentActivity = ActivityLog::forActivity($activityType)
                ->where('user_id', $userId)
                ->recent($conditions['cooldown_hours'])
                ->exists();

            if ($recentActivity) {
                return false;
            }
        }

        return true;
    }
}
