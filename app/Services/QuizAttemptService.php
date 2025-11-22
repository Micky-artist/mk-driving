<?php

namespace App\Services;

use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class QuizAttemptService
{
    public function __construct()
    {
        // Constructor if needed
    }

    /**
     * Start a new quiz attempt
     */
    public function startQuizAttempt(Quiz $quiz, User $user): QuizAttempt
    {
        return DB::transaction(function () use ($quiz, $user) {
            // Check for existing in-progress attempts
            $existingAttempt = QuizAttempt::where('user_id', $user->id)
                ->where('quiz_id', $quiz->id)
                ->where('status', 'in_progress')
                ->first();

            if ($existingAttempt) {
                return $existingAttempt;
            }

            // Create new attempt
            return QuizAttempt::create([
                'user_id' => $user->id,
                'quiz_id' => $quiz->id,
                'started_at' => now(),
                'status' => 'in_progress',
                'total_questions' => $quiz->questions()->count(),
            ]);
        });
    }

    /**
     * Submit answers for a quiz attempt
     */
    public function submitAnswers(QuizAttempt $attempt, array $answers): QuizAttempt
    {
        return DB::transaction(function () use ($attempt, $answers) {
            if ($attempt->status !== 'in_progress') {
                throw new \Exception('Cannot submit answers for a completed or abandoned attempt');
            }

            $quiz = $attempt->quiz;
            $questions = $quiz->questions()->with('options')->get();
            $score = 0;
            $correctAnswers = 0;
            $userAnswers = [];

            foreach ($questions as $question) {
                $userAnswer = $answers[$question->id] ?? null;
                $correctOption = $question->options->where('is_correct', true)->first();
                $isCorrect = $correctOption && $correctOption->id == $userAnswer;

                $userAnswers[] = [
                    'question_id' => $question->id,
                    'option_id' => $userAnswer,
                    'is_correct' => $isCorrect,
                    'created_at' => now(),
                ];

                if ($isCorrect) {
                    $score++;
                    $correctAnswers++;
                }
            }

            // Store the answers
            $attempt->userAnswers()->createMany($userAnswers);

            // Update attempt
            $attempt->update([
                'score' => $score,
                'correct_answers' => $correctAnswers,
                'completed_at' => now(),
                'status' => 'completed',
                'time_spent_seconds' => now()->diffInSeconds($attempt->started_at),
            ]);

            return $attempt->load('userAnswers');
        });
    }

    /**
     * Get user's quiz attempts
     */
    public function getUserAttempts(User $user, array $filters = [])
    {
        $query = QuizAttempt::with(['quiz', 'userAnswers'])
            ->where('user_id', $user->id)
            ->latest('created_at');

        if (isset($filters['quiz_id'])) {
            $query->where('quiz_id', $filters['quiz_id']);
        }

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->paginate($filters['per_page'] ?? 15);
    }

    /**
     * Get quiz attempt statistics
     */
    public function getQuizStats(Quiz $quiz): array
    {
        return [
            'total_attempts' => $quiz->attempts()->count(),
            'average_score' => round($quiz->attempts()->avg('score') ?? 0, 2),
            'completion_rate' => $this->calculateCompletionRate($quiz),
            'average_time_spent' => round($quiz->attempts()->avg('time_spent_seconds') ?? 0, 2),
        ];
    }

    protected function calculateCompletionRate(Quiz $quiz): float
    {
        $total = $quiz->attempts()->count();
        if ($total === 0) {
            return 0;
        }

        $completed = $quiz->attempts()->where('status', 'completed')->count();
        return round(($completed / $total) * 100, 2);
    }
}
