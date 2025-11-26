<div class="relative w-full overflow-hidden bg-gradient-to-br from-[#f5f5f7] via-white to-[#f5f5f7] dark:from-gray-900 dark:via-gray-800 dark:to-gray-900">
    <!-- Decorative Background Elements -->
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <div class="absolute top-0 right-0 w-96 h-96 bg-blue-100 dark:bg-blue-900/20 rounded-full filter blur-3xl opacity-30 animate-pulse"></div>
        <div class="absolute bottom-0 left-0 w-96 h-96 bg-blue-100 dark:bg-blue-900/20 rounded-full filter blur-3xl opacity-30 animate-pulse delay-1000"></div>
    </div>

    <div class="max-w-7xl mx-auto px-4 md:px-8 relative z-10">
        <div class="grid lg:grid-cols-2 gap-8 lg:gap-12 items-center">
            
            <!-- Content Column - Shows first on mobile, left on desktop -->
            <div class="text-center lg:text-left fade-in">
                <!-- Badge -->
                <div class="inline-flex items-center gap-2 px-3 py-1.5 sm:px-4 sm:py-2 bg-blue-50 dark:bg-blue-900/30 rounded-full mb-4 sm:mb-6">
                    <span class="relative flex h-2 w-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-blue-500"></span>
                    </span>
                    <span class="text-xs sm:text-sm font-semibold text-blue-600 dark:text-blue-400">{{ __('hero.be_our_guest') }}</span>
                </div>

                <!-- Main Heading -->
                <h1 class="font-bold text-2xl sm:text-3xl lg:text-4xl xl:text-5xl leading-tight mb-4 sm:mb-6 fade-in delay-100">
                    <span class="text-[#023047] dark:text-white">{{ __('hero.pass_your') }}</span>
                    <br>
                    <span class="relative inline-block">
                        <span class="text-[#023047] dark:text-white">{{ __('hero.driving_test') }}</span>
                        <svg class="absolute -bottom-1 sm:-bottom-2 left-0 w-full h-2 sm:h-3 text-blue-500" viewBox="0 0 300 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M2 10C100 3 200 3 298 10" stroke="currentColor" stroke-width="3" stroke-linecap="round"/>
                        </svg>
                    </span>
                    <br>
                    <span class="text-blue-600 dark:text-blue-400">{{ __('hero.first_try') }}</span>
                </h1>

                <!-- Subtitle -->
                <p class="text-base sm:text-lg lg:text-xl text-gray-600 dark:text-gray-300 mb-6 sm:mb-8 max-w-xl mx-auto lg:mx-0 fade-in delay-200">
                    {{ __('hero.subtitle_short', ['default' => 'Practice with real exam questions. Clear explanations. Pass with confidence.']) }}
                </p>

                <!-- Compact Stats Row -->
                <div class="flex flex-wrap justify-center lg:justify-start gap-4 sm:gap-6 mb-6 sm:mb-8 fade-in delay-300">
                    <div class="flex items-center gap-1.5">
                        <div class="flex gap-0.5">
                            @for($i = 0; $i < 5; $i++)
                                <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4 text-yellow-400 fill-current" viewBox="0 0 20 20">
                                    <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                                </svg>
                            @endfor
                        </div>
                        <p class="text-xs sm:text-sm font-semibold text-gray-700 dark:text-gray-300">1.15K+ drivers</p>
                    </div>
                    <div class="w-px h-8 bg-gray-300 dark:bg-gray-600"></div>
                    <div class="text-center lg:text-left">
                        <p class="text-xl sm:text-2xl font-bold text-[#023047] dark:text-white">97% <span class="text-xs sm:text-sm font-semibold text-gray-700 dark:text-gray-300">{{ __('hero.pass_rate', ['default' => 'Pass Rate']) }}</span></p>
                    </div>
                    <div class="w-px h-8 bg-gray-300 dark:bg-gray-600"></div>
                    <div class="text-center lg:text-left">
                        <p class="text-xl sm:text-2xl font-bold text-blue-500 dark:text-blue-400">650+ <span class="text-xs sm:text-sm font-semibold text-gray-700 dark:text-gray-300">{{ __('hero.questions', ['default' => 'Questions']) }}</span></p>
                    </div>
                </div>

                <!-- CTA Buttons - Stacked on mobile -->
                <div class="flex flex-col sm:flex-row gap-3 sm:gap-4 justify-center lg:justify-start mb-6 sm:mb-8 fade-in delay-400">
                    @if($guestQuiz ?? false)
                        <a href="{{ route('guest-quiz.show', ['locale' => app()->getLocale(), 'quiz' => $guestQuiz->id]) }}"
                           class="group relative inline-flex items-center justify-center gap-2 bg-[#023047] hover:bg-[#023047]/90 rounded-full text-sm sm:text-base lg:text-lg px-6 py-3 sm:px-8 sm:py-4 text-white font-semibold transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl">
                            <span>{{ __('hero.try_free_quiz', ['default' => 'Try 5 Free Questions']) }}</span>
                            <svg class="w-4 h-4 sm:w-5 sm:h-5 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                            </svg>
                        </a>
                    @endif
                    
                    <a href="{{ route('register', app()->getLocale()) }}"
                       class="inline-flex items-center justify-center gap-2 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-full text-sm sm:text-base lg:text-lg px-6 py-3 sm:px-8 sm:py-4 text-[#023047] dark:text-white font-semibold border-2 border-[#023047] dark:border-white transition-all duration-300 transform hover:scale-105 shadow-md hover:shadow-lg">
                        <span>{{ __('hero.sign_up_free', ['default' => 'Sign Up Free']) }}</span>
                    </a>
                </div>

                <!-- Compact Trust Indicators -->
                <div class="flex flex-col sm:flex-row items-center justify-center lg:justify-start gap-3 sm:gap-4 fade-in delay-500">
                    <div class="flex items-center gap-2">
                        <div class="flex -space-x-2">
                            @foreach(['a9-min.jpg', '73x73-min.jpg', 'OTUwMjkuanBn-min.jpg', '6ZhaVrf-min.jpg'] as $avatar)
                                <img 
                                    src="https://driving-tests.org/wp-content/uploads/2021/05/{{ $avatar }}" 
                                    alt="Student" 
                                    class="w-8 h-8 rounded-full border-2 border-white dark:border-gray-800"
                                />
                            @endforeach
                        </div>
                        <p class="text-xs sm:text-sm text-gray-600 dark:text-gray-400">
                            <span class="font-semibold text-gray-900 dark:text-white">{{ __('hero.trusted_by', ['default' => 'Trusted by DMVs']) }}</span>
                            {{ __('hero.and_libraries', ['default' => '& 10+ libraries']) }}
                        </p>
                    </div>
                    
                    <!-- Guarantee Badge - Inline on mobile -->
                    <div class="inline-flex items-center gap-2 px-3 py-1.5 sm:px-4 sm:py-2 bg-green-50 dark:bg-green-900/20 rounded-full border border-green-200 dark:border-green-800">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span class="text-xs sm:text-sm font-semibold text-green-800 dark:text-green-300">{{ __('hero.pass_guarantee', ['default' => 'Pass Guarantee']) }}</span>
                    </div>
                </div>
            </div>

            <!-- Image Column - Shows second on mobile, right on desktop -->
            <div class="relative fade-in delay-200">
                <div class="relative max-w-lg mx-auto lg:max-w-none">
                    <!-- Main Image Card -->
                    <div class="relative rounded-2xl sm:rounded-3xl overflow-hidden shadow-2xl transform hover:scale-[1.02] transition-all duration-500">
                        <img
                            src="{{ asset('images/inside-car.png') }}"
                            alt="MkScholars Driving School Dashboard"
                            class="w-full h-auto object-cover"
                            draggable="false"
                        />
                        
                        <!-- Overlay Gradient -->
                        <div class="absolute inset-0 bg-gradient-to-t from-black/50 via-transparent to-transparent"></div>
                        
                        <!-- Floating Cards - Optimized for mobile -->
                        
                        <!-- Get Started Card - Top Left -->
                        <div class="absolute top-2 left-2 sm:top-4 sm:left-4 max-w-[140px] sm:max-w-[180px] rounded-xl sm:rounded-2xl border-2 border-white/80 p-2.5 sm:p-3 bg-white/90 backdrop-blur-xl shadow-xl fade-in delay-300 transform hover:scale-105 transition-all duration-300">
                            <div class="flex items-start gap-1.5 sm:gap-2 mb-1.5 sm:mb-2">
                                <svg class="w-4 h-4 sm:w-5 sm:h-5 text-blue-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                </svg>
                                <p class="font-bold text-xs sm:text-sm text-gray-900">{{ __('hero.get_started') }}</p>
                            </div>
                            <p class="text-[10px] sm:text-xs text-gray-700">
                                {{ __('hero.start_test_description', ['default' => 'Begin your journey']) }}
                            </p>
                        </div>

                        <!-- Start Test Button - Bottom Right -->
                        @if($guestQuiz ?? false)
                            
                                href="{{ route('guest-quiz.show', ['locale' => app()->getLocale(), 'quiz' => $guestQuiz->id]) }}"
                                class="absolute bottom-2 right-2 sm:bottom-4 sm:right-4 group rounded-xl sm:rounded-2xl border-2 border-white/80 px-3 py-2 sm:px-5 sm:py-3 bg-gradient-to-r from-[#FF7B00] to-[#ff9a3c] hover:from-[#ff9a3c] hover:to-[#FF7B00] backdrop-blur-xl text-white transition-all duration-300 transform hover:scale-110 shadow-xl fade-in delay-400"
                            >
                                <div class="flex items-center gap-1.5 sm:gap-2">
                                    <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M6.3 2.841A1.5 1.5 0 004 4.11V15.89a1.5 1.5 0 002.3 1.269l9.344-5.89a1.5 1.5 0 000-2.538L6.3 2.84z"/>
                                    </svg>
                                    <span class="font-bold text-xs sm:text-sm">{{ __('hero.start_test') }}</span>
                                </div>
                            </a>
                        @endif

                        <!-- Stats Bubble - Bottom Left -->
                        <div class="absolute bottom-2 left-2 sm:bottom-4 sm:left-4 rounded-xl sm:rounded-2xl border-2 border-white/80 p-2.5 sm:p-3 bg-white/90 backdrop-blur-xl shadow-xl fade-in delay-500 transform hover:scale-105 transition-all duration-300">
                            <div class="flex items-center gap-2 sm:gap-3">
                                <div class="flex-shrink-0">
                                    <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-full bg-green-500 flex items-center justify-center">
                                        <span class="text-white font-bold text-base sm:text-lg">97%</span>
                                    </div>
                                </div>
                                <div>
                                    <p class="font-bold text-[10px] sm:text-xs text-gray-900">{{ __('hero.pass_rate') }}</p>
                                    <p class="text-[9px] sm:text-[10px] text-gray-600">{{ __('hero.success_guaranteed', ['default' => 'Guaranteed']) }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- DMV Badge - Top Right (Hidden on small mobile) -->
                        <div class="hidden xs:block absolute top-2 right-2 sm:top-4 sm:right-4 rounded-full border-2 border-white/80 px-2.5 py-1 sm:px-3 sm:py-1.5 bg-gradient-to-r from-blue-500 to-blue-600 backdrop-blur-xl shadow-xl fade-in delay-600">
                            <div class="flex items-center gap-1.5">
                                <svg class="w-3 h-3 sm:w-4 sm:h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                <span class="text-white font-semibold text-[10px] sm:text-xs">{{ __('hero.dmv_approved', ['default' => 'DMV Approved']) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- Bottom Trust Bar - Simplified for mobile -->
    <div class="border-t border-gray-200 dark:border-gray-700 bg-white/50 dark:bg-gray-800/50 backdrop-blur-sm">
        <div class="max-w-7xl mx-auto px-4 py-4 sm:py-6">
            <div class="grid grid-cols-2 sm:flex sm:flex-wrap items-center justify-center gap-3 sm:gap-6 lg:gap-8 opacity-60 hover:opacity-100 transition-opacity">
                <div class="flex items-center gap-1.5 sm:gap-2">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5 text-green-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <span class="text-xs sm:text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('hero.feature_1', ['default' => 'Real Exam Questions']) }}</span>
                </div>
                <div class="flex items-center gap-1.5 sm:gap-2">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5 text-green-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <span class="text-xs sm:text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('hero.feature_2', ['default' => 'Instant Results']) }}</span>
                </div>
                <div class="flex items-center gap-1.5 sm:gap-2">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5 text-green-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <span class="text-xs sm:text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('hero.feature_3', ['default' => 'Money-Back Guarantee']) }}</span>
                </div>
                <div class="flex items-center gap-1.5 sm:gap-2">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5 text-green-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <span class="text-xs sm:text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('hero.feature_4', ['default' => 'Mobile Friendly']) }}</span>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    @keyframes slideInDown {
        from {
            opacity: 0;
            transform: translateY(-20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .fade-in {
        animation: slideInDown 0.6s ease-out forwards;
        opacity: 0;
    }

    .fade-in.delay-100 { animation-delay: 0.1s; }
    .fade-in.delay-200 { animation-delay: 0.2s; }
    .fade-in.delay-300 { animation-delay: 0.3s; }
    .fade-in.delay-400 { animation-delay: 0.4s; }
    .fade-in.delay-500 { animation-delay: 0.5s; }
    .fade-in.delay-600 { animation-delay: 0.6s; }
    .delay-1000 { animation-delay: 1s; }

    /* Extra small breakpoint for very small devices */
    @media (min-width: 475px) {
        .xs\:block {
            display: block;
        }
    }
</style>