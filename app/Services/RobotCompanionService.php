<?php

namespace App\Services;

use App\Models\User;
use App\Models\QuizAttempt;
use App\Models\Question;
use App\Models\Option;
use App\Services\PointsService;
use App\Services\RobotActivityService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;

class RobotCompanionService
{
    private PointsService $pointsService;
    private RobotActivityService $robotActivityService;

    public function __construct(PointsService $pointsService, RobotActivityService $robotActivityService)
    {
        $this->pointsService = $pointsService;
        $this->robotActivityService = $robotActivityService;
    }

    /**
     * Get real historical activity from the last 24 hours for this quiz
     */
    public function getHistoricalActivity(int $quizId, int $currentUserId): array
    {
        Log::info('getHistoricalActivity called', [
            'quizId' => $quizId,
            'currentUserId' => $currentUserId
        ]);

        // Get all user attempts - no time filter, just get the latest 50 messages worth
        $historicalAttempts = QuizAttempt::where('quiz_id', $quizId)
            ->where('user_id', '!=', $currentUserId) // Exclude current user
            ->where('status', 'COMPLETED') // Use correct enum value
            ->with(['user', 'userAnswers.question', 'userAnswers.option'])
            ->orderBy('created_at', 'desc')
            ->limit(100) // Get more attempts to have enough for 50 messages
            ->get();

        Log::info('Attempts found', ['count' => $historicalAttempts->count()]);

        $activities = [];
        $userActivities = []; // Track one activity per user
        
        foreach ($historicalAttempts as $attempt) {
            // Double-check we're not including current user
            if ($attempt->user_id == $currentUserId) {
                continue;
            }
            
            // Skip if we already have an activity from this user
            if (isset($userActivities[$attempt->user_id])) {
                continue;
            }
            
            // Get only one answer per user (their most recent one)
            $latestAnswer = $attempt->userAnswers->first();
            if (!$latestAnswer) {
                continue;
            }
            
            // Add to activities and track this user
            $activities[] = [
                'type' => 'learner_answer',
                'learner_id' => $attempt->user_id,
                'learner_name' => $attempt->user->first_name,
                'quiz_id' => $quizId,
                'question_id' => $latestAnswer->question_id,
                'is_correct' => $latestAnswer->is_correct,
                'timestamp' => $attempt->created_at->timestamp, // Use actual timestamp
                'timestamp_human' => timeDiffForHumans($attempt->created_at), // Pass Carbon object directly
                'message' => $this->generateLearningMessage($attempt->user, $latestAnswer->is_correct, $latestAnswer->question),
                'is_robot' => null // Don't distinguish - all are learners
            ];
            
            $userActivities[$attempt->user_id] = true;
            
            // Stop if we have 5 users
            if (count($userActivities) >= 5) {
                break;
            }
        }

        // Sort by timestamp (newest first) and cap to 50 messages
        usort($activities, function($a, $b) {
            // Use actual timestamps for proper sorting
            return $b['timestamp'] - $a['timestamp'];
        });

        $activities = array_slice($activities, 0, 50); // Cap to 50 messages

        Log::info('Activities generated', ['count' => count($activities), 'unique_users' => count($userActivities)]);

        return $activities;
    }

    /**
     * Generate learning-focused message for real user activity
     */
    private function generateLearningMessage($user, bool $isCorrect, $question): string
    {
        $userName = $user->first_name;
        
        if ($isCorrect) {
            $messages = [
                __('quiz.companion.learner_correct', ['name' => $userName]),
                __('quiz.companion.learner_nailed', ['name' => $userName]),
                __('quiz.companion.learner_got_correct', ['name' => $userName]),
                __('quiz.companion.learner_aced', ['name' => $userName]),
                __('quiz.companion.learner_mastered', ['name' => $userName]),
                __('quiz.companion.learner_figured', ['name' => $userName]),
                __('quiz.companion.learner_got_right', ['name' => $userName]),
            ];
        } else {
            $messages = [
                __('quiz.companion.learner_wrong', ['name' => $userName]),
                __('quiz.companion.learner_struggled', ['name' => $userName]),
                __('quiz.companion.learner_tricky', ['name' => $userName]),
                __('quiz.companion.learner_wrong_too', ['name' => $userName]),
                __('quiz.companion.learner_missed', ['name' => $userName]),
                __('quiz.companion.learner_didnt_get', ['name' => $userName]),
                __('quiz.companion.learner_wrong_dont_worry', ['name' => $userName]),
            ];
        }
        
        return $messages[array_rand($messages)];
    }

    /**
     * Handle quiz answer activity with minimal, smart messaging
     */
    public function handleQuizAnswerActivity(int $quizId, int $userId, int $questionId, bool $isCorrect): array
    {
        Log::info('handleQuizAnswerActivity called', [
            'quizId' => $quizId,
            'userId' => $userId,
            'questionId' => $questionId,
            'isCorrect' => $isCorrect
        ]);

        // Use existing robot activity system
        $this->robotActivityService->handleUserActivity('user_quiz_answer', [
            'quiz_id' => $quizId,
            'question_id' => $questionId,
            'is_correct' => $isCorrect,
            'user_id' => $userId
        ]);

        // Check if we should show a message for this question
        if (!$this->shouldShowMessage($quizId, $userId, $questionId, $isCorrect)) {
            Log::info('shouldShowMessage returned false', [
                'quizId' => $quizId,
                'userId' => $userId,
                'questionId' => $questionId
            ]);
            return []; // No message this time
        }

        Log::info('shouldShowMessage returned true, generating robot response');

        // Get only ONE active robot for this question
        $activeRobots = $this->getActiveRobots($userId);
        if (empty($activeRobots)) {
            Log::warning('No active robots found', ['userId' => $userId]);
            return [];
        }

        // Select a random robot
        $selectedRobot = $activeRobots[array_rand($activeRobots)];
        
        // Generate single response
        $response = $this->generateCompetitiveRobotResponse($selectedRobot, $quizId, $questionId, $isCorrect, []);

        // Store minimal activity
        $this->storeMinimalActivity($quizId, $userId, $response);

        Log::info('Robot response generated', [
            'robot_id' => $selectedRobot->id,
            'robot_name' => $selectedRobot->first_name,
            'message' => $response['message']
        ]);

        return [$response];
    }

    /**
     * Determine if a message should be shown based on smart criteria
     */
    private function shouldShowMessage(int $quizId, int $userId, int $questionId, bool $isCorrect): bool
    {
        $cacheKey = "companion_messages_{$userId}_{$quizId}";
        $messageHistory = Cache::get($cacheKey, []);
        
        $currentQuestion = count($messageHistory) + 1;
        
        Log::info('shouldShowMessage check', [
            'currentQuestion' => $currentQuestion,
            'messageHistory' => $messageHistory,
            'quizId' => $quizId,
            'userId' => $userId
        ]);
        
        // Show messages on almost every question for constant live competition
        // Only skip a few questions to avoid overwhelming the user
        $skipQuestions = [9, 11, 13, 14, 16, 17, 19, 21, 22, 23, 24]; // Skip some mid-range questions
        if (in_array($currentQuestion, $skipQuestions)) {
            Log::info('Skipping message - question in skip list', ['currentQuestion' => $currentQuestion]);
            return false;
        }
        
        // Show on all other questions (1-8, 10, 12, 15, 18, 20, 25+)
        
        // Show encouragement if user is struggling
        if (count($messageHistory) >= 3) {
            $lastThree = array_slice($messageHistory, -3);
            $wrongCount = count(array_filter($lastThree, fn($m) => !$m['was_correct']));
            if ($wrongCount >= 2) {
                Log::info('Showing message - user struggling');
                return true; // User needs encouragement
            }
        }
        
        // Default to showing message for live competition feel
        Log::info('Showing message - default case');
        return true;
    }

    /**
     * Store single activity for minimal notifications
     */
    private function storeMinimalActivity(int $quizId, int $userId, array $response): void
    {
        $cacheKey = "companion_messages_{$userId}_{$quizId}";
        $messageHistory = Cache::get($cacheKey, []);
        
        $messageHistory[] = [
            'question_number' => count($messageHistory) + 1,
            'was_correct' => $response['is_correct'] ?? false,
            'timestamp' => now()
        ];
        
        // Keep only last 10 messages for pattern detection
        if (count($messageHistory) > 10) {
            $messageHistory = array_slice($messageHistory, -10);
        }
        
        Cache::put($cacheKey, $messageHistory, 3600); // 1 hour
        
        // Store single latest notification
        Cache::put('latest_companion_message', [
            'type' => 'learner_answer',
            'message' => $response['message'],
            'learner_name' => $response['learner_name'],
            'timestamp' => now()->timestamp,
            'timestamp_human' => now()->diffForHumans()
        ], 300);
    }

    /**
     * Generate simple robot response
     */
    private function generateCompetitiveRobotResponse($robot, int $quizId, int $questionId, bool $userCorrect, $otherUsers): array
    {
        $question = Question::with('options')->find($questionId);
        if (!$question) {
            return [];
        }

        $robotPersonality = $this->getRobotPersonality($robot->id);
        $correctOption = $question->options->where('is_correct', true)->first();
        
        // Simple robot performance logic
        $baseSkill = $robotPersonality['base_skill'] ?? 70;
        $successProbability = $userCorrect ? 
            max(50, $baseSkill - 10) : // User correct, robot adjusts
            min(90, $baseSkill + 10);  // User wrong, robot encourages
            
        $robotCorrect = (random_int(1, 100) <= $successProbability);
        $selectedOption = $robotCorrect ? $correctOption : $this->getWrongOption($question);

        return [
            'type' => 'learner_answer',
            'learner_id' => $robot->id,
            'learner_name' => $robot->first_name,
            'quiz_id' => $quizId,
            'question_id' => $questionId,
            'is_correct' => $robotCorrect,
            'selected_option_id' => $selectedOption->id,
            'response_time' => $this->generateResponseTime($robotPersonality),
            'personality' => $robotPersonality,
            'message' => $this->generateCompetitiveMessage($robot, $robotCorrect, $userCorrect, []),
            'timestamp' => now()
        ];
    }

    /**
     * Broadcast live quiz activity
     */
    private function broadcastLiveQuizActivity(int $quizId, int $userId, int $questionId, bool $isCorrect, array $robotResponses, $otherUsers): void
    {
        $activities = Cache::get('live_quiz_activities', []);
        
        // Add current user activity
        array_unshift($activities, [
            'type' => 'user_answer',
            'user_id' => $userId,
            'quiz_id' => $quizId,
            'question_id' => $questionId,
            'is_correct' => $isCorrect,
            'timestamp' => now()
        ]);

        // Add robot responses
        foreach ($robotResponses as $response) {
            array_unshift($activities, $response);
        }

        // Simulate other user activities (30% chance)
        foreach ($otherUsers as $user) {
            if (rand(1, 100) <= 30) {
                array_unshift($activities, [
                    'type' => 'user_answer',
                    'user_id' => $user['id'],
                    'user_name' => $user['first_name'],
                    'quiz_id' => $quizId,
                    'question_id' => $questionId,
                    'is_correct' => rand(1, 100) <= 70,
                    'timestamp' => now()->subSeconds(rand(1, 60))
                ]);
            }
        }

        // Keep last 100 activities
        $activities = array_slice($activities, 0, 100);
        Cache::put('live_quiz_activities', $activities, 300);

        // Update notification
        $robotCount = count($robotResponses);
        $userCount = $otherUsers->count();
        
        $notification = [
            'type' => 'live_competition',
            'message' => $this->generateLiveCompetitionMessage($robotCount, $userCount, $isCorrect),
            'robot_responses' => $robotResponses,
            'active_users' => $userCount,
            'timestamp' => now()->diffForHumans()
        ];
        
        Cache::put('latest_live_notification', $notification, 300);
    }

    /**
     * Generate live competition message
     */
    private function generateLiveCompetitionMessage(int $robotCount, int $userCount, bool $userCorrect): string
    {
        $key = 'quiz.companion.live_competition.';
        
        if ($robotCount > 0 && $userCount > 0) {
            $key .= $userCorrect ? 'learners_and_users_correct' : 'learners_and_users_wrong';
            return Lang::get($key, [
                'learnerCount' => $robotCount,
                'userCount' => $userCount
            ]);
        } elseif ($robotCount > 0) {
            $key .= $userCorrect ? 'learners_correct' : 'learners_wrong';
            return Lang::get($key, ['learnerCount' => $robotCount]);
        } elseif ($userCount > 0) {
            $key .= $userCorrect ? 'users_correct' : 'users_wrong';
            return Lang::get($key, ['userCount' => $userCount]);
        }
        
        $key .= $userCorrect ? 'solo_correct' : 'solo_wrong';
        return Lang::get($key);
    }

    /**
     * Generate competitive message based on real competition
     */
    private function generateCompetitiveMessage($robot, bool $robotCorrect, bool $userCorrect, $otherUsers): string
    {
        $robotName = $robot->first_name;
        $personality = $this->getRobotPersonality($robot->id);
        
        // Determine the scenario and get appropriate messages
        if ($userCorrect && $robotCorrect) {
            $scenario = 'both_correct';
        } elseif ($userCorrect && !$robotCorrect) {
            $scenario = 'user_correct_learner_wrong';
        } elseif (!$userCorrect && $robotCorrect) {
            $scenario = 'user_wrong_learner_correct';
        } else {
            $scenario = 'both_wrong';
        }
        
        $trait = $personality['trait'] ?? 'supportive';
        
        // Use the translation system instead of hardcoded messages
        $scenarioKey = $scenario;
        $traitKey = $trait;
        
        // Try to get the translated messages for this trait and scenario
        $translationKey = "quiz.companion.{$traitKey}.{$scenarioKey}";
        $messages = Lang::get($translationKey, ['learnerName' => $robotName]);
        
        // If translation is not found or not an array, fallback to a simple message
        if (!is_array($messages)) {
            $fallbackMessages = [
                'both_correct' => __('quiz.companion.both_correct', ['learnerName' => $robotName]),
                'user_correct_learner_wrong' => __('quiz.companion.user_correct_learner_wrong', ['learnerName' => $robotName]),
                'user_wrong_learner_correct' => __('quiz.companion.user_wrong_learner_correct', ['learnerName' => $robotName]),
                'both_wrong' => __('quiz.companion.both_wrong', ['learnerName' => $robotName]),
            ];
            $messages = $fallbackMessages[$scenario] ?? __('quiz.companion.default_message', ['learnerName' => $robotName]);
        }
        
        // Ensure we have an array of messages
        if (!is_array($messages)) {
            $messages = [$messages];
        }
        
        // Select a random message
        $message = $messages[array_rand($messages)];
        return $message;
    }

    /**
     * Get latest single companion message
     */
    public function getLatestActivities(): array
    {
        $message = Cache::get('latest_companion_message');
        
        // Clear the cache after retrieving to prevent duplicates
        if ($message) {
            Cache::forget('latest_companion_message');
            
            // Ensure the message has the correct format for frontend
            if (!isset($message['type'])) {
                $message['type'] = 'learner_answer';
            }
            if (!isset($message['timestamp'])) {
                $message['timestamp'] = now()->timestamp;
            }
            if (!isset($message['timestamp_human'])) {
                $message['timestamp_human'] = timeDiffForHumans(now());
            }
            if (!isset($message['learner_id'])) {
                $message['learner_id'] = null;
            }
            if (!isset($message['question_id'])) {
                $message['question_id'] = null;
            }
            
            return [$message];
        }
        
        return [];
    }

    /**
     * Get latest live notification
     */
    public function getLatestLiveNotification(): ?array
    {
        return Cache::get('latest_live_notification');
    }

    /**
     * Get active robots for companion system
     */
    private function getActiveRobots(int $userId): array
    {
        // Get 2-3 random robots to be active companions
        return User::where('is_robot', true)
            ->inRandomOrder()
            ->limit(rand(2, 3))
            ->get()
            ->all();
    }

    /**
     * Generate robot response based on user performance
     */
    private function generateRobotResponse(User $robot, Question $question, bool $userCorrect): array
    {
        $robotPersonality = $this->getRobotPersonality($robot->id);
        $correctOption = $question->options->where('is_correct', true)->first();
        
        // Calculate robot success probability based on user performance
        $successProbability = $this->calculateSuccessProbability($userCorrect, $robotPersonality);
        
        $robotCorrect = (random_int(1, 100) <= $successProbability);
        $selectedOption = $robotCorrect ? $correctOption : $this->getWrongOption($question);

        return [
            'robot_id' => $robot->id,
            'robot_name' => $robot->first_name,
            'question_id' => $question->id,
            'is_correct' => $robotCorrect,
            'selected_option_id' => $selectedOption->id,
            'response_time' => $this->generateResponseTime($robotPersonality),
            'personality' => $robotPersonality,
            'message' => $this->generateCompanionMessage($robot, $robotCorrect, $userCorrect)
        ];
    }

    /**
     * Get robot personality traits
     */
    private function getRobotPersonality(int $robotId): array
    {
        $personalities = [
            1 => ['name' => 'John', 'trait' => 'competitive', 'base_skill' => 75, 'response_speed' => 'fast'],
            2 => ['name' => 'Sarah', 'trait' => 'supportive', 'base_skill' => 68, 'response_speed' => 'medium'],
            3 => ['name' => 'Michael', 'trait' => 'analytical', 'base_skill' => 72, 'response_speed' => 'slow'],
            4 => ['name' => 'Grace', 'trait' => 'encouraging', 'base_skill' => 65, 'response_speed' => 'medium'],
            5 => ['name' => 'David', 'trait' => 'competitive', 'base_skill' => 70, 'response_speed' => 'fast'],
        ];

        return $personalities[$robotId] ?? $personalities[1];
    }

    /**
     * Calculate robot success probability based on user answer
     */
    private function calculateSuccessProbability(bool $userCorrect, array $personality): int
    {
        $baseSkill = $personality['base_skill'];
        
        if ($userCorrect) {
            // User got it right - robot has lower chance to maintain competition
            $probability = $baseSkill - rand(10, 20);
        } else {
            // User got it wrong - robot has higher chance to encourage
            $probability = $baseSkill + rand(5, 15);
        }

        // Keep within reasonable bounds (45-85%)
        return max(45, min(85, $probability));
    }

    /**
     * Get a wrong option for the robot
     */
    private function getWrongOption(Question $question): Option
    {
        $wrongOptions = $question->options->where('is_correct', false);
        return $wrongOptions->random();
    }

    /**
     * Generate realistic response time based on personality
     */
    private function generateResponseTime(array $personality): int
    {
        $baseTimes = [
            'fast' => [2, 5],
            'medium' => [4, 8], 
            'slow' => [6, 12]
        ];

        $range = $baseTimes[$personality['response_speed']] ?? [4, 8];
        return rand($range[0], $range[1]);
    }

    /**
     * Generate companion message based on performance
     */
    private function generateCompanionMessage(User $robot, bool $robotCorrect, bool $userCorrect): string
    {
        $robotName = $robot->first_name;
        $personality = $this->getRobotPersonality($robot->id);

        // Determine the scenario and get appropriate messages
        if ($userCorrect && $robotCorrect) {
            $scenario = 'both_correct';
        } elseif ($userCorrect && !$robotCorrect) {
            $scenario = 'user_correct_learner_wrong';
        } elseif (!$userCorrect && $robotCorrect) {
            $scenario = 'user_wrong_learner_correct';
        } else {
            $scenario = 'both_wrong';
        }
        
        $trait = $personality['trait'] ?? 'supportive';
        $messages = Lang::get("quiz.companion.{$trait}.{$scenario}");
        
        // Ensure we have an array of messages
        if (!is_array($messages)) {
            $messages = [$messages];
        }
        
        // Select a random message and replace the learner name
        $message = $messages[array_rand($messages)];
        return str_replace(':learnerName', $robotName, $message);
    }

    /**
     * Store robot answer for tracking
     */
    private function storeRobotAnswer(int $robotId, int $questionId, array $response): void
    {
        $cacheKey = "robot_answers_{$robotId}_" . session()->getId();
        $answers = Cache::get($cacheKey, []);
        $answers[$questionId] = $response;
        Cache::put($cacheKey, $answers, 3600); // 1 hour
    }

    /**
     * Broadcast robot activities to frontend
     */
    private function broadcastRobotActivities(array $robotResponses, bool $userCorrect): void
    {
        $notification = [
            'type' => 'robot_companion',
            'user_correct' => $userCorrect,
            'robots' => $robotResponses,
            'timestamp' => now()->diffForHumans(),
            'message' => $this->generateGroupMessage($robotResponses, $userCorrect)
        ];

        Cache::put('latest_robot_companion_notification', $notification, 300);
    }

    /**
     * Generate group message for multiple robots
     */
    private function generateGroupMessage(array $robotResponses, bool $userCorrect): string
    {
        $correctCount = count(array_filter($robotResponses, fn($r) => $r['is_correct']));
        $totalLearners = count($robotResponses);
        
        if ($userCorrect && $correctCount === $totalLearners) {
            return Lang::get('quiz.companion.group_messages.all_correct_user_correct');
        } elseif ($userCorrect && $correctCount > 0) {
            return Lang::get('quiz.companion.group_messages.some_correct_user_correct', [
                'correctCount' => $correctCount,
                'totalLearners' => $totalLearners
            ]);
        } elseif (!$userCorrect && $correctCount === $totalLearners) {
            return Lang::get('quiz.companion.group_messages.all_correct_user_wrong');
        } elseif (!$userCorrect && $correctCount > 0) {
            return Lang::get('quiz.companion.group_messages.some_correct_user_wrong', [
                'correctCount' => $correctCount
            ]);
        } else {
            return Lang::get('quiz.companion.group_messages.all_wrong');
        }
    }

    /**
     * Get robot quiz session summary
     */
    public function getRobotSessionSummary(int $userId, int $quizId): array
    {
        $activeRobots = $this->getActiveRobots($userId);
        $summaries = [];

        foreach ($activeRobots as $robot) {
            $cacheKey = "robot_answers_{$robot->id}_" . session()->getId();
            $answers = Cache::get($cacheKey, []);
            
            if (!empty($answers)) {
                $correctCount = count(array_filter($answers, fn($a) => $a['is_correct']));
                $totalQuestions = count($answers);
                $score = $totalQuestions > 0 ? round(($correctCount / $totalQuestions) * 100) : 0;
                
                $personality = $this->getRobotPersonality($robot->id);
                
                $summaries[] = [
                    'robot_name' => $robot->first_name,
                    'score' => $score,
                    'correct_answers' => $correctCount,
                    'total_answers' => $totalQuestions,
                    'personality' => $personality,
                    'final_message' => $this->generateFinalMessage($robot, $score, $personality)
                ];
            }
        }

        return $summaries;
    }

    /**
     * Generate final completion message
     */
    private function generateFinalMessage(User $robot, int $score, array $personality): string
    {
        $robotName = $robot->first_name;
        $trait = $personality['trait'] ?? 'supportive';
        
        // Determine performance category
        if ($score >= 80) {
            $category = 'excellent';
        } elseif ($score >= 60) {
            $category = 'good';
        } else {
            $category = 'needs_improvement';
        }
        
        $message = Lang::get("quiz.companion.final_messages.{$category}.{$trait}");
        return str_replace(':learnerName', $robotName, str_replace(':score', $score, $message));
    }

    /**
     * Get latest single companion notification
     */
    public function getLatestCompanionNotification(): ?array
    {
        return Cache::get('latest_companion_message');
    }
}
