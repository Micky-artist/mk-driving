<?php

namespace App\Http\Middleware;

use App\Services\PointsService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TrackUserActivity
{
    public function __construct(private PointsService $pointsService)
    {
    }

    public function handle(Request $request, Closure $next, string $activityType = null)
    {
        $response = $next($request);

        if (Auth::check()) {
            // Check for daily visit points on home page
            if ($this->shouldAwardDailyVisitPoints($request)) {
                $this->awardDailyVisitPoints($request);
            }
            
            // Handle specific activity types if provided
            if ($activityType) {
                $this->trackActivity($activityType, $request);
            }
        }

        return $response;
    }

    private function shouldAwardDailyVisitPoints(Request $request): bool
    {
        // Only award points for GET requests to the home page
        return $request->isMethod('GET') && 
               $request->routeIs('home') &&
               !$request->ajax();
    }

    private function awardDailyVisitPoints(Request $request): void
    {
        $userId = Auth::id();
        
        $metadata = [
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'visit_date' => date('Y-m-d'),
            'visited_at' => atomString(),
        ];

        $this->pointsService->awardPoints($userId, 'daily_visit', $metadata);
    }

    private function trackActivity(string $activityType, Request $request): void
    {
        $userId = Auth::id();
        $metadata = $this->getActivityMetadata($activityType, $request);

        match ($activityType) {
            'login' => $this->pointsService->awardPoints($userId, 'login', $metadata),
            'quiz_started' => $this->pointsService->awardPoints($userId, 'quiz_started', $metadata),
            'quiz_completed' => $this->pointsService->awardPoints($userId, 'quiz_completed', $metadata),
            'quiz_passed' => $this->pointsService->awardPoints($userId, 'quiz_passed', $metadata),
            'quiz_perfect' => $this->pointsService->awardPoints($userId, 'quiz_perfect', $metadata),
            'question_asked' => $this->pointsService->awardPoints($userId, 'question_asked', $metadata),
            'question_answered' => $this->pointsService->awardPoints($userId, 'question_answered', $metadata),
            default => null,
        };
    }

    private function getActivityMetadata(string $activityType, Request $request): array
    {
        return match ($activityType) {
            'quiz_started', 'quiz_completed', 'quiz_passed', 'quiz_perfect' => [
                'quiz_id' => $request->route('quiz'),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ],
            'question_asked' => [
                'question_id' => $request->route('question'),
                'forum_question_id' => $request->input('forum_question_id'),
            ],
            'question_answered' => [
                'answer_id' => $request->route('answer'),
                'question_id' => $request->route('question'),
            ],
            'login' => [
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ],
            default => [],
        };
    }
}
