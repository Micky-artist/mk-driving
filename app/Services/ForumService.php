<?php

namespace App\Services;

use App\Models\ForumQuestion;
use App\Models\User;
use App\Services\PointsService;

class ForumService
{
    /**
     * Get forum data for homepage including leaderboard and top question
     * 
     * @param string $locale
     * @param int $leaderboardLimit
     * @return array
     */
    public function getHomepageData(string $locale, int $leaderboardLimit = 3): array
    {
        \Illuminate\Support\Facades\Log::debug('ForumService getHomepageData called', ['locale' => $locale, 'limit' => $leaderboardLimit]);
        
        try {
            $data = [
                'leaderboard' => $this->getTopPerformers($leaderboardLimit),
                'topQuestion' => $this->getTopQuestionThisMonth($locale),
                'stats' => $this->getForumStats(),
            ];
            
            \Illuminate\Support\Facades\Log::debug('ForumService getHomepageData completed', ['data' => $data]);
            return $data;
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('ForumService getHomepageData failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return [
                'leaderboard' => [],
                'topQuestion' => null,
                'stats' => [
                    'totalQuestions' => 0,
                    'totalAnswers' => 0,
                    'activeUsers' => 0,
                    'thisMonthQuestions' => 0
                ]
            ];
        }
    }

    /**
     * Get top performers from leaderboard
     * 
     * @param int $limit
     * @return array
     */
    private function getTopPerformers(int $limit): array
    {
        \Illuminate\Support\Facades\Log::debug('getTopPerformers called', ['limit' => $limit]);
        
        try {
            $pointsService = app(PointsService::class);
            \Illuminate\Support\Facades\Log::debug('PointsService instantiated');
            
            $leaderboard = $pointsService->getLeaderboard($limit, 'weekly');
            \Illuminate\Support\Facades\Log::debug('Leaderboard retrieved', ['leaderboard' => $leaderboard]);

            return collect($leaderboard)->map(function ($performer, $index) {
                return [
                    'rank' => $index + 1,
                    'user' => [
                        'firstName' => $performer['user']['first_name'] ?? 'Anonymous',
                        'lastName' => $performer['user']['last_name'] ?? 'User',
                        'fullName' => ($performer['user']['first_name'] ?? 'Anonymous') . ' ' . ($performer['user']['last_name'] ?? 'User'),
                        'initials' => substr($performer['user']['first_name'] ?? 'A', 0, 1) . substr($performer['user']['last_name'] ?? 'U', 0, 1)
                    ],
                    'points' => $performer['points'] ?? 0,
                    'rankChange' => $performer['rank_change'] ?? 0
                ];
            })->toArray();
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('getTopPerformers failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return [];
        }
    }

    /**
     * Get top performing question for the current month
     * 
     * @param string $locale
     * @return array|null
     */
    private function getTopQuestionThisMonth(string $locale): ?array
    {
        \Illuminate\Support\Facades\Log::debug('getTopQuestionThisMonth called', ['locale' => $locale]);
        
        try {
            $topQuestion = ForumQuestion::withCount('answers')
                ->with(['answers' => function($query) {
                    $query->where('is_approved', true)
                        ->orderBy('votes', 'desc')
                        ->with('user')
                        ->take(1); // Only get the top voted answer
                }, 'user'])
                ->where('is_approved', true)
                ->where('created_at', '>=', now()->subDays(30))
                ->orderByRaw('answers_count DESC, views DESC')
                ->first();
            
            \Illuminate\Support\Facades\Log::debug('Top question query result', [
                'found' => $topQuestion ? true : false,
                'question_id' => $topQuestion?->id,
                'answers_count' => $topQuestion?->answers_count
            ]);

            if (!$topQuestion) {
                \Illuminate\Support\Facades\Log::debug('No top question found, returning null');
                return null;
            }

            $title = $topQuestion->title;
            $content = $topQuestion->content;
            $fallbackLocale = config('app.fallback_locale', 'rw');
            
            \Illuminate\Support\Facades\Log::debug('Processing question data', [
                'title_type' => gettype($title),
                'content_type' => gettype($content),
                'fallback_locale' => $fallbackLocale
            ]);

            $result = [
                'id' => $topQuestion->id,
                'title' => is_array($title) 
                    ? ($title[$locale] ?? $title[$fallbackLocale] ?? 'No title')
                    : $title,
                'content' => is_array($content) 
                    ? ($content[$locale] ?? $content[$fallbackLocale] ?? '')
                    : $content,
                'excerpt' => \Illuminate\Support\Str::limit(
                    strip_tags(
                        is_array($content) 
                            ? ($content[$locale] ?? $content[$fallbackLocale] ?? '')
                            : $content
                    ), 
                    200
                ),
                'slug' => $topQuestion->id, // Using ID as slug for now
                'stats' => [
                    'views' => $topQuestion->views ?? 0,
                    'answersCount' => $topQuestion->answers_count ?? 0,
                    'votes' => $topQuestion->votes ?? 0,
                    'createdAt' => $topQuestion->created_at->toISOString(),
                    'timeAgo' => $topQuestion->created_at->diffForHumans()
                ],
                'author' => [
                    'firstName' => $topQuestion->user->first_name ?? 'Anonymous',
                    'lastName' => $topQuestion->user->last_name ?? 'User',
                    'fullName' => ($topQuestion->user->first_name ?? 'Anonymous') . ' ' . ($topQuestion->user->last_name ?? 'User'),
                    'initials' => substr($topQuestion->user->first_name ?? 'A', 0, 1) . substr($topQuestion->user->last_name ?? 'U', 0, 1)
                ],
                'topics' => $topQuestion->topics ?? [],
                'topAnswers' => $topQuestion->answers->map(function($answer) use ($locale, $fallbackLocale) {
                    $answerContent = $answer->content;
                    return [
                        'id' => $answer->id,
                        'content' => is_array($answerContent) 
                            ? ($answerContent[$locale] ?? $answerContent[$fallbackLocale] ?? 'No content')
                            : $answerContent,
                        'excerpt' => \Illuminate\Support\Str::limit(strip_tags(
                            is_array($answerContent) 
                                ? ($answerContent[$locale] ?? $answerContent[$fallbackLocale] ?? 'No content')
                                : $answerContent
                        ), 100),
                        'author' => [
                            'firstName' => $answer->user->first_name ?? 'Anonymous',
                            'lastName' => $answer->user->last_name ?? 'User',
                            'fullName' => ($answer->user->first_name ?? 'Anonymous') . ' ' . ($answer->user->last_name ?? 'User'),
                            'initials' => substr($answer->user->first_name ?? 'A', 0, 1) . substr($answer->user->last_name ?? 'U', 0, 1)
                        ],
                        'stats' => [
                            'votes' => $answer->votes ?? 0,
                            'createdAt' => $answer->created_at->toISOString(),
                            'timeAgo' => $answer->created_at->diffForHumans()
                        ]
                    ];
                })->toArray()
            ];
            
            \Illuminate\Support\Facades\Log::debug('getTopQuestionThisMonth completed', [
                'result_id' => $result['id'],
                'result_title' => $result['title'],
                'answers_count' => count($result['topAnswers'])
            ]);
            
            return $result;
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('getTopQuestionThisMonth failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return null;
        }
    }

    /**
     * Get general forum statistics
     * 
     * @return array
     */
    private function getForumStats(): array
    {
        \Illuminate\Support\Facades\Log::debug('getForumStats called');
        
        try {
            $totalQuestions = ForumQuestion::where('is_approved', true)->count();
            $totalAnswers = \App\Models\ForumAnswer::where('is_approved', true)->count();
            $activeUsers = User::whereHas('forumQuestions')->orWhereHas('forumAnswers')->count();
            $thisMonthQuestions = ForumQuestion::where('is_approved', true)
                ->where('created_at', '>=', now()->startOfMonth())
                ->count();
                
            $stats = [
                'totalQuestions' => $totalQuestions,
                'totalAnswers' => $totalAnswers,
                'activeUsers' => $activeUsers,
                'thisMonthQuestions' => $thisMonthQuestions
            ];
            
            \Illuminate\Support\Facades\Log::debug('getForumStats completed', ['stats' => $stats]);
            return $stats;
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('getForumStats failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return [
                'totalQuestions' => 0,
                'totalAnswers' => 0,
                'activeUsers' => 0,
                'thisMonthQuestions' => 0
            ];
        }
    }
}
