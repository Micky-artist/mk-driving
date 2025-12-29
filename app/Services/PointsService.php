<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\UserPoint;
use App\Models\PointConfiguration;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PointsService
{
    public function awardPoints(int $userId, string $activityType, array $metadata = []): bool
    {
        if (!PointConfiguration::canAwardPoints($activityType, $userId)) {
            return false;
        }

        $points = PointConfiguration::getPointsForActivity($activityType);
        
        if ($points <= 0) {
            return false;
        }

        return DB::transaction(function () use ($userId, $activityType, $points, $metadata) {
            // Create activity log
            ActivityLog::create([
                'user_id' => $userId,
                'activity_type' => $activityType,
                'points_awarded' => $points,
                'metadata' => $metadata,
                'created_at' => now(),
            ]);

            // Update user points
            $userPoint = UserPoint::firstOrCreate(
                ['user_id' => $userId],
                [
                    'total_points' => 0,
                    'weekly_points' => 0,
                    'monthly_points' => 0,
                    'last_activity_at' => now(),
                ]
            );

            $userPoint->addPoints($points);

            return true;
        });
    }

    public function getLeaderboard(int $limit = 25, string $period = 'total'): array
    {
        Log::info('PointsService getLeaderboard called', ['limit' => $limit, 'period' => $period]);
        
        $column = match ($period) {
            'weekly' => 'weekly_points',
            'monthly' => 'monthly_points',
            default => 'total_points',
        };

        Log::info('Using column', ['column' => $column]);

        // Get all active users with their points (including zero points)
        // Left join to include users who may not have UserPoint records yet
        $query = DB::table('users')
            ->leftJoin('user_points', 'users.id', '=', 'user_points.user_id')
            ->where('users.is_active', true)
            ->where('users.role', '!=', User::ROLE_ADMIN)
            ->select(
                'users.id',
                'users.first_name',
                'users.last_name',
                'users.created_at',
                DB::raw('COALESCE(user_points.' . $column . ', 0) as points')
            )
            ->orderBy('points', 'desc')
            ->orderByRaw("CONCAT(users.first_name, ' ', users.last_name) ASC")
            ->limit($limit);

        Log::info('SQL Query', ['sql' => $query->toSql(), 'bindings' => $query->getBindings()]);
        
        $userPoints = $query->get();
        
        Log::info('UserPoints query result', ['count' => $userPoints->count(), 'data' => $userPoints->toArray()]);

        // Transform the results with ranks and format user data
        $result = $userPoints->map(function ($user, $index) {
            Log::info('Processing user', ['user_id' => $user->id, 'points' => $user->points]);
            
            return [
                'user' => [
                    'id' => $user->id,
                    'first_name' => $user->first_name,
                    'last_name' => $user->last_name,
                    'createdAt' => $user->created_at,
                ],
                'points' => (int) $user->points,
                'rank' => $index + 1,
            ];
        })->values()->toArray();

        Log::info('Final leaderboard result', ['count' => count($result), 'result' => $result]);
        
        return $result;
    }

    public function getUserRank(int $userId, string $period = 'total'): int
    {
        $column = match ($period) {
            'weekly' => 'weekly_points',
            'monthly' => 'monthly_points',
            default => 'total_points',
        };

        $userPoints = UserPoint::where('user_id', $userId)->first();

        if (!$userPoints || $userPoints->$column <= 0) {
            return 0;
        }

        return UserPoint::join('users', 'user_points.user_id', '=', 'users.id')
            ->where('users.role', '!=', User::ROLE_ADMIN)
            ->where($column, '>', $userPoints->$column)
            ->count() + 1;
    }

    public function getUserPoints(int $userId): array
    {
        $userPoint = UserPoint::where('user_id', $userId)->first();

        if (!$userPoint) {
            return [
                'total' => 0,
                'weekly' => 0,
                'monthly' => 0,
                'rank' => 0,
            ];
        }

        return [
            'total' => $userPoint->total_points,
            'weekly' => $userPoint->weekly_points,
            'monthly' => $userPoint->monthly_points,
            'rank' => $this->getUserRank($userId),
        ];
    }

    public function resetWeeklyPoints(): void
    {
        UserPoint::query()->update(['weekly_points' => 0]);
    }

    public function resetMonthlyPoints(): void
    {
        UserPoint::query()->update(['monthly_points' => 0]);
    }

    public function getRecentActivities(int $userId, int $limit = 10): array
    {
        return ActivityLog::where('user_id', $userId)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get()
            ->map(function ($log) {
                return [
                    'activity_type' => $log->activity_type,
                    'points_awarded' => $log->points_awarded,
                    'created_at' => $log->created_at,
                    'metadata' => $log->metadata,
                ];
            })
            ->toArray();
    }
}
