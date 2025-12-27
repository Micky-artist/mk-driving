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
            <x-home.top-hero 
                :title="__('home.car_animation.title')"
                :subtitle="__('home.car_animation.subtitle')"
                :ctaText="__('home.car_animation.cta')"
                :ctaUrl="route('register', app()->getLocale())"
                :quizzes="$quizzes" /> <!-- Pass quizzes for billboard link -->
        </div>

        <!-- Quiz-Taker Section (Full Width, Unconstrained) -->
        @php
            $guestQuiz = $quizzes->firstWhere('is_guest_quiz', true);
        @endphp
        @if($guestQuiz && isset($guestQuiz['id']) && isset($guestQuiz['title']) && isset($guestQuiz['questions']))
        <div class="bg-gray-50 dark:bg-gray-800 py-8 relative" id="guest-quiz-section">
            <div class="px-2">
                <x-section-header 
                    :title="auth()->check() ? __('home.guestQuiz.continueLearning') : __('home.guestQuiz.trySampleQuiz')" 
                    :href="route('dashboard.quizzes.show', ['locale' => app()->getLocale(), 'quiz' => $guestQuiz['id']])" />
                <x-unified-quiz-taker 
                    :quiz="$guestQuiz" 
                    :show-header="true"
                    :compact-mode="false"
                    :allow-navigation="true" />
            </div>
        </div>
        @endif

        <!-- Main Content Area -->
        <div class="flex-grow">
            <!-- Plan Tests Section -->
            <x-home.plan-tests :quizzes="$otherQuizzes" />

            <!-- Main Content -->
            <div class="relative bg-white/90 dark:bg-gray-900/90">
                <div class="relative z-10 px-2 sm:px-4 lg:px-8">
                    @include('components.home.subscription-plans')
                    @include('components.home.forum-intro', ['forumData' => $forumData])
                    @include('components.home.hero')
                </div>
            </div>
        </div>
    </div>
@endsection