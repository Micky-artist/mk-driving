@php
    $locale = app()->getLocale();
    
    // Extract quiz data (handle both objects and arrays)
    $title = is_object($quiz) ? $quiz->title : ($quiz['title'] ?? 'Quiz Title');
    $description = is_object($quiz) ? ($quiz->description ?? '') : ($quiz['description'] ?? '');
    $questionsCount = is_object($quiz) ? ($quiz->questions_count ?? 0) : count($quiz['questions'] ?? []);
    $quizId = is_object($quiz) ? $quiz->id : $quiz['id'];
    $isGuestQuiz = is_object($quiz) ? ($quiz->is_guest_quiz ?? false) : ($quiz['is_guest_quiz'] ?? false);
    
    // Smart button and link logic
    $user = auth()->user();
    $buttonText = __('dashboard.quizzes.start_quiz');
    $buttonLink = null;
    $isLocked = false;
    
    if (!$user) {
        // Guest users
        $isLocked = !$isGuestQuiz;
        if ($isGuestQuiz) {
            $buttonLink = route('guest-quiz.show', ['locale' => $locale, 'quiz' => $quizId]);
        } else {
            $buttonLink = route('login', ['locale' => $locale]);
            $buttonText = __('navigation.login');
        }
    } else {
        // Authenticated users - check if quiz requires specific plan
        $requiredPlanSlug = is_object($quiz) ? ($quiz->subscription_plan_slug ?? null) : ($quiz['subscription_plan_slug'] ?? null);
        
        if ($requiredPlanSlug && !$isGuestQuiz) {
            // Complex subscription check - delegate to controller or set locked state
            $isLocked = true;
            $buttonLink = route('plans', ['locale' => $locale]);
            $buttonText = __('dashboard.quizzes.upgrade_now');
        } else {
            // Free quiz - always accessible
            $buttonLink = route('dashboard.quizzes.show', ['locale' => $locale, 'quiz' => $quizId]);
            
            // Smart button text based on attempts
            if (is_object($quiz) && isset($quiz->attempts) && $quiz->attempts->isNotEmpty()) {
                $latestAttempt = $quiz->attempts->sortByDesc('created_at')->first();
                if ($latestAttempt && !$latestAttempt->completed_at) {
                    $buttonText = __('dashboard.quizzes.continue');
                } else {
                    $buttonText = __('dashboard.quizzes.practice_again');
                }
            }
        }
    }
    
    // Progress and attempt data
    $progressPercent = 0;
    $attemptsCount = 0;
    $status = 'not_started';
    
    if (is_object($quiz) && isset($quiz->attempts) && $quiz->attempts->isNotEmpty()) {
        $attemptsCount = $quiz->attempts->count();
        $latestAttempt = $quiz->attempts->sortByDesc('created_at')->first();
        
        // Calculate best score for display
        $bestCompletedAttempt = $quiz->attempts
            ->where('status', 'COMPLETED')
            ->sortByDesc('score')
            ->first();
        
        if ($bestCompletedAttempt) {
            $progressPercent = $bestCompletedAttempt->score;
        } elseif ($latestAttempt) {
            $progressPercent = $latestAttempt->score;
        }
        
        $status = $latestAttempt && $latestAttempt->completed_at ? 'completed' : 'in_progress';
    }
    
    // Status colors
    $statusColor = match($status) {
        'completed' => 'bg-green-100 text-green-800',
        'in_progress' => 'bg-yellow-100 text-yellow-800',
        default => 'bg-gray-100 text-gray-800'
    };
@endphp

<div class="group relative border border-slate-200 dark:border-slate-800 hover:border-blue-500 dark:hover:border-blue-600 transition-all duration-300 rounded-xl overflow-hidden h-full flex flex-col">
    <!-- Header -->
    <div class="p-4 border-b border-slate-200 dark:border-slate-700">
        <h3 class="text-lg font-semibold text-slate-900 dark:text-white mb-3">{{ $title }}</h3>
        
        @if (!empty($description))
            <p class="text-sm text-slate-600 dark:text-slate-400 line-clamp-2">{{ $description }}</p>
        @endif
    </div>

    <!-- Content -->
    <div class="p-4 flex-1 flex flex-col">
        <!-- Progress Section (priority over question count) -->
        @if ($attemptsCount > 0)
            <div class="mb-4">
                <div class="flex justify-between items-center text-sm mb-2">
                    <span class="text-slate-600 dark:text-slate-400">{{ __('dashboard.quizzes.progress') }}</span>
                    <span class="font-semibold text-slate-900 dark:text-white">{{ round($progressPercent) }}%</span>
                </div>
                <div class="w-full bg-slate-200 dark:bg-slate-600 rounded-full h-2">
                    <div class="bg-blue-600 h-2 rounded-full transition-all duration-300" style="width: {{ $progressPercent }}%"></div>
                </div>
            </div>
            
            <!-- Status and attempts info -->
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center text-sm text-slate-500">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                    </svg>
                    {{ $attemptsCount }} {{ __('dashboard.quizzes.attempts') }}
                </div>
                
                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $statusColor }}">
                    {{ $status === 'completed' ? __('dashboard.quizzes.completed') : ($status === 'in_progress' ? __('dashboard.quizzes.in_progress') : __('dashboard.quizzes.not_started')) }}
                </span>
            </div>
        @else
            <!-- First-time user info - no question count needed since all are 20 -->
        @endif

        <!-- Smart Action Button -->
        <div class="mt-auto">
            @if ($isLocked)
                <!-- Subscribe CTA for locked quizzes -->
                <a href="{{ route('plans', ['locale' => $locale]) }}" class="flex items-center justify-center w-full px-4 py-2.5 bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 text-white text-sm font-medium rounded-lg transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                    {{ __('dashboard.quizzes.upgrade_now') }}
                </a>
            @elseif ($buttonLink)
                <!-- Regular action button -->
                <a href="{{ $buttonLink }}" class="block w-full text-center px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors">
                    {{ $buttonText }}
                    <svg class="inline-block ml-2 -mr-1 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                    </svg>
                </a>
            @endif
        </div>
    </div>
</div>
