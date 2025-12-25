<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeaderboardEntry extends Model
{
    protected $fillable = [
        'leaderboard_id',
        'user_id',
        'rank',
        'score',
        'metadata'
    ];

    protected $casts = [
        'metadata' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Relationships
    public function leaderboard(): BelongsTo
    {
        return $this->belongsTo(Leaderboard::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get user's rank in specific leaderboard
     */
    public static function getUserRank(int $userId, string $leaderboardType): ?int
    {
        $leaderboard = Leaderboard::where('type', $leaderboardType)
            ->where('is_active', true)
            ->first();

        if (!$leaderboard) {
            return null;
        }

        return $leaderboard->getUserPosition($userId);
    }

    /**
     * Get user's leaderboard entries
     */
    public static function getUserEntries(int $userId, int $limit = 5): array
    {
        $entries = self::with('leaderboard')
            ->where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();

        return $entries->map(function ($entry) {
            return [
                'leaderboard_type' => $entry->leaderboard->type,
                'rank' => $entry->rank,
                'score' => $entry->score,
                'period' => $entry->leaderboard->period_start . ' - ' . $entry->leaderboard->period_end,
                'metadata' => $entry->metadata
            ];
        })->toArray();
    }
}
