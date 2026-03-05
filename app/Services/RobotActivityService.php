<?php

namespace App\Services;

use App\Models\User;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\UserAnswer;
use App\Models\UserPoint;
use App\Jobs\TriggerRobotActivity;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

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

        // Get 5 random robots for more vibrant community feel
        $robots = $this->selectRandomRobots(5);
        if (empty($robots)) {
            return;
        }

        foreach ($robots as $index => $robot) {
            // Calculate realistic delay (2-8 seconds for live competition)
            $delay = $this->calculateRealisticDelay($activity);
            $actualDelay = min($delay, 8);
            
            // Award points to robot based on real user activity
            $this->awardPointsToRobot($robot, $activity);

            // Create quiz attempt if user answered a question
            if ($activity === 'user_quiz_answer' && isset($data['quiz_id'])) {
                $this->createRobotQuizAttempt($robot, $data['quiz_id']);
            }

            // Store delayed activity for frontend to handle
            $this->storeDelayedRobotActivity($robot, $activity, $actualDelay);

            Log::info("Robot activity scheduled", [
                'robot_id' => $robot->id,
                'robot_name' => $robot->first_name,
                'activity' => $activity,
                'delay_seconds' => $actualDelay,
            ]);
        }
    }

    /**
     * Store delayed robot activity for frontend to handle with staggered timing
     */
    private function storeDelayedRobotActivity(User $robot, string $activity, int $delay): void
    {
        $message = $this->generateRobotActivityMessage($robot, $activity);
        
        // Store in cache with delay for frontend to pick up
        $activityData = [
            'robot_name' => $robot->first_name,
            'message' => $message,
            'activity' => $activity,
            'timestamp' => timeDiffForHumans(now()),
            'delay' => $delay,
            'is_correct' => rand(1, 100) <= 75 // 75% chance of being correct
        ];
        
        Cache::put("robot_activity_{$robot->id}_{$activity}", $activityData, 300); // 5 minutes
    }

    /**
     * Generate realistic robot activity message
     */
    private function generateRobotActivityMessage(User $robot, string $activity): string
    {
        $robotName = $robot->first_name;
        
        if ($activity === 'user_quiz_answer') {
            $messages = [
                __('quiz.companion.robot_correct', ['name' => $robotName]),
                __('quiz.companion.robot_nailed', ['name' => $robotName]),
                __('quiz.companion.robot_aced', ['name' => $robotName]),
                __('quiz.companion.robot_mastered', ['name' => $robotName]),
                __('quiz.companion.robot_wrong', ['name' => $robotName]),
                __('quiz.companion.robot_tricky', ['name' => $robotName]),
                __('quiz.companion.robot_missed', ['name' => $robotName]),
                __('quiz.companion.robot_struggled', ['name' => $robotName])
            ];
            return $messages[array_rand($messages)];
        }
        
        return "$robotName is learning";
    }

    /**
     * Create a realistic robot quiz attempt
     */
    private function createRobotQuizAttempt(User $robot, int $quizId): void
    {
        try {
            // Don't create if robot already has recent attempt for this quiz
            $existingAttempt = QuizAttempt::where('user_id', $robot->id)
                ->where('quiz_id', $quizId)
                ->where('created_at', '>=', now()->subHours(1))
                ->first();

            if ($existingAttempt) {
                return; // Robot already attempted recently
            }

            // Get quiz questions
            $quiz = Quiz::find($quizId);
            if (!$quiz) {
                return;
            }

            $questions = $quiz->questions()->with('options')->get();
            if ($questions->isEmpty()) {
                return;
            }

            // Create quiz attempt using save() instead of create()
            try {
                $attempt = new QuizAttempt();
                $attempt->user_id = $robot->id;
                $attempt->quiz_id = $quizId;
                $attempt->status = 'COMPLETED';
                $attempt->score = rand(60, 95);
                $attempt->time_spent_seconds = rand(300, 900);
                $attempt->started_at = now()->subMinutes(rand(10, 30));
                $attempt->completed_at = now();
                $attempt->total_questions = $questions->count();
                
                Log::info("About to save robot attempt", [
                    'robot_id' => $robot->id,
                    'robot_name' => $robot->first_name,
                    'quiz_id' => $quizId,
                    'user_exists' => User::find($robot->id) ? 'YES' : 'NO',
                    'quiz_exists' => Quiz::find($quizId) ? 'YES' : 'NO'
                ]);
                
                $attempt->save();
                
                // Get the ID directly from database to avoid refresh() racism
                $attemptId = DB::table('quiz_attempts')
                    ->where('user_id', $robot->id)
                    ->where('quiz_id', $quizId)
                    ->orderBy('created_at', 'desc')
                    ->value('id');
                
                $attempt->id = $attemptId;

                Log::info("Robot quiz attempt saved", [
                    'robot_id' => $robot->id,
                    'attempt_id' => $attempt->id,
                    'was_saved' => $attemptId ? 'YES' : 'NO'
                ]);

            } catch (\Exception $e) {
                Log::error("Exception creating robot quiz attempt", [
                    'robot_id' => $robot->id,
                    'robot_name' => $robot->first_name,
                    'quiz_id' => $quizId,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                return;
            }

            if (!$attempt || !$attempt->id) {
                Log::error("Failed to create robot quiz attempt - no ID", [
                    'robot_id' => $robot->id,
                    'robot_name' => $robot->first_name,
                    'quiz_id' => $quizId,
                    'attempt_object' => $attempt ? 'EXISTS' : 'NULL',
                    'attempt_id' => $attempt->id ?? 'NULL'
                ]);
                return;
            }

            Log::info("Robot quiz attempt created successfully", [
                'robot_id' => $robot->id,
                'robot_name' => $robot->first_name,
                'attempt_id' => $attempt->id,
                'quiz_id' => $quizId
            ]);

            // Create answers for some questions (not all to be realistic)
            $questionsToAnswer = $questions->random(rand(ceil($questions->count() * 0.7), $questions->count()));
            
            Log::info("Creating robot answers", [
                'robot_id' => $robot->id,
                'robot_name' => $robot->first_name,
                'total_questions' => $questions->count(),
                'questions_to_answer' => $questionsToAnswer->count()
            ]);
            
            foreach ($questionsToAnswer as $question) {
                $correctOption = $question->options->where('is_correct', true)->first();
                $wrongOptions = $question->options->where('is_correct', false);
                
                // Robot gets 70-80% of questions right
                $isCorrect = rand(1, 100) <= 75;
                $selectedOption = $isCorrect ? $correctOption : $wrongOptions->random();

                // Make sure we have the attempt ID before creating answers
                if ($attempt && $attempt->id) {
                    UserAnswer::create([
                        'quiz_attempt_id' => $attempt->id,
                        'question_id' => $question->id,
                        'option_id' => $selectedOption->id,
                        'is_correct' => $isCorrect,
                        'points_earned' => $isCorrect ? rand(5, 10) : 0,
                        'created_at' => now()->subSeconds(rand(60, 300))
                    ]);
                    
                    Log::info("Robot answer created", [
                        'robot_id' => $robot->id,
                        'question_id' => $question->id,
                        'is_correct' => $isCorrect
                    ]);
                } else {
                    Log::error("Failed to create robot answer - no attempt ID", [
                        'robot_id' => $robot->id,
                        'attempt_exists' => isset($attempt),
                        'attempt_id' => $attempt->id ?? 'null'
                    ]);
                }
            }

            Log::info("Robot quiz attempt created", [
                'robot_id' => $robot->id,
                'robot_name' => $robot->first_name,
                'quiz_id' => $quizId,
                'score' => $attempt->score,
                'questions_answered' => $questionsToAnswer->count()
            ]);

        } catch (\Exception $e) {
            Log::error("Failed to create robot quiz attempt", [
                'robot_id' => $robot->id,
                'quiz_id' => $quizId,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Select multiple random robots for activity
     */
    private function selectRandomRobots(int $count = 5): array
    {
        return User::where('is_robot', true)
            ->inRandomOrder()
            ->limit($count)
            ->get()
            ->all(); // Return User objects, not arrays
    }

    private function shouldTriggerRobotActivity(string $activity): bool
    {
        $probabilities = [
            'user_signup' => 60,
            'user_quiz_completed' => 40,
            'user_quiz_answer' => 70, // Increased from 35% to 70% for better live competition
            'user_subscription_purchased' => 70,
            'user_login' => 25,
            'user_forum_post' => 30,
        ];

        $probability = $probabilities[$activity] ?? 20;
        return (random_int(1, 100) <= $probability);
    }

    /**
     * Select a single random robot
     */
    private function selectRandomRobot(): ?User
    {
        return User::where('is_robot', true)->inRandomOrder()->first();
    }

    private function calculateRealisticDelay(string $activity): int
    {
        $baseDelays = [
            'user_signup' => [300, 900],        // 5-15 minutes after signup
            'user_quiz_completed' => [120, 600], // 2-10 minutes after quiz
            'user_quiz_answer' => [2, 8],       // New: 2-8 seconds for live competition
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
                'user_quiz_answer' => "{$robotName} is also taking this quiz and doing great!",
                'user_signup' => "Inspired by the new member, {$robotName} just aced a test!",
                'user_subscription_purchased' => "{$robotName} felt motivated and just completed a test!",
                'default' => "{$robotName} just completed a test!",
            ],
            'daily_login' => [
                'user_login' => "{$robotName} is also online and ready to learn!",
                'user_quiz_answer' => "{$robotName} is practicing alongside you!",
                'default' => "{$robotName} just logged in to practice!",
            ],
            'profile_updated' => [
                'user_quiz_answer' => "{$robotName} updated their profile during the quiz!",
                'default' => "{$robotName} updated their profile and earned points!",
            ],
            'forum_participation' => [
                'user_forum_post' => "{$robotName} joined the discussion in the forum!",
                'user_quiz_answer' => "{$robotName} is taking a break from forum to quiz!",
                'default' => "{$robotName} is active in the community!",
            ],
            'achievement_unlocked' => [
                'user_quiz_answer' => "{$robotName} unlocked an achievement during the quiz!",
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
