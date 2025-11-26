@extends('layouts.app')

@section('content')
    <!-- Hero Section (No Overlay) -->
    <div class="relative z-20">
        @include('components.home.hero')
    </div>
    
    <!-- Car Animation Component -->
    <x-home.car-animation 
        title="Start Your Driving Journey"
        subtitle="Join thousands of successful drivers who passed with our help"
        ctaText="Get Started Now"
    />
    
    <!-- Main Content with Overlay -->
    <div class="relative bg-white/90 dark:bg-gray-900/90">
        <div class="relative z-10 px-4 sm:px-6 lg:px-8">

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
        
        @include('components.home.subscription-plans')
        @include('components.home.offers')
        @include('components.home.blogs')
        </div> <!-- Close relative z-10 -->
    </div> <!-- Close main content with overlay -->
@endsection
