@props(['quizzes' => collect([])])

@php
    $filteredQuizzes = $quizzes->filter(fn($quiz) => !$quiz->is_guest_quiz);
    $hasQuizzes = $filteredQuizzes->isNotEmpty();
    $currentLocale = app()->getLocale();
@endphp

@if ($hasQuizzes)
    <section class="py-12 md:py-16 lg:py-20 bg-gradient-to-b from-blue-900/30 to-blue-950/50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Section Header -->
            <div class="text-center mb-10 md:mb-14">
                <h2 class="text-2xl md:text-3xl lg:text-4xl font-bold text-white mb-4">
                    {{ __('home.planTests.title') }}
                </h2>
                <div class="w-20 h-1 bg-cyan-400 mx-auto rounded-full mb-4"></div>
                <p class="text-blue-100 max-w-3xl mx-auto text-sm md:text-base">
                    {{ __('home.planTests.subtitle') }}
                </p>
            </div>

            <!-- Quizzes Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                @php
                    // Determine which quizzes should be locked (60% of them)
                    $totalQuizzes = $filteredQuizzes->count();
                    $lockedCount = ceil($totalQuizzes * 0.6);
                    $counter = 0;
                @endphp

                @foreach ($filteredQuizzes as $quiz)
                    @php
                        $isLocked = $counter < $lockedCount;
                        $counter++;

                        $title = $quiz->getTranslation('title', $currentLocale, false) ?: $quiz->title;
                        $description =
                            $quiz->getTranslation('description', $currentLocale, false) ?: $quiz->description;
                        $questionsCount = $quiz->questions_count ?? $quiz->questions()->count();
                        $plan = $quiz->subscription_plan; // Assuming this relationship exists
                        $planName = $plan->name ?? 'Premium';
                        $planColor = $plan->color ?? 'blue';
                    @endphp

                    <div
                        class="group relative bg-gradient-to-br from-blue-900/80 to-blue-800/50 rounded-xl p-6 shadow-xl border border-blue-700/30 hover:border-cyan-400/50 transition-all duration-300 transform hover:-translate-y-1 overflow-hidden">
                        @if ($isLocked)
                            <!-- Lock Overlay -->
                            <div
                                class="absolute inset-0 bg-gradient-to-t from-black/90 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex flex-col items-center justify-end p-6 pt-16">
                                <div class="text-center">
                                    <div
                                        class="w-12 h-12 bg-{{ $planColor }}-500/20 rounded-full flex items-center justify-center mx-auto mb-3">
                                        <svg class="w-6 h-6 text-{{ $planColor }}-400" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z">
                                            </path>
                                        </svg>
                                    </div>
                                    <h4 class="text-white font-bold mb-1">
                                        {{ __('home.planTests.unlock_with_plan', ['plan' => $planName]) }}</h4>
                                    <p class="text-blue-100 text-sm mb-4">{{ __('home.planTests.upgrade_to_access') }}
                                    </p>
                                    <a href="{{ route('subscriptions', ['locale' => $currentLocale]) }}"
                                        class="inline-flex items-center px-4 py-2 bg-{{ $planColor }}-600 hover:bg-{{ $planColor }}-700 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                                        {{ __('home.planTests.upgrade_now') }}
                                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                                        </svg>
                                    </a>
                                </div>
                            </div>

                            <!-- Lock Icon -->
                            <div
                                class="absolute top-3 left-3 bg-{{ $planColor }}-600 text-white p-1.5 rounded-full">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z">
                                    </path>
                                </svg>
                            </div>
                        @endif

                        <!-- Quiz Badge -->
                        <div
                            class="absolute -top-3 -right-3 bg-gradient-to-r from-cyan-500 to-blue-600 text-white text-xs font-bold px-3 py-1 rounded-full shadow-lg">
                            {{ $questionsCount }} {{ __('home.quiz.questions', ['count' => $questionsCount]) }}
                        </div>

                        <!-- Quiz Icon -->
                        <div
                            class="w-14 h-14 bg-cyan-500/10 rounded-xl flex items-center justify-center mb-4 text-cyan-400">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                </path>
                            </svg>
                        </div>

                        <!-- Quiz Title -->
                        <h3 class="text-xl font-bold text-white mb-2">{{ $title }}</h3>

                        <!-- Quiz Description -->
                        <p class="text-blue-100 text-sm mb-4 line-clamp-2">{{ $description }}</p>

                        <!-- CTA Button -->
                        @if ($isLocked)
                            <div class="text-{{ $planColor }}-400 text-sm font-medium">
                                {{ __('home.planTests.requires_plan', ['plan' => $planName]) }}
                            </div>
                        @else
                            <a href="{{ route('quizzes.attempt', ['quiz' => $quiz->id, 'locale' => $currentLocale]) }}"
                                class="inline-flex items-center text-cyan-300 hover:text-white font-medium text-sm group-hover:translate-x-1 transition-transform duration-300 focus:outline-none">
                                {{ __('home.quiz.start_quiz') }}
                                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                                </svg>
                            </a>
                        @endif
                    </div>
                @endforeach
            </div>

            <!-- View All Button -->
            <div class="text-center mt-10">
                <a href="{{ route('quizzes', app()->getLocale()) }}"
                    class="inline-flex items-center px-6 py-3 border border-transparent text-sm font-medium rounded-full text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                    {{ __('home.planTests.view_all_quizzes') }}
                    <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                    </svg>
                </a>
            </div>
        </div>
    </section>
@endif
