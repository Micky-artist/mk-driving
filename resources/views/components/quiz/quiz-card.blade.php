@php
    // Get the current locale
    $locale = app()->getLocale();
    
    // Get quiz details with fallback for localization
    $title = $quiz->getTranslation('title', $locale, false) ?: $quiz->title;
    $description = $quiz->getTranslation('description', $locale, false) ?: $quiz->description;
    $questionsCount = $quiz->questions_count ?? $quiz->questions()->count();
    
    // Get the first question if available
    $firstQuestion = $quiz->questions->first();
    $questionText = $firstQuestion ? 
        ($firstQuestion->getTranslation('text', $locale, false) ?: $firstQuestion->text) : 
        null;
    
    // Plan information
    $isFree = !$quiz->subscription_plan_slug;
    $plan = $quiz->subscriptionPlan;
    $planName = $isFree ? __('dashboard.quizzes.free') : ($plan->name ?? __('dashboard.quizzes.premium'));
    $planNameString = is_array($planName) ? ($planName[$locale] ?? $planName['en'] ?? 'PREMIUM') : $planName;
    
    // Format time
    $timeLimit = $quiz->time_limit_minutes . ' ' . __('dashboard.quizzes.min');
    if ($quiz->time_limit_minutes > 60) {
        $hours = floor($quiz->time_limit_minutes / 60);
        $minutes = $quiz->time_limit_minutes % 60;
        $timeLimit = $hours . 'h' . ($minutes > 0 ? ' ' . $minutes . 'm' : '');
    }
@endphp

<div class="group relative bg-white dark:bg-slate-900 border-2 border-slate-200 dark:border-slate-800 hover:border-blue-500 dark:hover:border-blue-600 transition-all duration-300 rounded-2xl overflow-hidden h-full flex flex-col">
    @if($quiz->is_locked)
        <!-- Lock Overlay -->
        <div class="absolute inset-0 bg-gradient-to-b from-white/0 via-white/80 to-white/95 dark:from-slate-900/0 dark:via-slate-900/80 dark:to-slate-900/95 z-20 flex flex-col items-center justify-end p-6 rounded-2xl">
            <div class="absolute inset-0 bg-gradient-to-b from-blue-50/30 via-blue-100/40 to-blue-100/80 dark:from-blue-900/10 dark:via-blue-900/20 dark:to-blue-900/40 backdrop-blur-sm rounded-2xl"></div>
            <div class="text-center relative z-10">
                <!-- Lock Icon -->
                <div class="w-12 h-12 mx-auto mb-4 bg-blue-600 dark:bg-blue-700 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                </div>
                
                <h4 class="text-lg font-bold text-slate-900 dark:text-white mb-2">
                    {{ $title }}
                </h4>
                <p class="text-sm text-slate-700 dark:text-slate-300 mb-1">
                    {{ trans_choice('dashboard.quizzes.questions_count', $questionsCount, ['count' => $questionsCount]) }}
                </p>
                <div class="h-1 w-12 bg-blue-500 my-3 rounded-full mx-auto"></div>
                <p class="text-sm text-slate-600 dark:text-slate-400 mb-4">
                    {{ __('dashboard.quizzes.upgrade_to_access') }}
                </p>
                
                <a href="{{ route('plans', ['locale' => $locale]) }}" 
                   class="block w-full text-center px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                    {{ __('dashboard.quizzes.upgrade_now') }}
                </a>
            </div>
        </div>
    @endif

    <!-- Lock Badge -->
    @if(!$isFree)
        <div class="absolute top-3 right-3 z-30">
            <div class="flex items-center gap-1.5 px-3 py-1 bg-blue-600 dark:bg-blue-700 text-white text-xs font-bold rounded-full shadow-md">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
                {{ $planNameString }}
            </div>
        </div>
    @endif

    <!-- Content -->
    <div class="relative z-10 p-6 group-hover:blur-[1px] transition-all duration-300 flex-1 flex flex-col">
        <!-- Quiz Icon -->
        <div class="w-12 h-12 bg-blue-100 dark:bg-blue-950 rounded-lg flex items-center justify-center mb-4">
            <svg class="w-6 h-6 text-blue-700 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
        </div>

        <!-- Title -->
        <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-2">
            {{ $title }}
        </h3>

        <!-- Question Count -->
        <div class="flex items-center text-sm text-slate-600 dark:text-slate-400 mb-3">
            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"/>
            </svg>
            {{ trans_choice('dashboard.quizzes.questions_count', $questionsCount, ['count' => $questionsCount]) }}
            <span class="mx-2">•</span>
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            {{ $timeLimit }}
        </div>

        <!-- First Question Preview -->
        @if($questionText)
            <div class="mt-4 p-4 bg-slate-50 dark:bg-slate-800 rounded-lg border border-slate-200 dark:border-slate-700 flex-1">
                <h4 class="text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                    {{ __('dashboard.quizzes.sample_question') }}:
                </h4>
                <p class="text-slate-800 dark:text-slate-200 text-sm">
                    {{ Str::limit(strip_tags($questionText), 120) }}
                </p>
            </div>
        @else
            <p class="text-slate-600 dark:text-slate-400 text-sm mt-2 flex-1">
                {{ Str::limit(strip_tags($description), 120) }}
            </p>
        @endif

        <!-- Action Button -->
        <div class="mt-6 pt-4 border-t border-slate-200 dark:border-slate-700">
            @if($quiz->is_locked)
                <button disabled class="w-full px-4 py-2.5 bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 text-sm font-medium rounded-lg cursor-not-allowed">
                    {{ __('dashboard.quizzes.locked') }}
                </button>
            @else
                <a href="{{ route('dashboard.quizzes.show', ['locale' => $locale, 'quiz' => $quiz]) }}" 
                   class="block w-full text-center px-4 py-2.5 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white text-sm font-medium rounded-lg shadow-sm hover:shadow-md transition-all duration-200">
                    {{ __('dashboard.quizzes.start_quiz') }}
                </a>
            @endif
        </div>
    </div>
</div>
