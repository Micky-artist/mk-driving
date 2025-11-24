@extends('layouts.app')

@section('content')
    <!-- Background Animation Component -->
    <x-background-animation />

    <!-- MAIN CONTENT - COMPLETELY UNTOUCHED -->
    <div class="relative z-10">
        @include('components.home.hero')

        <div class="my-8 sm:my-12 lg:my-16">
            <h2 class="text-2xl sm:text-3xl font-bold text-center mb-6 sm:mb-8 px-4">
                {{ __('home.guestQuizSection.tryOurFreeGuestQuiz') }}
            </h2>
            <div class="max-w-xl mx-auto px-4">
                @include('components.home.guest-quiz-section')
            </div>
        </div>

        @include('components.home.offers')
        @include('components.home.subscription-plans')
        @include('components.home.blogs')
    </div>
@endsection
