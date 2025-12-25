@extends('admin.layouts.app')

@section('title', 'Recent User Activity')

@push('styles')
<style>
    .activity-card {
        /* Using pure Tailwind classes instead of custom CSS */
    }
    
    .activity-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 2px;
        background: linear-gradient(90deg, #3b82f6, #8b5cf6, #ec4899);
        opacity: 0.3;
    }
    
    .activity-item {
        @apply border-l-4 border-transparent transition-all duration-200;
    }
    
    .activity-item:hover {
        @apply bg-gray-50 dark:bg-gray-700 border-l-blue-500;
    }
    
    .activity-icon-quiz {
        @apply bg-blue-100 text-blue-600 dark:bg-blue-900 dark:text-blue-300;
    }
    
    .activity-icon-login {
        @apply bg-green-100 text-green-600 dark:bg-green-900 dark:text-green-300;
    }
    
    .activity-icon-forum {
        @apply bg-purple-100 text-purple-600 dark:bg-purple-900 dark:text-purple-300;
    }
    
    .activity-icon-subscription {
        @apply bg-orange-100 text-orange-600 dark:bg-orange-900 dark:text-orange-300;
    }
    
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    .fade-in {
        animation: fadeIn 0.6s ease-out forwards;
    }
    
    .fade-in-delay-1 { animation-delay: 0.1s; }
    .fade-in-delay-2 { animation-delay: 0.2s; }
    .fade-in-delay-3 { animation-delay: 0.3s; }
</style>
@endpush

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="fade-in">
        <div class="sm:flex sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Recent User Activity</h1>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    Monitor and analyze user engagement patterns and recent activities
                </p>
            </div>
            <div class="mt-4 sm:mt-0">
                <form method="GET" action="{{ route('admin.users.recent-activity') }}" class="flex space-x-2">
                    <select name="activity_type" class="rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
                        <option value="">All Activities</option>
                        <option value="quiz" {{ request('activity_type') == 'quiz' ? 'selected' : '' }}>Quiz Attempts</option>
                        <option value="login" {{ request('activity_type') == 'login' ? 'selected' : '' }}>Logins</option>
                        <option value="forum" {{ request('activity_type') == 'forum' ? 'selected' : '' }}>Forum Activity</option>
                        <option value="subscription" {{ request('activity_type') == 'subscription' ? 'selected' : '' }}>Subscriptions</option>
                    </select>
                    <select name="timeframe" class="rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
                        <option value="24h" {{ request('timeframe') == '24h' ? 'selected' : '' }}>Last 24 Hours</option>
                        <option value="7d" {{ request('timeframe') == '7d' ? 'selected' : '' }}>Last 7 Days</option>
                        <option value="30d" {{ request('timeframe') == '30d' ? 'selected' : '' }}>Last 30 Days</option>
                        <option value="all" {{ request('timeframe') == 'all' ? 'selected' : '' }}>All Time</option>
                    </select>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                        Filter
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Activity Statistics -->
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4 fade-in-delay-1">
        <!-- Total Activities -->
        <div class="activity-card bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg relative">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-blue-500 rounded-md flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Total Activities</dt>
                            <dd class="text-lg font-semibold text-gray-900 dark:text-white">{{ number_format($activityStats['total_activities'] ?? 0) }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- Active Users Today -->
        <div class="activity-card bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg relative">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-green-500 rounded-md flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Active Today</dt>
                            <dd class="text-lg font-semibold text-gray-900 dark:text-white">{{ number_format($activityStats['active_today'] ?? 0) }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quiz Attempts -->
        <div class="activity-card bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg relative">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-purple-500 rounded-md flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Quiz Attempts</dt>
                            <dd class="text-lg font-semibold text-gray-900 dark:text-white">{{ number_format($activityStats['quiz_attempts'] ?? 0) }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- New Registrations -->
        <div class="activity-card bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg relative">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-orange-500 rounded-md flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">New Registrations</dt>
                            <dd class="text-lg font-semibold text-gray-900 dark:text-white">{{ number_format($activityStats['new_registrations'] ?? 0) }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- Subscriptions -->
        <div class="activity-card bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg relative">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-orange-600 rounded-md flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Subscriptions</dt>
                            <dd class="text-lg font-semibold text-gray-900 dark:text-white">{{ number_format($activityStats['subscriptions'] ?? 0) }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activities Timeline -->
    <div class="bg-white dark:bg-gray-800 shadow rounded-lg activity-card relative fade-in-delay-2">
        <div class="px-4 py-5 sm:p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white">
                    Recent Activity Timeline
                </h3>
                <div class="text-sm text-gray-500 dark:text-gray-400">
                    Showing {{ $activities->firstItem() ?? 0 }} to {{ $activities->lastItem() ?? 0 }} of {{ $activities->total() }} activities
                </div>
            </div>

            @if($activities->count() > 0)
                <div class="space-y-4">
                    @foreach($activities as $activity)
                        <div class="activity-item pl-4 pr-4 py-3 rounded-lg">
                            <div class="flex items-start space-x-3">
                                <div class="flex-shrink-0">
                                    @switch($activity->type)
                                        @case('quiz_attempt')
                                            <div class="w-8 h-8 rounded-full activity-icon-quiz flex items-center justify-center">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                            </div>
                                            @break
                                        @case('login')
                                            <div class="w-8 h-8 rounded-full activity-icon-login flex items-center justify-center">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                                                </svg>
                                            </div>
                                            @break
                                        @case('forum_post')
                                        @case('forum_answer')
                                            <div class="w-8 h-8 rounded-full activity-icon-forum flex items-center justify-center">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                                                </svg>
                                            </div>
                                            @break
                                        @case('subscription')
                                            <div class="w-8 h-8 rounded-full activity-icon-subscription flex items-center justify-center">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                                                </svg>
                                            </div>
                                            @break
                                        @default
                                            <div class="w-8 h-8 rounded-full bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300 flex items-center justify-center">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                            </div>
                                    @endswitch
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center justify-between">
                                        <p class="text-sm font-medium text-gray-900 dark:text-white">
                                            @switch($activity->type)
                                                @case('quiz_attempt')
                                                    Quiz Attempt: {{ $activity->data['quiz_title'] ?? 'Unknown Quiz' }}
                                                    @break
                                                @case('login')
                                                    User Login
                                                    @break
                                                @case('forum_post')
                                                    Forum Post: {{ $activity->data['title'] ?? 'New Post' }}
                                                    @break
                                                @case('forum_answer')
                                                    Forum Answer
                                                    @break
                                                @case('subscription')
                                                    @switch($activity->data['action'] ?? 'created')
                                                        @case('created')
                                                            Subscription Created: {{ $activity->data['plan_name'] ?? 'Unknown Plan' }}
                                                            @break
                                                        @case('approved')
                                                            Subscription Approved: {{ $activity->data['plan_name'] ?? 'Unknown Plan' }}
                                                            @break
                                                        @case('rejected')
                                                            Subscription Rejected: {{ $activity->data['plan_name'] ?? 'Unknown Plan' }}
                                                            @break
                                                        @default
                                                            Subscription: {{ $activity->data['plan_name'] ?? 'Unknown Plan' }}
                                                    @endswitch
                                                    @break
                                                @default
                                                    {{ $activity->type }}
                                            @endswitch
                                        </p>
                                        <span class="text-xs text-gray-500 dark:text-gray-400">
                                            {{ $activity->created_at->diffForHumans() }}
                                        </span>
                                    </div>
                                    <div class="mt-1">
                                        <p class="text-sm text-gray-600 dark:text-gray-400">
                                            by <a href="{{ route('admin.users.show', $activity->user_id) }}" class="font-medium text-blue-600 hover:text-blue-500">
                                                {{ $activity->user->first_name }} {{ $activity->user->last_name }}
                                            </a>
                                            @if($activity->data['score'] ?? null)
                                                - Score: {{ $activity->data['score'] }}/{{ $activity->data['total_questions'] ?? '?' }}
                                            @endif
                                            @if($activity->type === 'subscription' && ($activity->data['amount'] ?? null))
                                                - Amount: {{ number_format($activity->data['amount']) }} RWF
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="mt-6">
                    {{ $activities->links() }}
                </div>
            @else
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No activity found</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        No user activity recorded yet.
                    </p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
