@extends('layouts.app')

@section('title')
    {{ __('dashboard.title') }}
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    @if(session('error'))
        alert('{{ session('error') }}');
    @endif
});
</script>
@endpush

@section('content')
    
    <!-- Welcome Modal for New Users -->
    @include('components.welcome-modal')
    
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
                        <a href="{{ route('dashboard.quizzes.index', ['locale' => app()->getLocale()]) }}" 
                           class="text-sm text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 font-medium">
                            {{ __('dashboard.quizzes.more_quizzes') }}
                        </a>
                    </div>
                </div>
                <div class="p-4 space-y-4">
                    @foreach ($inProgressQuizzes as $currentQuiz)
                        @php
                            // Calculate accurate progress using the answers JSON data
                            $answers = $currentQuiz->answers ?? [];
                            $answeredQuestions = count($answers);
                            $correctAnswers = 0;
                            
                            foreach ($answers as $questionId => $answer) {
                                if (is_array($answer) && isset($answer['is_correct']) && $answer['is_correct']) {
                                    $correctAnswers++;
                                }
                            }
                            
                            $totalQuestions = $currentQuiz->quiz->questions_count ?? $currentQuiz->quiz->questions->count() ?? 0;
                            
                            // Calculate progress percentage (questions answered / total questions)
                            $progress = $totalQuestions > 0 ? round(($answeredQuestions / $totalQuestions) * 100) : 0;
                            
                            // Calculate score percentage (correct answers / answered questions)
                            $scorePercentage = $answeredQuestions > 0 ? round(($correctAnswers / $answeredQuestions) * 100) : 0;
                            
                            // Determine progress bar color based on score percentage (same as unified-quiz-taker)
                            if ($answeredQuestions === 0) {
                                $progressBarColor = '#10b981'; // Default green when no answers
                            } else {
                                if ($scorePercentage >= 80) $progressBarColor = '#10b981'; // Green
                                elseif ($scorePercentage >= 60) $progressBarColor = '#84cc16'; // Light green  
                                elseif ($scorePercentage >= 40) $progressBarColor = '#eab308'; // Yellow
                                elseif ($scorePercentage >= 20) $progressBarColor = '#f97316'; // Orange
                                else $progressBarColor = '#ef4444'; // Red
                            }
                        @endphp
                        <div class="border border-gray-200 dark:border-gray-600 rounded-lg p-4">
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
                                        <span class="font-medium text-gray-900 dark:text-white">{{ $answeredQuestions }}/{{ $totalQuestions }} ({{ $progress }}%)</span>
                                    </div>
                                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                        <div class="h-2 rounded-full transition-all duration-300" 
                                             style="width: {{ $progress }}%; background-color: {{ $progressBarColor }};">
                                        </div>
                                    </div>
                                    @if ($answeredQuestions > 0)
                                        <div class="flex justify-between text-xs text-gray-500 dark:text-gray-400 mt-1">
                                            <span>{{ __('dashboard.quizzes.score') }}: {{ $correctAnswers }}/{{ $answeredQuestions }} ({{ $scorePercentage }}%)</span>
                                        </div>
                                    @endif
                                </div>
                                <a href="{{ route('dashboard.quizzes.take', ['locale' => app()->getLocale(), 'quiz' => $currentQuiz->quiz->id, 'attempt' => $currentQuiz->id]) }}" 
                                   class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors">
                                    {{ __('dashboard.quizzes.resume') }}
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                    </svg>
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Permanent Practice Bar -->
        <div class="bg-gray-100 dark:bg-gray-700 rounded-xl shadow-lg p-6 text-gray-900 dark:text-white border border-gray-200 dark:border-gray-600">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="flex-1">
                    <h2 class="text-xl font-bold mb-2">{{ __('dashboard.practice_bar.title') }}</h2>
                    <p class="text-gray-600 dark:text-gray-300 text-sm sm:text-base">{{ __('dashboard.practice_bar.subtitle') }}</p>
                    <div class="mt-3 flex items-center gap-4">
                        <div class="flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <span class="font-medium">{{ __('dashboard.availableQuizzes') }}: {{ $stats['total_quizzes'] }}</span>
                        </div>
                        @if($stats['completed_count'] > 0)
                        <div class="flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span class="font-medium">{{ __('dashboard.completed') }}: {{ $stats['completed_count'] }}</span>
                        </div>
                        @endif
                    </div>
                </div>
                <div class="flex-shrink-0">
                    <a href="{{ route('dashboard.quizzes.index', ['locale' => app()->getLocale()]) }}" 
                       class="inline-flex items-center px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold text-lg rounded-lg transition-colors shadow-md">
                        {{ __('dashboard.practice_bar.start_practicing') }}
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                        </svg>
                    </a>
                </div>
            </div>
        </div>

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
                <div class="mt-3 pt-3 border-t border-gray-200 dark:border-gray-700">
                    <div class="inline-flex items-center text-sm font-medium text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 transition-colors">
                        {{ __('dashboard.quizzes.view_in_progress') }}
                        <svg class="ml-1.5 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                        </svg>
                    </div>
                </div>
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
                <div class="mt-3 pt-3 border-t border-gray-200 dark:border-gray-700">
                    <div class="inline-flex items-center text-sm font-medium text-green-600 dark:text-green-400 hover:text-green-800 dark:hover:text-green-300 transition-colors">
                        {{ __('dashboard.quizzes.view_completed') }}
                        <svg class="ml-1.5 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                        </svg>
                    </div>
                </div>
            </a>

            <a href="{{ route('dashboard.progress', ['locale' => app()->getLocale()]) }}" 
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
                <div class="mt-3 pt-3 border-t border-gray-200 dark:border-gray-700">
                    <div class="inline-flex items-center text-sm font-medium text-purple-600 dark:text-purple-400 hover:text-purple-800 dark:hover:text-purple-300 transition-colors">
                        {{ __('dashboard.quizzes.view_progress') }}
                        <svg class="ml-1.5 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                        </svg>
                    </div>
                </div>
            </a>

            <div class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-700 hover:shadow-md transition-shadow cursor-pointer block">
                <a href="{{ route('dashboard.quizzes.index', ['locale' => app()->getLocale()]) }}" class="block">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm text-gray-600 dark:text-gray-400">{{ __('dashboard.availableQuizzes') }}</span>
                        <div class="p-2 bg-orange-50 dark:bg-orange-900/20 rounded-lg">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-orange-600 dark:text-orange-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                    </div>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['total_quizzes'] }}</p>
                    <div class="mt-3 pt-3 border-t border-gray-200 dark:border-gray-700">
                        <div class="inline-flex items-center text-sm font-medium text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 transition-colors">
                            {{ __('dashboard.quizzes.take_quiz') }}
                            <svg class="ml-1.5 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                            </svg>
                        </div>
                    </div>
                </a>
            </div>
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

        <!-- User Progress Line Graph -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('dashboard.progress.title') }}</h2>
                    <div class="flex items-center gap-2">
                        @if($readinessData['is_ready'])
                            <span class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400">
                                {{ __('dashboard.readiness.ready') }}
                            </span>
                        @elseif($readinessData['getting_ready'])
                            <span class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-400">
                                {{ __('dashboard.readiness.getting_ready') }}
                            </span>
                        @else
                            <span class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400">
                                {{ __('dashboard.readiness.keep_practicing') }}
                            </span>
                        @endif
                        <span class="text-sm font-medium text-gray-900 dark:text-white">
                            {{ __('dashboard.readiness.percentage', ['percentage' => $readinessData['percentage']]) }}
                        </span>
                    </div>
                </div>
            </div>
            <div class="p-4">
                @if($progressData['has_data'])
                    <div class="relative h-64 w-full">
                        <canvas id="progressChart"></canvas>
                    </div>
                    <div class="mt-4 grid grid-cols-2 sm:grid-cols-4 gap-4 text-center">
                        <div>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $readinessData['percentage'] }}%</p>
                            <p class="text-xs text-gray-600 dark:text-gray-400">{{ __('dashboard.readiness.readiness') }}</p>
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $readinessData['average_score'] }}%</p>
                            <p class="text-xs text-gray-600 dark:text-gray-400">{{ __('dashboard.readiness.average_score', ['score' => $readinessData['average_score']]) }}</p>
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $readinessData['total_tests'] }}</p>
                            <p class="text-xs text-gray-600 dark:text-gray-400">{{ __('dashboard.readiness.tests_completed', ['count' => $readinessData['total_tests']]) }}</p>
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ count($progressData['scores']) }}</p>
                            <p class="text-xs text-gray-600 dark:text-gray-400">{{ __('dashboard.progress.recent_quizzes') }}</p>
                        </div>
                    </div>
                @else
                    <div class="text-center py-8">
                        <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">{{ __('dashboard.progress.no_data') }}</h3>
                        <p class="text-gray-600 dark:text-gray-400 mb-4">{{ __('dashboard.progress.start_quizzes') }}</p>
                        <a href="{{ route('dashboard.quizzes.index', ['locale' => app()->getLocale()]) }}" 
                           class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors">
                            {{ __('dashboard.readiness.see_more_quizzes') }}
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <!-- Quiz History -->
        @if ($completedQuizzes->count() > 0 || $inProgressQuizzes->count() > 0)
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('dashboard.quizzes.history') }}</h2>
                        <a href="{{ route('dashboard.quizzes.index', ['locale' => app()->getLocale()]) }}" 
                           class="text-sm text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 font-medium">
                            {{ __('dashboard.quizzes.view_all') }}
                        </a>
                    </div>
                </div>
                <div class="p-4 space-y-3">
                    {{-- Show in-progress quizzes first --}}
                    @foreach ($inProgressQuizzes->take(3) as $attempt)
                        @php
                            $quiz = $attempt->quiz;
                            $quizTitle = is_array($quiz->title)
                                ? $quiz->title[app()->getLocale()] ?? ($quiz->title['en'] ?? 'Untitled Quiz')
                                : $quiz->title;
                            
                            // Calculate score based on answered questions using answers JSON
                            $answers = $attempt->answers ?? [];
                            $answeredQuestions = count($answers);
                            $correctAnswers = 0;
                            
                            foreach ($answers as $questionId => $answer) {
                                if (is_array($answer) && isset($answer['is_correct']) && $answer['is_correct']) {
                                    $correctAnswers++;
                                }
                            }
                            
                            $totalQuestions = $quiz->questions_count ?? $quiz->questions->count() ?? 0;
                            $percentage = $answeredQuestions > 0 ? round(($correctAnswers / $answeredQuestions) * 100) : 0;
                        @endphp
                        <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg border border-gray-200 dark:border-gray-600 hover:shadow-md transition-shadow cursor-pointer"
                             onclick="window.location.href='{{ route('dashboard.quizzes.take', ['locale' => app()->getLocale(), 'quiz' => $quiz->id, 'attempt' => $attempt->id]) }}'">
                            <div class="flex-1 min-w-0">
                                <h3 class="font-medium text-gray-900 dark:text-white truncate">{{ $quizTitle }}</h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                    {{ __('dashboard.quizzes.score') }}: {{ $correctAnswers }}/{{ $answeredQuestions }} ({{ $percentage }}%) • 
                                    {{ __('dashboard.in_progress') }}: {{ $answeredQuestions }}/{{ $totalQuestions }} {{ __('dashboard.questions') }}
                                </p>
                            </div>
                            <div class="ml-3 flex-shrink-0">
                                <span class="inline-flex items-center px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                                    {{ __('dashboard.in_progress') }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                    
                    {{-- Show completed quizzes --}}
                    @foreach ($completedQuizzes->take(2) as $attempt)
                        @php
                            $quiz = $attempt->quiz;
                            $quizTitle = is_array($quiz->title)
                                ? $quiz->title[app()->getLocale()] ?? ($quiz->title['en'] ?? 'Untitled Quiz')
                                : $quiz->title;
                            
                            // Get accurate score from the attempt
                            $score = $attempt->score ?? 0;
                            $totalQuestions = $attempt->total_questions > 0 ? $attempt->total_questions : ($quiz->questions_count ?? $quiz->questions->count() ?? 0);
                            
                            // Calculate correct answers based on score (score is already a percentage)
                            $correctAnswers = $totalQuestions > 0 ? round(($score / 100) * $totalQuestions) : 0;
                            
                            // Check if user can retake this quiz
                            $user = auth()->user();
                            $canRetake = true;
                            $nextRetakeTime = null;
                            
                            if (!$user->isAdmin()) {
                                $hasActiveSubscription = $user->activeSubscriptions()->exists();
                                $hasRequiredPlan = false;
                                
                                if ($quiz->subscription_plan_slug) {
                                    $hasRequiredPlan = $user->activeSubscriptions()
                                        ->whereHas('plan', function($query) use ($quiz) {
                                            $query->where('slug', $quiz->subscription_plan_slug);
                                        })
                                        ->exists();
                                }
                                
                                // Check retake restrictions for non-admin users
                                if (!$hasRequiredPlan && !$quiz->is_guest_quiz && $attempt->completed_at) {
                                    $canRetake = $attempt->completed_at->addHours(24)->isPast();
                                    if (!$canRetake) {
                                        $nextRetakeTime = $attempt->completed_at->addHours(24);
                                    }
                                }
                            }
                        @endphp
                        <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg border border-gray-200 dark:border-gray-600 hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors cursor-pointer group"
                             onclick="window.location.href='{{ route('dashboard.quizzes.show', ['locale' => app()->getLocale(), 'quiz' => $quiz->id]) }}'">
                            <div class="flex-1 min-w-0">
                                <h3 class="font-medium text-gray-900 dark:text-white truncate group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">
                                    {{ $quizTitle }}
                                </h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                    {{ __('dashboard.quizzes.score') }}: {{ $correctAnswers }}/{{ $totalQuestions }} ({{ round($score) }}%)
                                </p>
                                @if (!$canRetake && $nextRetakeTime)
                                    <p class="text-xs text-orange-600 dark:text-orange-400 mt-1">
                                        {{ __('dashboard.quizzes.can_retry_in', ['time' => $nextRetakeTime->diffForHumans()]) }}
                                    </p>
                                @endif
                            </div>
                            <div class="ml-3 flex-shrink-0 flex items-center space-x-2">
                                <span class="inline-flex items-center px-2 py-1 text-xs rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                    {{ __('dashboard.completed') }}
                                </span>
                                @if ($canRetake)
                                    <span class="inline-flex items-center px-2.5 py-1 text-xs font-medium rounded-full bg-gradient-to-r from-blue-500 to-blue-600 text-white dark:from-blue-600 dark:to-blue-700 transition-all duration-200 shadow-md hover:shadow-lg transform hover:scale-105">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                        </svg>
                                        {{ __('dashboard.quizzes.try_again') }}
                                    </span>
                                @endif
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

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Progress Chart
    @if($progressData['has_data'])
        const ctx = document.getElementById('progressChart').getContext('2d');
        const progressChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: @json($progressData['labels']),
                datasets: [{
                    label: '{{ __("dashboard.progress.quiz_scores") }}',
                    data: @json($progressData['scores']),
                    borderColor: 'rgb(59, 130, 246)',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    tension: 0.4,
                    fill: true,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    pointBackgroundColor: 'rgb(59, 130, 246)',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2
                }, {
                    label: '{{ __("dashboard.progress.cumulative_average") }}',
                    data: @json($progressData['averages']),
                    borderColor: 'rgb(168, 85, 247)',
                    backgroundColor: 'rgba(168, 85, 247, 0.1)',
                    tension: 0.4,
                    fill: true,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    pointBackgroundColor: 'rgb(168, 85, 247)',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        labels: {
                            usePointStyle: true,
                            padding: 20,
                            font: {
                                size: 12
                            }
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        titleColor: '#fff',
                        bodyColor: '#fff',
                        borderColor: 'rgb(59, 130, 246)',
                        borderWidth: 1,
                        padding: 12,
                        displayColors: true,
                        callbacks: {
                            title: function(context) {
                                const index = context[0].dataIndex;
                                return @json($progressData['quiz_titles'])[index] || context[0].label;
                            },
                            label: function(context) {
                                return context.dataset.label + ': ' + context.parsed.y + '%';
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100,
                        ticks: {
                            callback: function(value) {
                                return value + '%';
                            },
                            font: {
                                size: 11
                            }
                        },
                        grid: {
                            color: 'rgba(156, 163, 175, 0.1)'
                        }
                    },
                    x: {
                        ticks: {
                            font: {
                                size: 11
                            }
                        },
                        grid: {
                            color: 'rgba(156, 163, 175, 0.1)'
                        }
                    }
                },
                interaction: {
                    intersect: false,
                    mode: 'index'
                }
            }
        });
    @endif
});
</script>
@endpush
@endsection
