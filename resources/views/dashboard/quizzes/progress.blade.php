@extends('layouts.app')

@section('title', __('dashboard.progress.title'))

@section('content')
    
    <div class="pt-16">    <div class="min-h-screen bg-gray-50 dark:bg-gray-900">
        <!-- Header -->
        <div class="bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="py-6">
                    <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">{{ __('dashboard.progress.title') }}</h1>
                    <p class="text-gray-600 dark:text-gray-400 mt-1">{{ __('dashboard.progress.subtitle') }}</p>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <main class="max-w-7xl mx-auto px-4 sm:px-2 py-6 space-y-6">
            <!-- Performance Overview Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Average Score Card -->
                <div class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm text-gray-600 dark:text-gray-400">{{ __('dashboard.progress.average_score') }}</span>
                        <div class="p-2 bg-purple-50 dark:bg-purple-900/20 rounded-lg">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-purple-600 dark:text-purple-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                        </div>
                    </div>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['average_score'] }}%</p>
                    <div class="mt-2">
                        @if($improvement['improvement_percentage'] > 0)
                            <span class="text-xs text-green-600 dark:text-green-400 font-medium">
                                ↑ {{ $improvement['improvement_percentage'] }}% {{ __('dashboard.progress.from_last_week') }}
                            </span>
                        @elseif($improvement['improvement_percentage'] < 0)
                            <span class="text-xs text-red-600 dark:text-red-400 font-medium">
                                ↓ {{ abs($improvement['improvement_percentage']) }}% {{ __('dashboard.progress.from_last_week') }}
                            </span>
                        @else
                            <span class="text-xs text-gray-500 dark:text-gray-400 font-medium">
                                → {{ __('dashboard.progress.no_change') }}
                            </span>
                        @endif
                    </div>
                </div>

                <!-- Completed Quizzes Card -->
                <div class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm text-gray-600 dark:text-gray-400">{{ __('dashboard.progress.completed_quizzes') }}</span>
                        <div class="p-2 bg-green-50 dark:bg-green-900/20 rounded-lg">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-600 dark:text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['completed_attempts'] }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                        {{ $stats['completion_rate'] }}% {{ __('dashboard.progress.completion_rate') }}
                    </p>
                </div>

                <!-- Current Streak Card -->
                <div class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm text-gray-600 dark:text-gray-400">{{ __('dashboard.progress.current_streak') }}</span>
                        <div class="p-2 bg-orange-50 dark:bg-orange-900/20 rounded-lg">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-orange-600 dark:text-orange-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 18.657A8 8 0 016.343 7.343S7 9 9 10c0-2 .5-5 2.986-7C14 5 16.09 5.777 17.656 7.343A7.975 7.975 0 0120 13a7.975 7.975 0 01-2.343 5.657z" />
                            </svg>
                        </div>
                    </div>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['current_streak'] }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                        {{ __('dashboard.progress.best_streak') }}: {{ $stats['best_streak'] }}
                    </p>
                </div>

                <!-- Leaderboard Position Card -->
                <div class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm text-gray-600 dark:text-gray-400">{{ __('dashboard.progress.leaderboard_position') }}</span>
                        <div class="p-2 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 01.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 01-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 01-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 01-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 01-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 01.806-1.946 3.42 3.42 0 013.138-3.138z" />
                            </svg>
                        </div>
                    </div>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">
                        {{ $userPosition <= 10 ? '#' . $userPosition : __('dashboard.progress.not_in_top_10') }}
                    </p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                        {{ __('dashboard.progress.out_of') }} {{ $leaderboard->count() }} {{ __('dashboard.progress.active_users') }}
                    </p>
                </div>
            </div>

            <!-- Encouragement Section -->
            <div class="bg-gradient-to-r from-blue-600 to-orange-600 rounded-xl p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-xl font-bold mb-2">{{ __('dashboard.progress.keep_going') }}</h2>
                        @if($stats['current_streak'] >= 7)
                            <p class="text-blue-100">{{ __('dashboard.progress.streak_achievement', ['days' => $stats['current_streak']]) }}</p>
                        @elseif($stats['average_score'] >= 80)
                            <p class="text-blue-100">{{ __('dashboard.progress.excellent_performance') }}</p>
                        @elseif($stats['completed_attempts'] >= 10)
                            <p class="text-blue-100">{{ __('dashboard.progress.dedicated_learner') }}</p>
                        @else
                            <p class="text-blue-100">{{ __('dashboard.progress.building_habits') }}</p>
                        @endif
                    </div>
                    <div class="text-center">
                        <div class="text-3xl font-bold">{{ $stats['current_streak'] }}</div>
                        <div class="text-sm text-blue-100">{{ __('dashboard.progress.day_streak') }}</div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Leaderboard -->
                <div class="lg:col-span-1">
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                        <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('dashboard.progress.leaderboard') }}</h2>
                        </div>
                        <div class="p-4 space-y-3">
                            @foreach($leaderboard as $index => $entry)
                                <div class="flex items-center justify-between p-3 {{ $entry['user']->id === $user->id ? 'bg-blue-50 dark:bg-blue-900/20 rounded-lg' : '' }}">
                                    <div class="flex items-center space-x-3">
                                        <div class="flex-shrink-0">
                                            @if($index === 0)
                                                <div class="w-8 h-8 bg-yellow-400 text-white rounded-full flex items-center justify-center text-sm font-bold">1</div>
                                            @elseif($index === 1)
                                                <div class="w-8 h-8 bg-gray-400 text-white rounded-full flex items-center justify-center text-sm font-bold">2</div>
                                            @elseif($index === 2)
                                                <div class="w-8 h-8 bg-orange-400 text-white rounded-full flex items-center justify-center text-sm font-bold">3</div>
                                            @else
                                                <div class="w-8 h-8 bg-gray-200 dark:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-full flex items-center justify-center text-sm font-bold">{{ $index + 1 }}</div>
                                            @endif
                                        </div>
                                        <div>
                                            <p class="font-medium text-gray-900 dark:text-white text-sm">
                                                {{ $entry['user']->first_name }} {{ $entry['user']->last_name }}
                                                @if($entry['user']->id === $user->id)
                                                    <span class="text-xs text-blue-600 dark:text-blue-400 ml-1">({{ __('dashboard.progress.you') }})</span>
                                                @endif
                                            </p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $entry['completed_quizzes'] }} {{ __('dashboard.progress.quizzes') }}</p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <p class="font-semibold text-gray-900 dark:text-white">{{ round($entry['average_score'], 1) }}%</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Performance by Category & Recent Activity -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Performance by Category -->
                    @if($performanceByCategory->count() > 0)
                        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                            <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('dashboard.progress.performance_by_category') }}</h2>
                            </div>
                            <div class="p-4 space-y-4">
                                @foreach($performanceByCategory as $category => $performance)
                                    <div>
                                        <div class="flex justify-between items-center mb-2">
                                            <h3 class="font-medium text-gray-900 dark:text-white text-sm truncate">{{ $category }}</h3>
                                            <span class="text-sm text-gray-600 dark:text-gray-400">{{ round($performance['average_score'], 1) }}%</span>
                                        </div>
                                        <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                            <div class="bg-gradient-to-r from-blue-500 to-orange-500 h-2 rounded-full" style="width: {{ $performance['average_score'] }}%"></div>
                                        </div>
                                        <div class="flex justify-between items-center mt-1 text-xs text-gray-500 dark:text-gray-400">
                                            <span>{{ $performance['attempts'] }} {{ __('dashboard.progress.attempts') }}</span>
                                            <span>{{ __('dashboard.progress.best') }}: {{ round($performance['best_score'], 1) }}%</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Recent Activity -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                        <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('dashboard.progress.recent_activity') }}</h2>
                        </div>
                        <div class="p-4 space-y-3">
                            @foreach($recentAttempts as $attempt)
                                <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                    <div class="flex-1 min-w-0">
                                        <h3 class="font-medium text-gray-900 dark:text-white truncate text-sm">{{ $attempt->quiz->title ?? 'Unknown Quiz' }}</h3>
                                        <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">
                                            {{ $attempt->created_at->format('M d, Y H:i') }}
                                            @if($attempt->completed_at)
                                                • {{ __('dashboard.progress.completed') }}
                                            @else
                                                • {{ __('dashboard.progress.in_progress') }}
                                            @endif
                                        </p>
                                    </div>
                                    <div class="ml-3 flex-shrink-0">
                                        @if($attempt->completed_at && $attempt->score)
                                            <span class="inline-flex items-center px-2 py-1 text-xs rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                                {{ round($attempt->score) }}%
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                                {{ __('dashboard.progress.active') }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
@endsection

@push('styles')
<style>
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
</style>
@endpush
