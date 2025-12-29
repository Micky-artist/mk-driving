<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Leaderboard extends Model
{
    protected $fillable = [
        'type',
        'period_start',
        'period_end',
        'leaderboard_data',
        'is_active'
    ];

    protected $casts = [
        'leaderboard_data' => 'array',
        'is_active' => 'boolean',
        'period_start' => 'datetime',
        'period_end' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Relationships
    public function entries(): HasMany
    {
        return $this->hasMany(LeaderboardEntry::class);
    }

    // Constants for leaderboard types
    public const TYPE_WEEKLY = 'weekly';
    public const TYPE_MONTHLY = 'monthly';
    public const TYPE_ALL_TIME = 'all_time';
    public const TYPE_QUIZ_MASTERS = 'quiz_masters';
    public const TYPE_FORUM_HELPERS = 'forum_helpers';
    public const TYPE_STREAK_CHAMPIONS = 'streak_champions';

    /**
     * Get or create weekly leaderboard
     */
    public static function getWeekly(): self
    {
        return self::firstOrCreate([
            'type' => self::TYPE_WEEKLY,
            'period_start' => now()->startOfWeek()->format('Y-m-d'),
            'period_end' => now()->endOfWeek()->format('Y-m-d'),
        ], [
            'leaderboard_data' => [],
            'is_active' => true
        ]);
    }

    /**
     * Get or create monthly leaderboard
     */
    public static function getMonthly(): self
    {
        return self::firstOrCreate([
            'type' => self::TYPE_MONTHLY,
            'period_start' => now()->startOfMonth()->format('Y-m-d'),
            'period_end' => now()->endOfMonth()->format('Y-m-d'),
        ], [
            'leaderboard_data' => [],
            'is_active' => true
        ]);
    }

    /**
     * Get all-time leaderboard
     */
    public static function getAllTime(): self
    {
        return self::firstOrCreate([
            'type' => self::TYPE_ALL_TIME,
            'period_start' => '2020-01-01', // App start date
            'period_end' => null,
        ], [
            'leaderboard_data' => [],
            'is_active' => true
        ]);
    }

    /**
     * Update leaderboard with current rankings
     */
    public function updateRankings(): void
    {
        $users = User::withCount(['quizAttempts', 'forumAnswers'])
            ->where('role', '!=', User::ROLE_ADMIN)
            ->orderBy('points', 'desc')
            ->limit(100) // Top 100 users
            ->get();

        $rankings = [];
        $rank = 1;

        foreach ($users as $user) {
            $rankings[] = [
                'rank' => $rank++,
                'user_id' => $user->id,
                'name' => $user->first_name . ' ' . $user->last_name,
                'points' => $user->points,
                'streak_days' => $user->streak_days,
                'quiz_attempts' => $user->quiz_attempts_count,
                'forum_answers' => $user->forum_answers_count,
                'avatar' => $user->profile_image,
                'badges' => $user->achievement_badges ?? []
            ];

            // Update user's leaderboard position directly in the database
            $user->update(['leaderboard_position' => $rank - 1]);
        }

        // For users not in top 100, set their position to null or a high number
        User::whereNotIn('id', $users->pluck('id'))
            ->update(['leaderboard_position' => null]);

        $this->update(['leaderboard_data' => $rankings]);
    }

    /**
     * Get top users for this leaderboard
     */
    public function getTopUsers(int $limit = 10): array
    {
        $data = $this->leaderboard_data ?? [];
        return array_slice($data, 0, $limit);
    }

    /**
     * Get user's position in leaderboard
     */
    public function getUserPosition(int $userId): ?int
    {
        $data = $this->leaderboard_data ?? [];
        
        foreach ($data as $entry) {
            if ($entry['user_id'] === $userId) {
                return $entry['rank'];
            }
        }
        
        return null;
    }
}
