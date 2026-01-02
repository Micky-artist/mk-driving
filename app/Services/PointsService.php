<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\UserPoint;
use App\Models\PointConfiguration;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class PointsService
{
    private ?RobotActivityService $robotActivityService = null;

    private function getRobotActivityService(): RobotActivityService
    {
        if ($this->robotActivityService === null) {
            $this->robotActivityService = new RobotActivityService($this);
        }
        return $this->robotActivityService;
    }
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
            $userPoint = UserPoint::updateOrCreate(
                ['user_id' => $userId],
                [
                    'total_points' => DB::raw("COALESCE(total_points, 0) + {$points}"),
                    'weekly_points' => DB::raw("COALESCE(weekly_points, 0) + {$points}"),
                    'monthly_points' => DB::raw("COALESCE(monthly_points, 0) + {$points}"),
                    'last_activity_at' => now(),
                ]
            );

            // Trigger robot activity if this is a real user activity
            $user = User::find($userId);
            if ($user && !$user->is_robot) {
                $this->triggerRobotActivity($userId, $activityType, $points);
            }

            return true;
        });
    }

    private function triggerRobotActivity(int $userId, string $activityType, int $points): void
    {
        // Only trigger robot activity if probability allows
        if (random_int(1, 100) <= 40) { // 40% chance
            $this->getRobotActivityService()->markRealUserActivity();
            $this->getRobotActivityService()->handleUserActivity('user_quiz_completed');
        }
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
                'users.is_robot',
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
            
            // Get recent activities for this user
            $recentActivities = $this->getRecentActivities($user->id, 3);
            
            return [
                'user' => [
                    'id' => $user->id,
                    'first_name' => $user->first_name,
                    'last_name' => $user->last_name,
                    'createdAt' => $user->created_at,
                    'is_robot' => (bool) $user->is_robot,
                ],
                'points' => (int) $user->points,
                'rank' => $index + 1,
                'recent_activities' => $recentActivities,
                'last_activity' => $recentActivities[0]['created_at'] ?? null,
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

    public function getLeaderboardWithNotifications(int $limit = 25, string $period = 'total'): array
    {
        $leaderboard = $this->getLeaderboard($limit, $period);
        
        // Add robot notifications if available
        $robotNotification = Cache::get('latest_robot_notification');
        
        return [
            'leaderboard' => $leaderboard,
            'robot_notification' => $robotNotification,
            'has_robot_activity' => !empty($robotNotification),
        ];
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
                    'activity_description' => $this->getActivityDescription($log->activity_type, $log->metadata),
                    'points_awarded' => $log->points_awarded,
                    'created_at' => $log->created_at,
                    'time_ago' => $log->created_at->diffForHumans(),
                    'metadata' => $log->metadata,
                ];
            })
            ->toArray();
    }

    private function getActivityDescription(string $activityType, array $metadata = []): string
    {
        $isRobotActivity = $metadata['is_robot_activity'] ?? false;
        
        $descriptions = [
            'quiz_completed' => $isRobotActivity ? 'Completed a practice test' : 'Completed a quiz',
            'daily_login' => 'Logged in today',
            'profile_updated' => 'Updated profile',
            'forum_participation' => 'Participated in forum',
            'achievement_unlocked' => 'Unlocked an achievement',
            'user_signup' => 'Joined the platform',
            'subscription_purchased' => 'Upgraded to premium',
        ];

        return $descriptions[$activityType] ?? 'Earned points';
    }
}
