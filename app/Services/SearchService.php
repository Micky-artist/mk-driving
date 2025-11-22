<?php

namespace App\Services;

use App\Models\Quiz;
use App\Models\News;
use App\Models\User;
use App\Models\SubscriptionPlan;
use Illuminate\Support\Facades\Auth;

class SearchService
{
    public function globalSearch(string $query, ?User $user = null)
    {
        $userRole = $user ? $user->role : null;
        
        $results = [
            $this->searchQuizzes($query, $userRole),
            $this->searchSubscriptionPlans($query),
            $this->searchNews($query),
        ];
        
        // If user is admin, include user search
        if ($user && $user->isAdmin()) {
            $results[] = $this->searchUsers($query);
        }
        
        // Flatten results and sort by type priority
        $allResults = collect($results)->flatten(1);
        
        // Sort by type priority: quizzes first, then subscription plans, then news, then users
        $typePriority = [
            'quiz' => 1,
            'subscription' => 2,
            'news' => 3,
            'user' => 4,
        ];
        
        return $allResults->sortBy(function ($item) use ($typePriority) {
            return $typePriority[$item['type']] ?? 5;
        })->values()->all();
    }
    
    public function searchQuizzes(string $query, ?string $userRole = null)
    {
        $queryBuilder = Quiz::query()
            ->where(function($q) use ($query) {
                $q->where('title->en', 'like', "%{$query}%")
                  ->orWhere('title->rw', 'like', "%{$query}%")
                  ->orWhere('description->en', 'like', "%{$query}%")
                  ->orWhere('description->rw', 'like', "%{$query}%");
                
                // Search in topics if it's a JSON column
                if (config('database.default') === 'pgsql') {
                    $q->orWhereRaw("topics::text ilike ?", ["%{$query}%"]);
                } else {
                    $q->orWhere('topics', 'like', "%{$query}%");
                }
            });
            
        // For non-admin users, only show active quizzes
        if ($userRole !== 'admin') {
            $queryBuilder->where('is_active', true);
        }
        
        $quizzes = $queryBuilder->withCount('questions')
            ->with('subscriptionPlan')
            ->latest()
            ->take(10)
            ->get();
            
        return $quizzes->map(function($quiz) use ($userRole) {
            return [
                'id' => $quiz->id,
                'title' => $quiz->getTranslation('title', 'en', true),
                'description' => $quiz->getTranslation('description', 'en', true) ?? 'No description',
                'type' => 'quiz',
                'url' => $userRole === 'admin' 
                    ? "/admin/quizzes/{$quiz->id}" 
                    : "/dashboard/quizzes/{$quiz->id}",
                'metadata' => [
                    'time_limit' => $quiz->time_limit_minutes,
                    'question_count' => $quiz->questions_count,
                    'is_active' => $quiz->is_active,
                    'is_guest_quiz' => $quiz->is_guest_quiz,
                    'subscription_plan' => $quiz->subscriptionPlan ? $quiz->subscriptionPlan->name : null,
                ],
            ];
        });
    }
    
    public function searchUsers(string $query)
    {
        $users = User::query()
            ->where('first_name', 'like', "%{$query}%")
            ->orWhere('last_name', 'like', "%{$query}%")
            ->orWhere('email', 'like', "%{$query}%")
            ->latest()
            ->take(10)
            ->get();
            
        return $users->map(function($user) {
            return [
                'id' => $user->id,
                'title' => "{$user->first_name} {$user->last_name}",
                'description' => $user->email,
                'type' => 'user',
                'url' => "/admin/users/{$user->id}",
                'metadata' => [
                    'role' => $user->role,
                    'created_at' => $user->created_at->toDateTimeString(),
                ],
            ];
        });
    }
    
    public function searchSubscriptionPlans(string $query)
    {
        $plans = SubscriptionPlan::query()
            ->where(function($q) use ($query) {
                $q->where('name->en', 'like', "%{$query}%")
                  ->orWhere('name->rw', 'like', "%{$query}%")
                  ->orWhere('description->en', 'like', "%{$query}%")
                  ->orWhere('description->rw', 'like', "%{$query}%");
            })
            ->where('is_active', true)
            ->orderBy('price')
            ->take(10)
            ->get();
            
        return $plans->map(function($plan) {
            return [
                'id' => $plan->id,
                'title' => $plan->getTranslation('name', 'en', true),
                'description' => $plan->getTranslation('description', 'en', true) ?? 'No description',
                'type' => 'subscription',
                'url' => "/admin/subscription-plans/{$plan->id}",
                'metadata' => [
                    'price' => $plan->price,
                    'duration' => $plan->duration,
                    'is_active' => $plan->is_active,
                    'features' => $plan->features,
                ],
            ];
        });
    }
    
    public function searchNews(string $query)
    {
        $news = News::query()
            ->where('title', 'like', "%{$query}%")
            ->orWhereJsonContains('content', ['en' => $query])
            ->orWhereJsonContains('content', ['rw' => $query])
            ->with('author')
            ->latest()
            ->take(10)
            ->get();
            
        return $news->map(function($item) {
            return [
                'id' => $item->id,
                'title' => $item->title,
                'description' => is_array($item->content) 
                    ? ($item->content['en'] ?? ($item->content['rw'] ?? 'No content'))
                    : (string) $item->content,
                'type' => 'news',
                'url' => "/news/{$item->slug}",
                'metadata' => [
                    'author' => $item->author ? "{$item->author->first_name} {$item->author->last_name}" : null,
                    'created_at' => $item->created_at->toDateTimeString(),
                    'images' => $item->images,
                ],
            ];
        });
    }
}
