@extends('layouts.app')

@section('content')
    <!-- Background Animation Component -->
    <x-background-animation />
    
    <!-- Semi-transparent overlay layer -->
    <div class="fixed inset-0 bg-white/10 dark:bg-gray-900/40 -z-10"></div>

    <!-- MAIN CONTENT - COMPLETELY UNTOUCHED -->
    <div class="relative z-10">
        @include('components.home.hero')

        <div class="my-8 sm:my-12 lg:my-16 fade-in delay-200">
            <h2 class="text-2xl sm:text-3xl font-bold text-center mb-6 sm:mb-8 px-4 fade-in delay-300">
                {{ __('home.guestQuizSection.tryOurFreeGuestQuiz') }}
            </h2>
            <div class="w-full fade-in delay-400">
                @include('components.home.guest-quiz-carousel', [
                    'quizzes' => $quizzes,
                    'guestQuiz' => $guestQuiz ?? null
                ])
            </div>
        </div>

        @include('components.home.offers')
        @include('components.home.subscription-plans')
        @include('components.home.blogs')
    </div>
@endsection
