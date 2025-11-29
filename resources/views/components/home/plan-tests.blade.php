@props(['quizzes' => collect([])])

@php
    $filteredQuizzes = $quizzes->filter(fn($quiz) => !$quiz->is_guest_quiz);
    $hasQuizzes = $filteredQuizzes->isNotEmpty();
    $currentLocale = app()->getLocale();
@endphp

@if ($hasQuizzes)
    <section class="py-4 bg-white dark:bg-slate-950">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Section Header -->
            <div class="mb-4 text-center">
                <div class="mb-2">
                    <div class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-blue-100 to-cyan-100 dark:from-blue-900/30 dark:to-cyan-900/30 rounded-full border border-blue-200 dark:border-blue-800 mb-4">
                        <span class="h-2 w-2 bg-blue-600 rounded-full mr-2"></span>
                        <span class="text-sm font-semibold text-blue-700 dark:text-blue-300">
                            {{ __('home.planTests.more_tests') }}
                        </span>
                    </div>
                </div>
                <p class="text-lg text-slate-600 dark:text-slate-400 max-w-2xl mx-auto">
                    {{ __('home.planTests.subtitle') }}
                </p>
            </div>

            <!-- Quizzes Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($filteredQuizzes as $quiz)
                    @php
                        $title = $quiz->getTranslation('title', $currentLocale, false) ?: $quiz->title;
                        $description = $quiz->getTranslation('description', $currentLocale, false) ?: $quiz->description;
                        $questionsCount = $quiz->questions_count ?? $quiz->questions()->count();
                        $plan = $quiz->subscription_plan;
                        $planName = $plan->name ?? 'Premium';
                    @endphp

                    <div class="group relative bg-white dark:bg-slate-900 border-2 border-slate-200 dark:border-slate-800 hover:border-blue-500 dark:hover:border-blue-600 transition-all duration-300 rounded-2xl overflow-hidden">
                        
                        <!-- Lock Overlay (Always Visible) -->
                        <div class="absolute inset-0 bg-gradient-to-b from-white/0 via-white/80 to-white/95 dark:from-slate-900/0 dark:via-slate-900/80 dark:to-slate-900/95 z-20 flex flex-col items-center justify-end p-6 rounded-2xl">
                            <div class="absolute inset-0 bg-gradient-to-b from-blue-50/30 via-blue-100/40 to-blue-100/80 dark:from-blue-900/10 dark:via-blue-900/20 dark:to-blue-900/40 backdrop-blur-sm rounded-2xl"></div>
                            <div class="text-center relative z-10">
                                <!-- Lock Icon -->
                                <div class="w-16 h-16 mx-auto mb-4 bg-blue-600 dark:bg-blue-700 flex items-center justify-center">
                                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                    </svg>
                                </div>
                                
                                <h4 class="text-xl font-bold text-slate-900 dark:text-white mb-2">
                                    {{ $title }}
                                </h4>
                                <p class="text-sm text-slate-700 dark:text-slate-300 mb-1">
                                    {{ trans_choice('home.planTests.questions_count', $questionsCount, ['count' => $questionsCount]) }} • {{ __('home.planTests.plan', ['planName' => $planName]) }}
                                </p>
                                <div class="h-1 w-16 bg-blue-500 my-3 mx-auto rounded-full"></div>
                                <p class="text-sm text-slate-600 dark:text-slate-400 mb-4 max-w-xs mx-auto">
                                    {{ __('home.planTests.unlock_access') }}
                                </p>
                                
                                <div class="w-full space-y-3">
                                    <a href="{{ route('subscriptions', ['locale' => $currentLocale]) }}" 
                                       class="block w-full text-center px-4 py-3 bg-amber-500 hover:bg-amber-600 text-white font-semibold rounded-lg shadow-md hover:shadow-lg transition-all duration-200 flex items-center justify-center">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        {{ __('home.planTests.pay_with_mobile_money') }}
                                    </a>
                                    <a href="{{ route('subscriptions', ['locale' => $currentLocale]) }}" 
                                       class="block w-full text-center px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                                        {{ __('home.planTests.view_all_plans') }}
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Lock Badge -->
                        <div class="absolute top-4 right-4 z-30">
                            <div class="flex items-center gap-1.5 px-3 py-1.5 bg-blue-600 dark:bg-blue-700 text-white text-xs font-bold rounded-full shadow-md">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                                {{ $planName }}
                            </div>
                        </div>

                        <!-- Content with Partial Blur -->
                        <div class="relative z-10 p-8 group-hover:blur-[2px] transition-all duration-300">
                            <!-- Quiz Icon -->
                            <div class="w-12 h-12 bg-blue-100 dark:bg-blue-950 flex items-center justify-center mb-6">
                                <svg class="w-6 h-6 text-blue-700 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            </div>

                            <!-- Title -->
                            <h3 class="text-2xl font-bold text-slate-900 dark:text-white mb-3">
                                {{ $title }}
                            </h3>

                            <!-- Question Count -->
                            <div class="flex items-center text-sm text-slate-600 dark:text-slate-400 mb-4">
                                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"/>
                                </svg>
                                {{ trans_choice('home.planTests.questions_count', $questionsCount, ['count' => $questionsCount]) }}
                            </div>

                            <!-- Description -->
                            <p class="text-slate-700 dark:text-slate-300 leading-relaxed">
                                {{ $description }}
                            </p>
                        </div>

                        <!-- Plan Features -->
                        <div class="relative z-10 px-6 pb-6 group-hover:opacity-70 transition-opacity duration-300">
                            <div class="pt-4 border-t-2 border-slate-100 dark:border-slate-800">
                                <div class="flex items-center justify-between">
                                    <span class="text-sm font-medium text-slate-600 dark:text-slate-400">
                                        {{ trans_choice('home.planTests.questions_count', $questionsCount, ['count' => $questionsCount]) }}
                                    </span>
                                    <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- View All CTA -->
            <div class="mt-12 text-center">
                <a href="{{ route('quizzes', app()->getLocale()) }}" 
                   class="inline-flex items-center px-6 py-3 bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700 text-blue-600 dark:text-blue-400 font-semibold rounded-lg border-2 border-blue-200 dark:border-blue-900 transition-all duration-200 hover:shadow-md">
                    {{ __('home.planTests.browse_all_quizzes') }}
                    <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>
        </div>
    </section>

    <style>
        /* Only essential transitions */
        .transition-colors {
            transition-property: background-color, border-color, color;
            transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
        }

        .transition-opacity {
            transition-property: opacity;
            transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
        }
    </style>
@endif