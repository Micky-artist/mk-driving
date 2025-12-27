@props(['quizzes' => collect([])])

@php
    $filteredQuizzes = $quizzes->filter(fn($quiz) => !($quiz['is_guest_quiz'] ?? false));
    $hasQuizzes = $filteredQuizzes->isNotEmpty();
    $currentLocale = app()->getLocale();
@endphp

@if ($hasQuizzes)
    <section class="py-4 sm:py-6 lg:py-8 bg-white dark:bg-gray-900">
        <div class="max-w-7xl mx-auto px-2 sm:px-4 lg:px-8">
            <!-- Section Header -->
            <x-section-header :title="__('home.planTests.title')" :href="route('dashboard.quizzes.index', app()->getLocale())" />

            <!-- Quizzes Grid -->
            <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-3">
                @foreach ($filteredQuizzes as $quiz)
                    <x-quiz.quiz-card :quiz="$quiz" />
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