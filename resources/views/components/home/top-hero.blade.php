@props(['title', 'subtitle', 'ctaText', 'ctaUrl', 'quizzes' => collect([])])

@php
    $guestQuiz = $quizzes->first(fn($q) => $q->is_guest_quiz);
    $hasGuestQuiz = $guestQuiz !== null;
@endphp

<div class="relative w-full bg-gradient-to-br from-blue-50 to-blue-100 dark:from-gray-900 dark:to-gray-800 overflow-hidden">
    <!-- Animated Background Elements -->
    <div class="absolute inset-0 opacity-10">
        <div class="absolute inset-0 bg-grid-white/[0.05] [mask-image:linear-gradient(to_bottom,transparent,white,transparent)]"></div>
    </div>
    
    <div class="relative max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8 md:py-12 overflow-visible">
        <div class="relative flex flex-col lg:flex-row gap-8 items-start min-h-[400px] md:min-h-[450px]">
            <!-- Left Column - Guest Quiz (Full width on mobile, 1/2 on desktop) -->
            <div class="w-full lg:w-1/2 relative z-10">
                @if($hasGuestQuiz)
                    <div class="h-full">
                        @include('components.home.guest-quiz-section', [
                            'guestQuiz' => $guestQuiz
                        ])
                    </div>
                @endif
            </div>

            <!-- Right Column - Title and Animation -->
            <div class="w-full lg:w-1/2 -mt-12 relative z-20">
                <!-- Title Section - Moved to top of right column -->
                <div class="text-left ml-2 mt-8 md:mt-12">
                    <h1 class="text-2xl sm:text-3xl md:text-4xl font-extrabold bg-gradient-to-r from-blue-600 via-blue-500 to-cyan-500 dark:from-cyan-300 dark:via-blue-200 dark:to-cyan-300 bg-clip-text text-transparent mb-1 sm:mb-2 leading-tight">
                        {{ $title }}
                    </h1>
                    <div class="h-1 w-12 bg-blue-400/70 dark:bg-cyan-300/70 my-2 mx-auto rounded-full"></div>
                </div>
                
                <!-- Animation Container -->
                <div class="w-full md:-mt-12">
                    <x-home.car-animation />
                    
                    <!-- CTA Button -->
                    <div class="relative inline-block group ml-4 mt-40 md:mt-60">
                        <div class="absolute -inset-1 bg-gradient-to-r from-cyan-400 to-blue-500 rounded-full blur opacity-75 group-hover:opacity-100 transition-all duration-300 group-hover:animate-pulse"></div>
                        <a href="{{ $ctaUrl }}" 
                           class="relative flex items-center justify-center px-6 py-3 sm:px-8 sm:py-4 text-sm sm:text-base bg-gradient-to-r from-blue-600 to-blue-700 text-white font-bold rounded-full hover:from-blue-500 hover:to-blue-600 transition-all duration-300 transform group-hover:scale-105 shadow-xl hover:shadow-2xl">
                            <span class="drop-shadow-md">{{ $ctaText }}</span>
                            <svg class="ml-3 w-5 h-5 transform group-hover:translate-x-1 transition-transform duration-300" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
                </div>
            </div>
        </div>
    </div>
    
    
    <!-- Decorative Elements -->
    <div class="absolute bottom-0 left-0 right-0 h-12 bg-gradient-to-t from-white/10 dark:from-gray-900/50 to-transparent"></div>
</div>