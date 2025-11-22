@extends('layouts.app')

@section('content')
    <!-- Background Elements -->
    <div class="fixed right-0 translate-x-[30%] sm:translate-x-[50%] -translate-y-1/3 rounded-full w-[20rem] sm:w-[30rem] md:w-[40rem] lg:w-[50rem] aspect-square bg-[#8ECAE680]/50 pointer-events-none -z-10">
        <div class="absolute bottom-0 left-1/2 -translate-x-1/2 rounded-full w-[50%] aspect-square bg-[#8ECAE680]/90"></div>
    </div>
    
    <div class="relative z-10">
        @include('components.home.hero')
        
        <div class="my-8 sm:my-12 lg:my-16">
            <h2 class="text-2xl sm:text-3xl font-bold text-center mb-6 sm:mb-8 px-4">{{ __('home.guestQuizSection.tryOurFreeGuestQuiz') }}</h2>
            <div class="max-w-xl mx-auto px-4">
                @include('components.home.guest-quiz-section')
            </div>
        </div>
        
        @include('components.home.offers')
        @include('components.home.subscription-plans')
        @include('components.home.blogs')
    </div>
@endsection
