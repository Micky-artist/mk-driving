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

        // Get all user points ordered by the selected period with user relationship
        // Include users with zero points, sort by points desc, then by name asc
        $query = UserPoint::select('user_points.*')
            ->join('users', 'user_points.user_id', '=', 'users.id')
            ->where('users.is_active', true)
            ->orderBy($column, 'desc')
            ->orderByRaw("CONCAT(users.first_name, ' ', users.last_name) ASC")
            ->with(['user' => function($query) {
                $query->select('id', 'first_name', 'last_name', 'created_at');
            }])
            ->limit($limit);

        Log::info('SQL Query', ['sql' => $query->toSql(), 'bindings' => $query->getBindings()]);
        
        $userPoints = $query->get();
        
        Log::info('UserPoints query result', ['count' => $userPoints->count(), 'data' => $userPoints->toArray()]);

        // Transform the results with ranks and format user data
        $result = $userPoints->map(function ($userPoint, $index) use ($column) {
            Log::info('Processing user point', ['user_id' => $userPoint->user_id, 'points' => $userPoint->$column]);
            
            return [
                'user' => [
                    'id' => $userPoint->user->id,
                    'first_name' => $userPoint->user->first_name,
                    'last_name' => $userPoint->user->last_name,
                    'createdAt' => $userPoint->user->created_at->toIso8601String(),
                ],
                'points' => (int) $userPoint->$column,
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

        return UserPoint::where($column, '>', $userPoints->$column)->count() + 1;
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
