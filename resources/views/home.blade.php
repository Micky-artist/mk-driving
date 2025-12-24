@extends('layouts.app')

@section('content')
    <div class="flex flex-col">
        @php
            // Get all quizzes including the guest quiz
            $guestQuiz = $quizzes->firstWhere('is_guest_quiz', true);
            $otherQuizzes = $quizzes->where('is_guest_quiz', false)->take(3);
        @endphp

        <!-- Top Hero with Car Animation and Guest Quiz -->
        <div class="flex-none">
            <x-home.top-hero 
                :title="__('home.car_animation.title')"
                :subtitle="__('home.car_animation.subtitle')"
                :ctaText="__('home.car_animation.cta')"
                :ctaUrl="route('register', app()->getLocale())"
                :quizzes="$quizzes" />
        </div>

        <!-- Main Content Area -->
        <div class="flex-grow">
            <!-- Plan Tests Section -->
            <x-home.plan-tests :quizzes="$otherQuizzes" />

            <!-- Main Content -->
            <div class="relative bg-white/90 dark:bg-gray-900/90">
                <div class="relative z-10 px-4 sm:px-6 lg:px-8">
                    @include('components.home.hero')
                    @include('components.home.subscription-plans')
                    @include('components.home.forum-section', ['questions' => $recentQuestions ?? []])
                </div>
            </div>
        </div>
    </div>
@endsection