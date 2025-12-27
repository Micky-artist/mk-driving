@extends('layouts.app')

@section('title')
    {{ __('dashboard.title') }}
@endsection

@section('content')
    
    <div class="px-2 sm:px-6 lg:px-8 py-2">
        <!-- Subscription Info positioned behind main content -->
        @if ($currentSubscriptions->count() > 0)
            @php
                $nearestExpiry = $currentSubscriptions->min('ends_at');
                $currentPlan = $currentSubscriptions->first();
                $planName = is_string($currentPlan->plan->name)
                    ? json_decode($currentPlan->plan->name, true)
                    : $currentPlan->plan->name;
                $planDisplayName = $planName[app()->getLocale()] ?? ($planName['en'] ?? 'N/A');
                
                // Simple status badge
                $statusBadge = $currentPlan->status === 'ACTIVE' 
                    ? '<span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400">' . __('dashboard.active') . '</span>'
                    : '<span class="px-2 py-1 text-xs font-medium rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-400">' . __('dashboard.pending') . '</span>';
            @endphp
            <div class="relative -mb-4">
                <div class="bg-gradient-to-r from-blue-600 to-blue-700 rounded-xl p-4 text-white">
                    <!-- Mobile layout: row approach -->
                    <div class="flex flex-col gap-2 sm:hidden">
                        <div class="flex items-center justify-between">
                            <h2 class="text-base font-bold">{{ $planDisplayName }}</h2>
                            {!! $statusBadge !!}
                        </div>
                        <div class="flex items-center justify-between">
                            <p class="text-blue-100 text-xs">{{ __('dashboard.valid_until') }}</p>
                            @if ($nearestExpiry)
                                <p class="font-semibold text-sm">{{ $nearestExpiry->format('M d - g:i A') }}</p>
                            @else
                                <p class="font-semibold text-sm">{{ __('dashboard.no_end_date') }}</p>
                            @endif
                        </div>
                    </div>
                    
                    <!-- Desktop layout: column approach -->
                    <div class="hidden sm:flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                        <div>
                            <div class="flex items-center gap-2 mb-1">
                                <h2 class="text-lg font-bold">{{ $planDisplayName }}</h2>
                                {!! $statusBadge !!}
                            </div>
                            <p class="text-blue-100 text-sm">{{ __('dashboard.current_subscription') }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-blue-100 text-sm">{{ __('dashboard.valid_until') }}</p>
                            <p class="font-semibold">
                                @if ($nearestExpiry)
                                    {{ $nearestExpiry->format('M d, Y - g:i A') }}
                                @else
                                    {{ __('dashboard.no_end_date') }}
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Main Content Area that overlays the subscription section -->
        <div class="relative z-10 space-y-6">

        <!-- Current/Recently Opened Quiz -->
        @if ($inProgressQuizzes->count() > 0)
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('dashboard.continue_learning') }}</h2>
                        <a href="{{ route('dashboard.quizzes.index', ['locale' => app()->getLocale(), 'see' => 'in-progress']) }}" 
                           class="text-sm text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 font-medium">
                            {{ __('dashboard.quizzes.more_quizzes') }}
                        </a>
                    </div>
                </div>
                <div class="p-4">
                    @php
                        $currentQuiz = $inProgressQuizzes->first();
                        $progress = $currentQuiz->progress ?? 0;
                    @endphp
                    <div class="space-y-3">
                        <div>
                            <h3 class="font-medium text-gray-900 dark:text-white text-lg">{{ $currentQuiz->quiz->title }}</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                {{ __('dashboard.updated') }}: {{ timeDiffForHumans($currentQuiz->updated_at) }}
                            </p>
                        </div>
                        <div>
                            <div class="flex justify-between text-sm mb-1">
                                <span class="text-gray-600 dark:text-gray-400">{{ __('dashboard.quizzes.progress') }}</span>
                                <span class="font-medium text-gray-900 dark:text-white">{{ $progress }}%</span>
                            </div>
                            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                <div class="bg-blue-600 h-2 rounded-full transition-all duration-300" style="width: {{ $progress }}%"></div>
                            </div>
                        </div>
                        <a href="{{ route('dashboard.quizzes.show', ['locale' => app()->getLocale(), 'quiz' => $currentQuiz->quiz->id]) }}" 
                           class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors">
                            {{ __('dashboard.quizzes.resume') }}
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        @endif

        <!-- Quiz Stats -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <a href="{{ route('dashboard.quizzes.index', ['locale' => app()->getLocale(), 'see' => 'in-progress']) }}" 
               class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-700 hover:shadow-md transition-shadow cursor-pointer block">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm text-gray-600 dark:text-gray-400">{{ __('dashboard.inProgress') }}</span>
                    <div class="p-2 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
                <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['in_progress_count'] }}</p>
            </a>

            <a href="{{ route('dashboard.quizzes.index', ['locale' => app()->getLocale(), 'see' => 'completed']) }}" 
               class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-700 hover:shadow-md transition-shadow cursor-pointer block">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm text-gray-600 dark:text-gray-400">{{ __('dashboard.stats.completed') }}</span>
                    <div class="p-2 bg-green-50 dark:bg-green-900/20 rounded-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-600 dark:text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
                <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['completed_count'] }}</p>
            </a>

            <a href="{{ route('dashboard.quizzes.progress', ['locale' => app()->getLocale()]) }}" 
               class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-700 hover:shadow-md transition-shadow cursor-pointer block">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm text-gray-600 dark:text-gray-400">{{ __('dashboard.stats.average_score') }}</span>
                    <div class="p-2 bg-purple-50 dark:bg-purple-900/20 rounded-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-purple-600 dark:text-purple-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                    </div>
                </div>
                <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($stats['average_score'], 1) }}%</p>
            </a>

            <a href="{{ route('dashboard.quizzes.index', ['locale' => app()->getLocale()]) }}" 
               class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-700 hover:shadow-md transition-shadow cursor-pointer block">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm text-gray-600 dark:text-gray-400">{{ __('dashboard.availableQuizzes') }}</span>
                    <div class="p-2 bg-orange-50 dark:bg-orange-900/20 rounded-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-orange-600 dark:text-orange-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                </div>
                <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['total_quizzes'] }}</p>
            </a>
        </div>

        <!-- More Quizzes to Continue -->
        @if ($newQuizzes->count() > 0)
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('dashboard.quizzes.more_quizzes') }}</h2>
                        <a href="{{ route('dashboard.quizzes.index', ['locale' => app()->getLocale()]) }}" 
                           class="text-sm text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 font-medium">
                            {{ __('dashboard.quizzes.view_all') }}
                        </a>
                    </div>
                </div>
                <div class="p-4 space-y-3">
                    @foreach ($newQuizzes->take(3) as $quiz)
                        <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg border border-gray-200 dark:border-gray-600 hover:shadow-md transition-shadow cursor-pointer"
                             onclick="window.location.href='{{ route('dashboard.quizzes.show', ['locale' => app()->getLocale(), 'quiz' => $quiz->id]) }}'">
                            <div class="flex-1 min-w-0">
                                <h3 class="font-medium text-gray-900 dark:text-white truncate">{{ $quiz->title }}</h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ $quiz->questions_count }} {{ __('dashboard.questions') }}</p>
                            </div>
                            <div class="ml-3 flex-shrink-0">
                                <span class="inline-flex items-center px-2 py-1 text-xs rounded-full {{ $quiz->attempt_status === 'completed' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : ($quiz->attempt_status === 'in_progress' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200' : 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300') }}">
                                    {{ $quiz->attempt_status === 'completed' ? __('dashboard.completed') : ($quiz->attempt_status === 'in_progress' ? __('dashboard.in_progress') : __('dashboard.not_started')) }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Quiz History -->
        @if ($completedQuizzes->count() > 0)
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('dashboard.quizzes.history') }}</h2>
                        <a href="{{ route('dashboard.quizzes.index', ['locale' => app()->getLocale(), 'see' => 'completed']) }}" 
                           class="text-sm text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 font-medium">
                            {{ __('dashboard.quizzes.view_all') }}
                        </a>
                    </div>
                </div>
                <div class="p-4 space-y-3">
                    @foreach ($completedQuizzes->take(5) as $attempt)
                        @php
                            $quiz = $attempt->quiz;
                            $quizTitle = is_array($quiz->title)
                                ? $quiz->title[app()->getLocale()] ?? ($quiz->title['en'] ?? 'Untitled Quiz')
                                : $quiz->title;
                            
                            $totalQuestions = $attempt->total_questions > 0 ? $attempt->total_questions : ($quiz->questions_count ?? $quiz->questions->count() ?? 0);
                            $percentage = $attempt->score_percentage ?? 0;
                            $correctAnswers = $totalQuestions > 0 ? round(($percentage / 100) * $totalQuestions) : 0;
                        @endphp
                        <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg border border-gray-200 dark:border-gray-600">
                            <div class="flex-1 min-w-0">
                                <h3 class="font-medium text-gray-900 dark:text-white truncate">{{ $quizTitle }}</h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                    {{ __('dashboard.quizzes.score') }}: {{ $correctAnswers }}/{{ $totalQuestions }} ({{ $percentage }}%)
                                </p>
                            </div>
                            <div class="ml-3 flex-shrink-0">
                                <span class="inline-flex items-center px-2 py-1 text-xs rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                    {{ __('dashboard.completed') }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- No Subscription State -->
        @if ($currentSubscriptions->isEmpty())
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 text-center border border-gray-200 dark:border-gray-700">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">{{ __('subscription.no_subscription') }}</h3>
                <p class="text-gray-600 dark:text-gray-400 mb-4">{{ __('subscription.upgrade_message') }}</p>
                <a href="{{ route('plans', ['locale' => app()->getLocale()]) }}" 
                   class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors">
                    {{ __('dashboard.subscription.subscribe_now') }}
                </a>
            </div>
        @endif
    </div>
@endsection
