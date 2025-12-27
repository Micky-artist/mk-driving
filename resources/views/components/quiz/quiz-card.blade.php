@php
    // Get the current locale
    $locale = app()->getLocale();

    // Handle both Quiz model objects and array data
    if (is_object($quiz)) {
        // Quiz model object (from dashboard)
        $title = $quiz->title;
        $description = $quiz->description ?? '';
        $questionsCount = $quiz->questions_count ?? 0;
        $quizId = $quiz->id;
        $isGuestQuiz = $quiz->is_guest_quiz ?? false;
        $timeLimitMinutes = $quiz->time_limit_minutes ?? 20;
    } else {
        // Array data (from homepage)
        $title = $quiz['title'] ?? 'Quiz Title';
        $description = $quiz['description'] ?? 'Quiz Description';
        $questionsCount = count($quiz['questions'] ?? []);
        $quizId = $quiz['id'];
        $isGuestQuiz = $quiz['is_guest_quiz'] ?? false;
        $timeLimitMinutes = $quiz['time_limit_minutes'] ?? 20;
    }
    $isFree = $isGuestQuiz; // Free quizzes are guest quizzes
    $planName = $isGuestQuiz ? __('dashboard.quizzes.free') : __('dashboard.quizzes.premium');

    // Format time
    $timeLimitMinutes = $quiz['time_limit_minutes'] ?? 20;
    $timeLimit = $timeLimitMinutes . ' ' . __('dashboard.quizzes.min');
    if ($timeLimitMinutes > 60) {
        $hours = floor($timeLimitMinutes / 60);
        $minutes = $timeLimitMinutes % 60;
        $timeLimit = $hours . 'h' . ($minutes > 0 ? ' ' . $minutes . 'm' : '');
    }

    // Determine if quiz is locked based on user authentication and subscription
    $isLocked = false;
    $user = auth()->user();
    $hasActiveSubscription = false;

    if (!$user) {
        // Guest users - only guest quizzes are accessible
        $isLocked = !$isGuestQuiz;
    } else {
        // Authenticated users - check subscription
        $hasActiveSubscription = $user->activeSubscriptions()->exists();
        if (!$hasActiveSubscription && !$isGuestQuiz) {
            $isLocked = true;
        }
    }

    // All features enabled - no separation between dashboard and homepage
    $showDashboardFeatures = true;
    $progressPercent = 0;
    $status = 'not_started';
    $score = 0;
    $attemptsCount = 0;
    $canRetake = true;
    $nextRetakeTime = null;

    if ($showDashboardFeatures && is_object($quiz) && isset($quiz->attempts) && $quiz->attempts->isNotEmpty()) {
        $attemptsCount = $quiz->attempts->count();
        $latestAttempt = $quiz->attempts->sortByDesc('created_at')->first();
        $score = $latestAttempt ? $latestAttempt->score : 0;

        // The score field is already a percentage (0-100), so use it directly
        $progressPercent = min(100, max(0, $score));

        // Determine status
        $status = $latestAttempt && $latestAttempt->completed_at ? 'completed' : 'in_progress';

        // Check retake restrictions
        if (!$hasActiveSubscription && $latestAttempt && $latestAttempt->completed_at) {
            $canRetake = $latestAttempt->completed_at->addHours(24)->isPast();
            if (!$canRetake) {
                $nextRetakeTime = $latestAttempt->completed_at->addHours(24);
            }
        }
    }

    // Determine status badge color based on score
    $statusColorClass = 'bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300';
    $statusDotClass = 'bg-gray-500';
    if ($attemptsCount > 0) {
        if ($progressPercent >= 60) {
            $statusColorClass = 'bg-green-100 dark:bg-green-900/20 text-green-800 dark:text-green-300';
            $statusDotClass = 'bg-green-500';
        } elseif ($progressPercent >= 40) {
            $statusColorClass = 'bg-yellow-100 dark:bg-yellow-900/20 text-yellow-800 dark:text-yellow-300';
            $statusDotClass = 'bg-yellow-500';
        } else {
            $statusColorClass = 'bg-red-100 dark:bg-red-900/20 text-red-800 dark:text-red-300';
            $statusDotClass = 'bg-red-500';
        }
    }

    // Define gradient based on score with 60% as passing threshold
    // Header stays blue, only progress bar and status badge are color-coded
    $gradient = 'from-blue-600 to-blue-700';

    // Determine button text and link based on user state
    $buttonText = __('dashboard.quizzes.start_quiz');
    $buttonLink = null;

    if (!$isLocked) {
        if ($user) {
            $buttonLink = route('dashboard.quizzes.show', ['locale' => $locale, 'quiz' => $quizId]);
            // Always check for attempts to determine button text
            if (is_object($quiz) && isset($quiz->attempts) && $quiz->attempts->isNotEmpty()) {
                $latestAttempt = $quiz->attempts->sortByDesc('created_at')->first();
                $status = $latestAttempt && $latestAttempt->completed_at ? 'completed' : 'in_progress';
                $buttonText =
                    $status === 'in_progress'
                        ? __('dashboard.quizzes.continue')
                        : __('dashboard.quizzes.practice_again');
            } else {
                $buttonText = __('dashboard.quizzes.start_quiz');
            }
        } else {
            // Guest user - redirect to guest quiz or login
            if ($isGuestQuiz) {
                $buttonLink = route('guest-quiz.show', ['locale' => $locale, 'quiz' => $quizId]);
            } else {
                $buttonLink = route('login', ['locale' => $locale]);
                $buttonText = __('navigation.login');
            }
        }
    }
@endphp

<div
    class="group relative bg-white dark:bg-slate-900 border-2 border-slate-200 dark:border-slate-800 hover:border-blue-500 dark:hover:border-blue-600 transition-all duration-300 rounded-2xl overflow-hidden h-full flex flex-col">
    @if ($isLocked)
        <!-- Lock Overlay -->
        <div
            class="absolute inset-0 bg-gradient-to-b from-blue-900/20 via-blue-900/10 to-white/90 dark:from-blue-100/20 dark:via-blue-100/10 dark:to-slate-900/90 z-20 flex flex-col items-center justify-end rounded-2xl">
            <!-- Lock Section -->
            <div class="w-full bg-white dark:bg-slate-800 rounded-xl p-4 shadow-2xl border border-slate-200 dark:border-slate-700 group-hover:border-blue-500 dark:group-hover:border-blue-600 transition-colors duration-300"
                style="box-shadow: 0 -4px 6px -1px rgba(0, 0, 0, 0.1), 0 -2px 4px -1px rgba(0, 0, 0, 0.06), 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);">
                <div class="text-center">
                    <div
                        class="w-12 h-12 mx-auto mb-3 bg-blue-600 dark:bg-blue-700 rounded-full flex items-center justify-center transition-transform duration-300 hover:scale-110">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                    </div>

                    <p class="text-sm text-slate-700 dark:text-slate-300 mb-3 font-medium">
                        {{ __('dashboard.quizzes.upgrade_to_access') }}
                    </p>

                    <a href="{{ route('plans', ['locale' => $locale]) }}"
                        class="block w-full text-center px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                        {{ __('dashboard.quizzes.upgrade_now') }}
                    </a>
                </div>
            </div>
        </div>
    @endif


    <!-- Dashboard Header for Authenticated Users -->
    @if ($showDashboardFeatures)
        <div class="bg-gradient-to-r {{ $gradient }} p-5 text-white flex-shrink-0">
            <div class="flex justify-between items-start gap-3 mb-4">
                <div class="flex-1 min-w-0">
                    <h3 class="text-lg font-bold truncate">{{ $title }}</h3>
                    @if (!empty($description))
                        <p class="text-sm text-white/90 mt-1 line-clamp-2">
                            {{ $description }}
                        </p>
                    @endif
                </div>
                <div class="bg-white/20 rounded-lg p-2 text-center flex-shrink-0" style="min-width: 80px;">
                    <div class="text-xs text-white/90 whitespace-nowrap">
                        {{ $attemptsCount > 0 ? __('dashboard.quizzes.score') : __('dashboard.quizzes.attempts') }}
                    </div>
                    <div class="text-2xl font-bold leading-tight mt-1">
                        @if ($attemptsCount > 0)
                            {{ round($progressPercent, 1) }}%
                        @else
                            0
                        @endif
                    </div>
                </div>
            </div>

            <div class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-white/20">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 mr-1" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                {{ $questionsCount }} {{ trans_choice('dashboard.questions', $questionsCount) }}
            </div>
        </div>
    @endif

    <!-- Content -->
    <div class="relative z-10 p-6 transition-all duration-300 flex-1 flex flex-col">
        <!-- Content Header -->
        <div class="flex justify-between items-start mb-4">
            <!-- Quiz Icon -->
            <div class="w-12 h-12 bg-blue-100 dark:bg-blue-950 rounded-lg flex items-center justify-center">
                <svg class="w-6 h-6 text-blue-700 dark:text-blue-400" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
            </div>

            <!-- Title -->
            <h3 class="text-xl font-bold text-slate-900 dark:text-white flex-1 ml-4">
                {{ $title }}
            </h3>
        </div>
        <!-- Progress Section for Dashboard Users -->
        @if ($showDashboardFeatures)
            <div
                class="p-5 bg-gradient-to-b from-gray-50 dark:from-gray-700 to-white dark:to-gray-800 border-b border-gray-100 dark:border-gray-700 flex-shrink-0">
                <div class="flex justify-between items-center text-sm mb-2">
                    <span
                        class="text-gray-600 dark:text-gray-400 font-medium">{{ __('dashboard.quizzes.progress') }}</span>
                    <span class="font-semibold text-gray-900 dark:text-white">{{ $progressPercent }}%</span>
                </div>
                <div class="w-full bg-gray-200 dark:bg-gray-600 rounded-full h-2.5 overflow-hidden">
                    <div class="bg-gradient-to-r {{ $gradient }} h-2.5 rounded-full transition-all duration-500 ease-out"
                        style="width: {{ $progressPercent }}%;"></div>
                </div>
            </div>

            <!-- Status Section -->
            <div class="p-2 bg-white dark:bg-gray-800 flex-shrink-0">
                <div class="flex justify-between items-center">
                    <div class="flex items-center text-sm text-gray-500 dark:text-gray-400">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 mr-1" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        {{ __('dashboard.time_limit', ['minutes' => $quiz->time_limit_minutes ?? 20]) }}
                    </div>
                    <span
                        class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-semibold flex-shrink-0 {{ $statusColorClass }}">
                        <span class="w-1.5 h-1.5 rounded-full mr-1.5 {{ $statusDotClass }}"></span>
                        @if ($status === 'completed')
                            {{ __('dashboard.quizzes.completed') }}
                        @elseif($status === 'in_progress')
                            {{ __('dashboard.quizzes.in_progress') }}
                        @else
                            {{ __('dashboard.quizzes.not_started') }}
                        @endif
                    </span>
                </div>

                <div class="mt-4">
                    <button type="button"
                        onclick="event.preventDefault(); @if ($canRetake || $hasActiveSubscription) window.location.href='{{ $buttonLink }}' @else showRetakeRestriction('{{ $nextRetakeTime ? $nextRetakeTime->diffForHumans() : '' }}') @endif"
                        class="w-full flex items-center justify-center px-4 py-2 border border-transparent rounded-lg text-sm font-medium text-white focus:outline-none focus:ring-2 focus:ring-offset-2 transition-colors duration-200 bg-blue-600 hover:bg-blue-700 focus:ring-blue-500 {{ !$canRetake && !$hasActiveSubscription ? 'opacity-75' : '' }}">
                        {{ $buttonText }}
                        <svg class="ml-2 -mr-1 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M14 5l7 7m0 0l-7 7m7-7H3" />
                        </svg>
                    </button>
                </div>
            </div>
        @else
            <!-- Regular content for non-dashboard users -->
            <!-- First Question Preview -->
            @if ($questionText)
                <div
                    class="mt-4 p-4 bg-slate-50 dark:bg-slate-800 rounded-lg border border-slate-200 dark:border-slate-700 flex-1">
                    <h4 class="text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                        {{ __('dashboard.quizzes.sample_question') }}:
                    </h4>
                    <p class="text-slate-800 dark:text-slate-200 text-sm">
                        @if (is_string($questionText))
                            {{ Str::limit(strip_tags($questionText), 120) }}
                        @else
                            {{ __('dashboard.quizzes.sample_question_text') }}
                        @endif
                    </p>
                </div>
            @else
                <p class="text-slate-600 dark:text-slate-400 text-sm mt-2 flex-1">
                    {{ Str::limit(strip_tags($description), 120) }}
                </p>
            @endif

            <!-- Action Button -->
            <div class="mt-6 pt-4 border-t border-slate-200 dark:border-slate-700">
                <a href="{{ $buttonLink }}"
                    class="block w-full text-center px-4 py-2.5 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white text-sm font-medium rounded-lg shadow-sm hover:shadow-md transition-all duration-200">
                    {{ $buttonText }}
                </a>
            </div>
        @endif
    </div>

</div>

@if ($showDashboardFeatures && !$canRetake && !$hasActiveSubscription)
    <script>
        function showRetakeRestriction(timeLeft) {
            if (typeof window.showRetakeRestrictionModal === 'function') {
                window.showRetakeRestrictionModal(timeLeft);
            } else {
                var messageTemplate = '{{ __('dashboard.quizzes.retake_restriction') }}';
                var defaultMessage = '{{ __('dashboard.quizzes.retake_restriction_default') }}';
                var message = timeLeft ? messageTemplate.replace(':time', timeLeft) : defaultMessage;
                alert(message);
            }
        }
    </script>
@endif
