<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserPoint;
use App\Jobs\TriggerRobotActivity;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class RobotActivityService
{
    private PointsService $pointsService;

    public function __construct(PointsService $pointsService)
    {
        $this->pointsService = $pointsService;
    }

    public function handleUserActivity(string $activity, array $data = []): void
    {
        if (!$this->shouldTriggerRobotActivity($activity)) {
            return;
        }

        $robot = $this->selectRandomRobot();
        if (!$robot) {
            return;
        }

        $delay = $this->calculateRealisticDelay($activity);
        
        // Use short in-memory delay (max 7 seconds) instead of queue
        $actualDelay = min($delay, 7);
        sleep($actualDelay);
        
        // Award points to robot based on real user activity
        $this->awardPointsToRobot($robot, $activity);

        // Mark that real user activity occurred (prevents excessive robot activity)
        $this->markRealUserActivity();

        Log::info("Robot activity triggered immediately", [
            'robot_id' => $robot->id,
            'robot_name' => $robot->first_name,
            'activity' => $activity,
            'delay_seconds' => $actualDelay,
        ]);
    }

    private function shouldTriggerRobotActivity(string $activity): bool
    {
        $probabilities = [
            'user_signup' => 60,
            'user_quiz_completed' => 40,
            'user_subscription_purchased' => 70,
            'user_login' => 25,
            'user_forum_post' => 30,
        ];

        $probability = $probabilities[$activity] ?? 20;
        return (random_int(1, 100) <= $probability);
    }

    private function selectRandomRobot(): ?User
    {
        return User::where('is_robot', true)
            ->inRandomOrder()
            ->first();
    }

    private function calculateRealisticDelay(string $activity): int
    {
        $baseDelays = [
            'user_signup' => [300, 900],        // 5-15 minutes after signup
            'user_quiz_completed' => [120, 600], // 2-10 minutes after quiz
            'user_subscription_purchased' => [180, 720], // 3-12 minutes after purchase
            'user_login' => [60, 300],           // 1-5 minutes after login
            'user_forum_post' => [240, 480],    // 4-8 minutes after forum post
        ];

        $delayRange = $baseDelays[$activity] ?? [60, 300];
        
        // Add some randomness to make it more natural
        $baseDelay = random_int($delayRange[0], $delayRange[1]);
        $randomVariation = random_int(-30, 60); // ±30-60 seconds variation
        
        return max(0, $baseDelay + $randomVariation);
    }

    public function awardPointsToRobot(User $robot, string $triggerActivity): void
    {
        $robotActivity = $this->selectRobotActivity();
        
        $success = $this->pointsService->awardPoints($robot->id, $robotActivity, [
            'triggered_by' => $triggerActivity,
            'is_robot_activity' => true,
        ]);

        if ($success) {
            $this->broadcastRobotActivity($robot, $robotActivity, $triggerActivity);
            
            Log::info("Robot activity triggered", [
                'robot_id' => $robot->id,
                'robot_name' => $robot->first_name,
                'activity' => $robotActivity,
                'trigger' => $triggerActivity,
            ]);
        }
    }

    private function selectRobotActivity(): string
    {
        $activities = [
            'quiz_completed' => 35,
            'daily_login' => 25,
            'profile_updated' => 15,
            'forum_participation' => 20,
            'achievement_unlocked' => 5,
        ];

        $random = random_int(1, 100);
        $cumulative = 0;
        
        foreach ($activities as $activity => $weight) {
            $cumulative += $weight;
            if ($random <= $cumulative) {
                return $activity;
            }
        }
        
        return 'daily_login';
    }

    private function broadcastRobotActivity(User $robot, string $activity, string $trigger): void
    {
        $message = $this->generateActivityMessage($robot, $activity, $trigger);
        
        Cache::put('latest_robot_notification', [
            'message' => $message,
            'robot_name' => $robot->first_name,
            'activity' => $activity,
            'timestamp' => now()->diffForHumans(),
            'trigger' => $trigger,
        ], 300);
    }

    private function generateActivityMessage(User $robot, string $activity, string $trigger): string
    {
        $robotName = $robot->first_name;
        
        $messages = [
            'quiz_completed' => [
                'user_quiz_completed' => "{$robotName} just completed a test and earned points!",
                'user_signup' => "Inspired by the new member, {$robotName} just aced a test!",
                'user_subscription_purchased' => "{$robotName} felt motivated and just completed a test!",
                'default' => "{$robotName} just completed a test!",
            ],
            'daily_login' => [
                'user_login' => "{$robotName} is also online and ready to learn!",
                'default' => "{$robotName} just logged in to practice!",
            ],
            'profile_updated' => [
                'default' => "{$robotName} updated their profile and earned points!",
            ],
            'forum_participation' => [
                'user_forum_post' => "{$robotName} joined the discussion in the forum!",
                'default' => "{$robotName} is active in the community!",
            ],
            'achievement_unlocked' => [
                'default' => "{$robotName} unlocked an achievement!",
            ],
        ];

        $triggerMessages = $messages[$activity] ?? [];
        return $triggerMessages[$trigger] ?? ($triggerMessages['default'] ?? "{$robotName} is active!");
    }

    public function getLatestRobotNotification(): ?array
    {
        return Cache::get('latest_robot_notification');
    }

    public function ensureRobotActivity(): void
    {
        $recentRealActivity = Cache::get('recent_real_user_activity', false);
        $robotActivityCount = Cache::get('robot_activity_count', 0);
        
        // Only trigger robot activity if no real user activity recently or robots haven't been too active
        if (!$recentRealActivity && $robotActivityCount < 3) {
            $this->simulateMinimalRobotActivity();
            Cache::increment('robot_activity_count');
        }
        
        // Reset counters periodically
        if (Cache::get('robot_activity_count', 0) > 10) {
            Cache::put('robot_activity_count', 0, 3600); // Reset every hour
        }
    }

    private function simulateMinimalRobotActivity(): void
    {
        $robot = $this->selectRandomRobot();
        if (!$robot) {
            return;
        }

        $robotPoint = UserPoint::where('user_id', $robot->id)->first();
        if (!$robotPoint || $robotPoint->total_points < 50) {
            $this->awardPointsToRobot($robot, 'system_maintenance');
        }
    }

    public function markRealUserActivity(): void
    {
        Cache::put('recent_real_user_activity', true, 3600);
    }
}
