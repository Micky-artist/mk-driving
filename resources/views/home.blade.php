@extends('layouts.app')

@section('content')
    <div class="flex flex-col">
        @php
            // Get all quizzes including the guest quiz
            $guestQuiz = $quizzes->firstWhere('is_guest_quiz', true);
            $otherQuizzes = $quizzes->where('is_guest_quiz', false)->take(4);
        @endphp

        <!-- Top Hero with Car Animation (Animations Only) -->
        <div class="flex-none">
            <x-home.top-hero :title="__('home.car_animation.title')" :subtitle="__('home.car_animation.subtitle')" :ctaText="__('home.car_animation.cta')" :ctaUrl="route('register', app()->getLocale())" :quizzes="$quizzes" />
            <!-- Pass quizzes for billboard link -->
        </div>

        <!-- Quiz-Taker Section (Full Width, Unconstrained) -->
        @php
            $guestQuiz = $quizzes->firstWhere('is_guest_quiz', true);
        @endphp
        @if ($guestQuiz && isset($guestQuiz['id']) && isset($guestQuiz['title']) && isset($guestQuiz['questions']))
            <div class="bg-gray-50 dark:bg-gray-800 py-8 relative" id="guest-quiz-section">
                <div class="px-2 md:px-6">
                    <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700">
                        <div class="p-2 md:p-6">
                            <x-section-header :title="auth()->check()
                                ? __('home.guestQuiz.continueLearning')
                                : __('home.guestQuiz.trySampleQuiz')" :href="route('dashboard.quizzes.show', [
                                'locale' => app()->getLocale(),
                                'quiz' => $guestQuiz['id'],
                            ])" />
                            <x-unified-quiz-taker :quiz="$guestQuiz" :show-header="true" :compact-mode="false"
                                :allow-navigation="true" />

                            <!-- Continue with Quiz Viewer Button -->
                            <div class="py-4 text-center">
                                <a href="{{ route('dashboard.quizzes.show', ['locale' => app()->getLocale(), 'quiz' => $guestQuiz['id']]) }}"
                                    class="group inline-flex items-center px-6 py-3 text-base font-medium text-blue-600 dark:text-blue-400 border-2 border-blue-500 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg transition-colors">
                                    <span>{{ __('home.quizViewer.continueWithViewer') }}</span>
                                    <svg class="w-4 h-4 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5l7 7-7 7" />
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Main Content Area -->
        <div class="flex-grow">
            <!-- Plan Tests Section -->
            <div class="px-2 md:px-6">
                <div class="bg-white dark:bg-gray-900 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-2 md:p-6">
                    <x-home.plan-tests :quizzes="$otherQuizzes" />
                </div>
            </div>

            <!-- Main Content -->
            <div class="px-2 md:px-6">
                <div class="bg-white dark:bg-gray-900 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-2 md:p-6">
                    @include('components.home.subscription-plans')
                </div>
            </div>

            <!-- Forum Section -->
            <div class="px-2 md:px-6">
                <div class="bg-white dark:bg-gray-900 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-2 md:p-6">
                    @include('components.home.forum-intro', ['forumData' => $forumData])
                </div>
            </div>
        </div>
    </div>
@endsection
