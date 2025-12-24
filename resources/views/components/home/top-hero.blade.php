@props(['title', 'subtitle', 'ctaText', 'ctaUrl', 'quizzes' => collect([])])

@php
    $guestQuiz = $quizzes->first(fn($q) => $q->is_guest_quiz);
    $hasGuestQuiz = $guestQuiz !== null;
@endphp

<div
    class="relative w-full bg-gradient-to-br from-blue-50 to-blue-100 dark:from-gray-900 dark:to-gray-800 overflow-hidden">

    <!-- Animated Background Elements -->
    <div class="absolute inset-0 opacity-10">
        <div
            class="absolute inset-0 bg-grid-white/[0.05] [mask-image:linear-gradient(to_bottom,transparent,white,transparent)]">
        </div>
    </div>

    <div class="relative max-w-7xl w-full mx-auto px-2 sm:px-6 lg:px-8 py-8 overflow-visible">
        <div class="relative flex flex-col lg:flex-row gap-2 md:gap-8 items-start">
            <!-- Left Column - Guest Quiz (Full width on mobile, 1/2 on desktop) -->
            <!-- Desktop Quiz Section (hidden on mobile) -->
            <div class="w-full hidden lg:block lg:w-1/2 relative z-10">
                @if ($hasGuestQuiz)
                    <div class="h-full">
                        @include('components.home.guest-quiz-section', [
                            'guestQuiz' => $guestQuiz,
                        ])
                    </div>
                @endif
            </div>

            <!-- Right Column - Title and Animation -->
            <div class="w-full lg:w-1/2 -mt-12 relative" style="z-index: 1;">
                <!-- Animated Background with enhanced visibility -->
                <div class="absolute inset-0 overflow-hidden" style="z-index: -1;">
                    <!-- Base layer - subtle movement with softer edges -->
                    <div class="absolute inset-0 bg-gradient-to-r from-transparent via-gray-200/25 to-transparent dark:from-transparent dark:via-blue-900/20 dark:to-transparent"
                        style="animation: groundMove 10s linear infinite; animation-direction: reverse; will-change: transform; mask-image: linear-gradient(90deg, transparent 0%, white 20%, white 80%, transparent 100%);">
                    </div>

                    <!-- Mid layer - medium movement with softer edges -->
                    <div class="absolute inset-0 bg-gradient-to-r from-transparent via-gray-300/20 to-transparent dark:from-transparent dark:via-blue-800/15 dark:to-transparent"
                        style="animation: groundMove 15s linear infinite; animation-direction: reverse; animation-delay: -5s; will-change: transform; mask-image: linear-gradient(90deg, transparent 0%, white 15%, white 85%, transparent 100%);">
                        <div class="absolute inset-0 backdrop-blur-[1px]"></div>
                    </div>

                    <!-- Top layer - road lines with softer edges -->
                    <div class="absolute inset-0 opacity-70 dark:opacity-20">
                        <div class="absolute inset-0 flex flex-col justify-center items-start gap-16">
                            <div class="h-px bg-gradient-to-r from-transparent via-gray-500/60 to-transparent dark:from-transparent dark:via-blue-300/30 dark:to-transparent w-full"
                                style="animation: roadLines 12s linear infinite; animation-direction: reverse; will-change: transform; mask-image: linear-gradient(90deg, transparent 0%, white 10%, white 90%, transparent 100%);">
                            </div>
                            <div class="h-px bg-gradient-to-r from-transparent via-gray-500/60 to-transparent dark:from-transparent dark:via-blue-300/30 dark:to-transparent w-full"
                                style="animation: roadLines 12s linear infinite; animation-direction: reverse; animation-delay: 3s; will-change: transform; mask-image: linear-gradient(90deg, transparent 0%, white 10%, white 90%, transparent 100%);">
                            </div>
                            <div class="h-px bg-gradient-to-r from-transparent via-gray-500/60 to-transparent dark:from-transparent dark:via-blue-300/30 dark:to-transparent w-full"
                                style="animation: roadLines 12s linear infinite; animation-direction: reverse; animation-delay: 6s; will-change: transform; mask-image: linear-gradient(90deg, transparent 0%, white 10%, white 90%, transparent 100%);">
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Title Section - Overlay on Animation -->
                <div class="absolute top-0 left-0 right-0 z-30 text-center mt-4 md:mt-8">
                    <h1
                        class="text-2xl sm:text-3xl md:text-4xl font-extrabold bg-gradient-to-r from-blue-600 via-blue-500 to-cyan-500 dark:from-cyan-300 dark:via-blue-200 dark:to-cyan-300 bg-clip-text text-transparent mb-1 sm:mb-2 leading-tight">
                        {{ $title }}
                    </h1>
                    <div class="h-1 w-12 bg-blue-400/70 dark:bg-cyan-300/70 my-2 mx-auto rounded-full"></div>
                </div>

                <!-- Animation Container -->
                <div class="w-full">
                    <div class="relative flex flex-col items-center justify-center">
                        <!-- Car Animation Only -->
                        <div class="w-full flex justify-center">
                            <div class="w-[280px] md:w-[500px] h-[200px] md:h-[250px] relative">
                                <!-- Loading Skeleton -->
                                <div id="car-loading"
                                    class="absolute pt-4 inset-0 bg-gradient-to-br from-gray-200/40 to-gray-300/40 dark:from-blue-800/20 dark:to-blue-900/20 rounded-lg overflow-hidden">
                                    <!-- Shimmer overlay -->
                                    <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/30 to-transparent animate-shimmer"></div>
                                    
                                    <!-- Skeleton shapes for car -->
                                    <div class="absolute inset-0 flex flex-col justify-center items-center gap-4 p-8">
                                        <!-- Main car body skeleton -->
                                        <div class="w-3/4 h-12 bg-gray-300/60 dark:bg-gray-600/40 rounded-lg animate-pulse"></div>
                                        <!-- Car details -->
                                        <div class="flex gap-4 w-3/4 justify-center">
                                            <div class="w-8 h-8 bg-gray-300/60 dark:bg-gray-600/40 rounded-full animate-pulse" style="animation-delay: 0.2s;"></div>
                                            <div class="w-16 h-6 bg-gray-300/60 dark:bg-gray-600/40 rounded animate-pulse" style="animation-delay: 0.4s;"></div>
                                            <div class="w-8 h-8 bg-gray-300/60 dark:bg-gray-600/40 rounded-full animate-pulse" style="animation-delay: 0.6s;"></div>
                                        </div>
                                        <!-- Moving lines for motion effect -->
                                        <div class="absolute inset-0 flex flex-col justify-center items-start gap-6 opacity-50">
                                            <div class="h-px w-full bg-gradient-to-r from-transparent via-gray-400/40 dark:via-blue-300/20 to-transparent"
                                                style="animation: slideRight 2.5s linear infinite;"></div>
                                            <div class="h-px w-full bg-gradient-to-r from-transparent via-gray-400/40 dark:via-blue-300/20 to-transparent"
                                                style="animation: slideRight 2.5s linear infinite; animation-delay: 0.8s;"></div>
                                            <div class="h-px w-full bg-gradient-to-r from-transparent via-gray-400/40 dark:via-blue-300/20 to-transparent"
                                                style="animation: slideRight 2.5s linear infinite; animation-delay: 1.6s;"></div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Car Animation Container -->
                                <div id="car-animation"
                                    class="w-full h-full opacity-0 transition-opacity duration-500 -mt-28">
                                </div>
                            </div>
                        </div>

                        <!-- Billboard CTA with Rotating Messages (Mobile Only) -->
                        <div class="relative inline-block group mb-12 md:hidden">
                            <!-- Billboard Frame -->
                            <div class="absolute -inset-0.5 bg-gradient-to-r from-yellow-400/20 to-yellow-500/20 rounded-md blur-sm opacity-80 group-hover:blur-md group-hover:opacity-100 transition-all duration-500 animate-pulse"
                                style="animation-duration: 3s;"></div>

                            <!-- Billboard Content -->
                            <div
                                class="relative bg-gradient-to-br from-blue-700/90 to-blue-800/90 text-white rounded-md border-2 border-yellow-400/30 p-4 shadow-lg overflow-hidden h-32 md:h-40 flex flex-col">
                                <!-- Messages Container -->
                                <div class="flex-1 flex flex-col justify-center items-center space-y-2 text-center">
                                    <!-- Messages will cycle through these -->
                                    <div class="message-slide text-yellow-300 font-bold text-lg">
                                        {{ __('home.billboard.freeTrial') }}</div>
                                    <div class="message-slide text-yellow-300 font-bold text-lg"
                                        style="animation-delay: 2s;">{{ __('home.billboard.practiceAnytime') }}</div>
                                    <div class="message-slide text-yellow-300 font-bold text-lg"
                                        style="animation-delay: 4s;">{{ __('home.billboard.momoPay') }}</div>
                                </div>

                                <!-- CTA Button -->
                                <div class="relative z-50 mt-4">
                                    @auth
                                        <a href="/{{ app()->getLocale() }}/dashboard"
                                            class="block w-full text-center bg-gradient-to-r from-yellow-400 to-yellow-500 text-blue-900 font-bold py-2 px-4 rounded hover:from-yellow-300 hover:to-yellow-400 transition-all duration-300 transform group-hover:scale-[1.02] shadow-md hover:shadow-yellow-500/30">
                                            {{ $ctaText }}
                                            <svg class="inline-block ml-2 w-4 h-4" fill="none" stroke="currentColor"
                                                stroke-width="3" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                                            </svg>
                                        </a>
                                    @else
                                        <a href="{{ route('guest-quiz.show', ['locale' => app()->getLocale(), 'quiz' => 11]) }}"
                                            class="block w-full text-center bg-gradient-to-r from-yellow-400 to-yellow-500 text-blue-900 font-bold py-2 px-4 rounded hover:from-yellow-300 hover:to-yellow-400 transition-all duration-300 transform group-hover:scale-[1.02] shadow-md hover:shadow-yellow-500/30">
                                            {{ $ctaText }}
                                            <svg class="inline-block ml-2 w-4 h-4" fill="none" stroke="currentColor"
                                                stroke-width="3" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                                            </svg>
                                        </a>
                                    @endauth
                                </div>
                            </div>

                            <!-- Billboard Pole -->
                            <div
                                class="absolute -bottom-12 left-1/2 transform -translate-x-1/2 w-3 h-12 bg-gradient-to-b from-gray-400 to-gray-600 rounded-b-lg">
                            </div>

                            <style>
                                @keyframes slideInOut {

                                    0%,
                                    100% {
                                        transform: translateY(20px);
                                        opacity: 0;
                                    }

                                    5%,
                                    25% {
                                        transform: translateY(0);
                                        opacity: 1;
                                    }

                                    30% {
                                        transform: translateY(-20px);
                                        opacity: 0;
                                    }
                                }

                                .message-slide {
                                    position: absolute;
                                    width: 100%;
                                    left: 0;
                                    opacity: 0;
                                    animation: slideInOut 6s infinite;
                                }

                                .message-slide:nth-child(1) {
                                    animation-delay: 0s;
                                }

                                .message-slide:nth-child(2) {
                                    animation-delay: 2s;
                                }

                                .message-slide:nth-child(3) {
                                    animation-delay: 4s;
                                }
                            </style>
                        </div>

                        <!-- Bike Animation (Mobile Only) -->
                        <div class="w-full flex justify-center md:hidden">
                            <div class="w-[250px] md:w-[450px] h-[116px] md:h-[220px] relative">
                                <!-- Loading Skeleton -->
                                <div id="bike-loading"
                                    class="absolute inset-0 bg-gradient-to-br from-gray-200/40 to-gray-300/40 dark:from-blue-800/20 dark:to-blue-900/20 rounded-lg overflow-hidden">
                                    <!-- Shimmer overlay -->
                                    <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/30 to-transparent animate-shimmer"></div>
                                    
                                    <!-- Skeleton shapes for bike -->
                                    <div class="absolute inset-0 flex flex-col justify-center items-center gap-3 p-6">
                                        <!-- Main bike body skeleton -->
                                        <div class="w-2/3 h-8 bg-gray-300/60 dark:bg-gray-600/40 rounded-lg animate-pulse"></div>
                                        <!-- Bike details -->
                                        <div class="flex gap-3 w-2/3 justify-center">
                                            <div class="w-6 h-6 bg-gray-300/60 dark:bg-gray-600/40 rounded-full animate-pulse" style="animation-delay: 0.2s;"></div>
                                            <div class="w-12 h-4 bg-gray-300/60 dark:bg-gray-600/40 rounded animate-pulse" style="animation-delay: 0.4s;"></div>
                                            <div class="w-6 h-6 bg-gray-300/60 dark:bg-gray-600/40 rounded-full animate-pulse" style="animation-delay: 0.6s;"></div>
                                        </div>
                                        <!-- Moving lines for motion effect -->
                                        <div class="absolute inset-0 flex flex-col justify-center items-start gap-4 opacity-50">
                                            <div class="h-px w-full bg-gradient-to-r from-transparent via-gray-400/40 dark:via-blue-300/20 to-transparent"
                                                style="animation: slideRight 2.5s linear infinite;"></div>
                                            <div class="h-px w-full bg-gradient-to-r from-transparent via-gray-400/40 dark:via-blue-300/20 to-transparent"
                                                style="animation: slideRight 2.5s linear infinite; animation-delay: 0.8s;"></div>
                                            <div class="h-px w-full bg-gradient-to-r from-transparent via-gray-400/40 dark:via-blue-300/20 to-transparent"
                                                style="animation: slideRight 2.5s linear infinite; animation-delay: 1.6s;"></div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Bike Animation Container -->
                                <div id="bike-animation"
                                    class="w-full h-full opacity-0 transition-opacity duration-500">
                                </div>
                            </div>
                        </div>
                        <div class="hidden md:flex md:items-center md:justify-center md:space-x-4">
                            <!-- Billboard CTA (Desktop) -->
                            <div class="relative inline-block group -mt-8 md:w-72">
                                <!-- Billboard Frame -->
                                <div class="absolute -inset-0.5 bg-gradient-to-r from-yellow-400/20 to-yellow-500/20 rounded-md blur-sm opacity-80 group-hover:blur-md group-hover:opacity-100 transition-all duration-500 animate-pulse"
                                    style="animation-duration: 3s;"></div>

                                <!-- Billboard Content -->
                                <div
                                    class="relative bg-gradient-to-br from-blue-700/90 to-blue-800/90 text-white rounded-md border-2 border-yellow-400/30 p-4 shadow-lg overflow-hidden h-32 md:h-40 flex flex-col">
                                    <!-- Messages Container -->
                                    <div class="flex-1 flex flex-col justify-center items-center space-y-2 text-center">
                                        <!-- Messages will cycle through these -->
                                        <div class="message-slide text-yellow-300 font-bold text-lg">
                                            {{ __('home.billboard.freeTrial') }}</div>
                                        <div class="message-slide text-yellow-300 font-bold text-lg"
                                            style="animation-delay: 2s;">{{ __('home.billboard.practiceAnytime') }}
                                        </div>
                                        <div class="message-slide text-yellow-300 font-bold text-lg"
                                            style="animation-delay: 4s;">{{ __('home.billboard.momoPay') }}</div>
                                    </div>

                                    <!-- CTA Button -->
                                    <div class="relative z-50 mt-4">
                                        @auth
                                            <a href="/{{ app()->getLocale() }}/dashboard"
                                                class="block w-full text-center bg-gradient-to-r from-yellow-400 to-yellow-500 text-blue-900 font-bold py-2 px-4 rounded hover:from-yellow-300 hover:to-yellow-400 transition-all duration-300 transform group-hover:scale-[1.02] shadow-md hover:shadow-yellow-500/30">
                                                {{ $ctaText }}
                                                <svg class="inline-block ml-2 w-4 h-4" fill="none"
                                                    stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                                                </svg>
                                            </a>
                                        @else
                                            <a href="{{ route('guest-quiz.show', ['locale' => app()->getLocale(), 'quiz' => 11]) }}"
                                                class="block w-full text-center bg-gradient-to-r from-yellow-400 to-yellow-500 text-blue-900 font-bold py-2 px-4 rounded hover:from-yellow-300 hover:to-yellow-400 transition-all duration-300 transform group-hover:scale-[1.02] shadow-md hover:shadow-yellow-500/30">
                                                {{ $ctaText }}
                                                <svg class="inline-block ml-2 w-4 h-4" fill="none"
                                                    stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                                                </svg>
                                            </a>
                                        @endauth
                                    </div>
                                </div>

                                <!-- Billboard Pole -->
                                <div
                                    class="absolute -bottom-12 left-1/2 transform -translate-x-1/2 w-3 h-12 bg-gradient-to-b from-gray-400 to-gray-600 rounded-b-lg">
                                </div>
                            </div>

                            <!-- Bike Animation (Desktop) -->
                            <div class="w-[250px] md:w-[450px] h-[180px] md:h-[220px] relative">
                                <!-- Loading Skeleton -->
                                <div id="bike-loading-desktop"
                                    class="absolute inset-0 bg-gradient-to-br from-gray-200/40 to-gray-300/40 dark:from-blue-800/20 dark:to-blue-900/20 rounded-lg overflow-hidden">
                                    <!-- Shimmer overlay -->
                                    <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/30 to-transparent animate-shimmer"></div>
                                    
                                    <!-- Skeleton shapes for bike -->
                                    <div class="absolute inset-0 flex flex-col justify-center items-center gap-4 p-8">
                                        <!-- Main bike body skeleton -->
                                        <div class="w-2/3 h-10 bg-gray-300/60 dark:bg-gray-600/40 rounded-lg animate-pulse"></div>
                                        <!-- Bike details -->
                                        <div class="flex gap-4 w-2/3 justify-center">
                                            <div class="w-8 h-8 bg-gray-300/60 dark:bg-gray-600/40 rounded-full animate-pulse" style="animation-delay: 0.2s;"></div>
                                            <div class="w-16 h-6 bg-gray-300/60 dark:bg-gray-600/40 rounded animate-pulse" style="animation-delay: 0.4s;"></div>
                                            <div class="w-8 h-8 bg-gray-300/60 dark:bg-gray-600/40 rounded-full animate-pulse" style="animation-delay: 0.6s;"></div>
                                        </div>
                                        <!-- Moving lines for motion effect -->
                                        <div class="absolute inset-0 flex flex-col justify-center items-start gap-6 opacity-50">
                                            <div class="h-px w-full bg-gradient-to-r from-transparent via-gray-400/40 dark:via-blue-300/20 to-transparent"
                                                style="animation: slideRight 2.5s linear infinite;"></div>
                                            <div class="h-px w-full bg-gradient-to-r from-transparent via-gray-400/40 dark:via-blue-300/20 to-transparent"
                                                style="animation: slideRight 2.5s linear infinite; animation-delay: 0.8s;"></div>
                                            <div class="h-px w-full bg-gradient-to-r from-transparent via-gray-400/40 dark:via-blue-300/20 to-transparent"
                                                style="animation: slideRight 2.5s linear infinite; animation-delay: 1.6s;"></div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Bike Animation Container -->
                                <div id="bike-animation-desktop"
                                    class="w-full h-full opacity-0 transition-opacity duration-500">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Animation styles and scripts -->
                <style>
                    @keyframes groundMove {
                        0% {
                            transform: translateX(-20%);
                        }

                        100% {
                            transform: translateX(20%);
                        }
                    }

                    @keyframes roadLines {
                        0% {
                            transform: translateX(-100%);
                        }

                        100% {
                            transform: translateX(100%);
                        }
                    }

                    @keyframes slideRight {
                        0% {
                            transform: translateX(-100%);
                            opacity: 0;
                        }

                        10% {
                            opacity: 0.3;
                        }

                        90% {
                            opacity: 0.3;
                        }

                        100% {
                            transform: translateX(100%);
                            opacity: 0;
                        }
                    }

                    @keyframes shimmer {
                        0% {
                            transform: translateX(-100%);
                        }

                        100% {
                            transform: translateX(100%);
                        }
                    }

                    .animate-shimmer {
                        animation: shimmer 2s ease-in-out infinite;
                        background-size: 200% 100%;
                    }

                    .animate-groundMove {
                        animation: groundMove 20s linear infinite alternate;
                        will-change: transform;
                    }

                    .animate-roadLines {
                        animation: roadLines 20s linear infinite;
                        will-change: transform;
                    }

                    .animation-duration-20s {
                        animation-duration: 20s !important;
                    }

                    .animation-delay-5s {
                        animation-delay: 5s !important;
                    }

                    .animation-delay-10s {
                        animation-delay: 10s !important;
                    }
                </style>

                <!-- Lottie Library -->
                <link rel="preconnect" href="https://cdnjs.cloudflare.com">
                <link rel="preload" as="script"
                    href="https://cdnjs.cloudflare.com/ajax/libs/lottie-web/5.12.2/lottie.min.js">
                <script src="https://cdnjs.cloudflare.com/ajax/libs/lottie-web/5.12.2/lottie.min.js" defer></script>

                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        // Initialize separated animations
                        let carAnimation, bikeAnimation;

                        function initAnimations() {
                            // Load car animation
                            carAnimation = lottie.loadAnimation({
                                container: document.getElementById('car-animation'),
                                renderer: 'svg',
                                loop: true,
                                autoplay: true,
                                path: '{{ asset('json/car-moving.json') }}?v=' + Date.now(),
                                rendererSettings: {
                                    preserveAspectRatio: 'xMidYMid meet',
                                    progressiveLoad: false
                                }
                            });

                            // Load bike animation for mobile
                            bikeAnimation = lottie.loadAnimation({
                                container: document.getElementById('bike-animation'),
                                renderer: 'svg',
                                loop: true,
                                autoplay: true,
                                path: '{{ asset('json/bike-moving.json') }}?v=' + Date.now(),
                                rendererSettings: {
                                    preserveAspectRatio: 'xMidYMid meet',
                                    progressiveLoad: false
                                }
                            });

                            // Load bike animation for desktop
                            const bikeAnimationDesktop = lottie.loadAnimation({
                                container: document.getElementById('bike-animation-desktop'),
                                renderer: 'svg',
                                loop: true,
                                autoplay: true,
                                path: '{{ asset('json/bike-moving.json') }}?v=' + Date.now(),
                                rendererSettings: {
                                    preserveAspectRatio: 'xMidYMid meet',
                                    progressiveLoad: false
                                }
                            });

                            // Apply mobile scaling
                            const isMobile = window.innerWidth < 768;

                            carAnimation.addEventListener('DOMLoaded', function() {
                                const container = document.getElementById('car-animation');
                                const loadingElement = document.getElementById('car-loading');

                                container.style.width = '100%';
                                container.style.height = 'auto';

                                if (isMobile) {
                                    container.style.transform = 'scale(1.5)';
                                    container.style.transformOrigin = 'center top';
                                }

                                // Hide loading state and show animation
                                if (loadingElement) loadingElement.style.display = 'none';
                                container.style.opacity = '1';
                            });

                            bikeAnimation.addEventListener('DOMLoaded', function() {
                                const container = document.getElementById('bike-animation');
                                const loadingElement = document.getElementById('bike-loading');

                                container.style.width = '100%';
                                container.style.height = 'auto';

                                if (isMobile) {
                                    container.style.transform = 'scale(1.5)';
                                    container.style.transformOrigin = 'center bottom';
                                }

                                // Hide loading state and show animation
                                if (loadingElement) loadingElement.style.display = 'none';
                                container.style.opacity = '1';
                            });

                            // Desktop bike animation event listener
                            bikeAnimationDesktop.addEventListener('DOMLoaded', function() {
                                const container = document.getElementById('bike-animation-desktop');
                                const loadingElement = document.getElementById('bike-loading-desktop');

                                container.style.width = '100%';
                                container.style.height = 'auto';

                                // Desktop doesn't need scaling

                                // Hide loading state and show animation
                                if (loadingElement) loadingElement.style.display = 'none';
                                container.style.opacity = '1';
                            });
                        }

                        // Initialize when Lottie is loaded
                        function checkLottieAndInit() {
                            if (window.lottie && window.lottie.loadAnimation) {
                                setTimeout(initAnimations, 100);
                            } else {
                                setTimeout(checkLottieAndInit, 100);
                            }
                        }

                        checkLottieAndInit();
                    });
                </script>
            </div>
            <!-- Mobile Quiz Section (visible only on mobile) -->
            <div class="w-full lg:hidden mt-2">
                @if ($hasGuestQuiz)
                    <div class="h-full">
                        @include('components.home.guest-quiz-section', [
                            'guestQuiz' => $guestQuiz,
                        ])
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
</div>


<!-- Decorative Elements -->
<div class="absolute bottom-0 left-0 right-0 h-12 bg-gradient-to-t from-white/10 dark:from-gray-900/50 to-transparent">
</div>
</div>
