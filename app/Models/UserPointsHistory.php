<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserPointsHistory extends Model
{
    protected $fillable = [
        'user_id',
        'points_change',
        'reason',
        'metadata'
    ];

    protected $casts = [
        'metadata' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Constants for point reasons
    public const REASON_QUIZ_COMPLETED = 'quiz_completed';
    public const REASON_QUIZ_PERFECT_SCORE = 'quiz_perfect_score';
    public const REASON_FORUM_ANSWER = 'forum_answer';
    public const REASON_HELPFUL_ANSWER = 'helpful_answer';
    public const REASON_DAILY_LOGIN = 'daily_login';
    public const REASON_STREAK_BONUS = 'streak_bonus';
    public const REASON_CONTRIBUTION_BONUS = 'contribution_bonus';
    public const REASON_ACHIEVEMENT_UNLOCK = 'achievement_unlock';
    public const REASON_NEWS_ENGAGEMENT = 'news_engagement';

    /**
     * Award points to user
     */
    public static function awardPoints(User $user, int $points, string $reason, array $metadata = []): self
    {
        // Update user's total points
        $user->increment('points', $points);
        
        // Update rank based on new points
        $user->updateRank();

        // Record the points history
        return self::create([
            'user_id' => $user->id,
            'points_change' => $points,
            'reason' => $reason,
            'metadata' => $metadata
        ]);
    }

    /**
     * Get points earned in the last X days
     */
    public static function getRecentPoints(User $user, int $days = 7): int
    {
        return self::where('user_id', $user->id)
            ->where('created_at', '>=', now()->subDays($days))
            ->sum('points_change');
    }

    /**
     * Get points breakdown by reason
     */
    public static function getPointsBreakdown(User $user): array
    {
        return self::where('user_id', $user->id)
            ->selectRaw('reason, SUM(points_change) as total_points, COUNT(*) as count')
            ->groupBy('reason')
            ->orderBy('total_points', 'desc')
            ->get()
            ->keyBy('reason')
            ->toArray();
    }
}
