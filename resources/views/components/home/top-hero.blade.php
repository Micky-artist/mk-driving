@props(['title', 'subtitle', 'ctaText', 'ctaUrl', 'quizzes' => collect([])])

@php
    $guestQuiz = $quizzes->first(fn($q) => $q['is_guest_quiz'] ?? false);
    $hasGuestQuiz = $guestQuiz !== null;

    // Debug logging
    \Illuminate\Support\Facades\Log::debug('Top-hero component debug', [
        'quizzes_count' => $quizzes->count(),
        'guest_quiz_found' => $hasGuestQuiz,
        'guest_quiz_id' => $guestQuiz['id'] ?? null,
        'guest_quiz_title' => $guestQuiz['title'] ?? null,
    ]);
@endphp

<div
    class="relative w-full bg-gradient-to-br from-blue-50 to-blue-100 dark:from-gray-900 dark:to-gray-800 overflow-hidden">

    <!-- Animated Background Elements -->
    <div class="absolute inset-0 opacity-10">
        <div
            class="absolute inset-0 bg-grid-white/[0.05] [mask-image:linear-gradient(to_bottom,transparent,white,transparent)]">
        </div>
    </div>

    <div class="relative max-w-7xl w-full mx-auto px-2 sm:px-4 lg:px-8 overflow-visible py-6 lg:py-8">
        <!-- Desktop Layout: Full Width Animations -->
        <div class="hidden lg:grid lg:grid-cols-12 lg:items-center lg:gap-8 relative">
            <!-- Left: Car Animation -->
            <div class="col-span-4 flex justify-start items-center">
                <div class="w-[280px] md:w-[500px] h-[120px] md:h-[150px] relative overflow-hidden">
                    <!-- Loading Skeleton -->
                    <div id="car-loading-desktop"
                        class="absolute pt-4 inset-0 bg-gradient-to-br from-gray-200/40 to-gray-300/40 dark:from-blue-800/20 dark:to-blue-900/20 rounded-lg overflow-hidden">

                        <!-- Skeleton shapes for car -->
                        <div class="absolute inset-0 flex flex-col justify-center items-center gap-4 p-8">
                            <!-- Main car body skeleton -->
                            <div class="w-3/4 h-12 bg-gray-300/60 dark:bg-gray-600/40 rounded-lg animate-pulse"></div>
                            <!-- Car details -->
                            <div class="flex gap-4 w-3/4 justify-center">
                                <div class="w-8 h-8 bg-gray-300/60 dark:bg-gray-600/40 rounded-full animate-pulse"
                                    style="animation-delay: 0.2s;"></div>
                                <div class="w-16 h-6 bg-gray-300/60 dark:bg-gray-600/40 rounded animate-pulse"
                                    style="animation-delay: 0.4s;"></div>
                                <div class="w-8 h-8 bg-gray-300/60 dark:bg-gray-600/40 rounded-full animate-pulse"
                                    style="animation-delay: 0.6s;"></div>
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
                    <div id="car-animation-desktop"
                        class="w-full h-full opacity-0 transition-opacity duration-500 scale-[300%] -translate-y-8">
                    </div>
                </div>
            </div>

            <!-- Center: Billboard CTA -->
            <div class="col-span-4 flex justify-center items-center px-4">
                <div class="relative inline-block group w-full max-w-2xl">
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
                            <div class="message-slide text-yellow-300 font-bold text-lg" style="animation-delay: 2s;">
                                {{ __('home.billboard.practiceAnytime') }}</div>
                            <div class="message-slide text-yellow-300 font-bold text-lg" style="animation-delay: 4s;">
                                {{ __('home.billboard.momoPay') }}</div>
                        </div>

                        <!-- CTA Button -->
                        <div class="relative z-10 mt-4">
                            @auth
                                <a href="{{ route('dashboard', ['locale' => app()->getLocale() ?: config('app.fallback_locale', 'en')]) }}"
                                    class="block w-full text-center bg-gradient-to-r from-yellow-400 to-yellow-500 text-blue-900 font-bold py-2 px-4 rounded hover:from-yellow-300 hover:to-yellow-400 transition-all duration-300 transform group-hover:scale-[1.02] shadow-md hover:shadow-yellow-500/30">
                                    {{ $ctaText }}
                                    <svg class="inline-block ml-2 w-4 h-4" fill="none" stroke="currentColor"
                                        stroke-width="3" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3">
                                        </path>
                                    </svg>
                                </a>
                            @else
                                @if ($hasGuestQuiz)
                                    <a href="{{ route('guest-quiz.show', ['locale' => app()->getLocale() ?: config('app.fallback_locale', 'en'), 'quiz' => $guestQuiz['id']]) }}"
                                        class="block w-full text-center bg-gradient-to-r from-yellow-400 to-yellow-500 text-blue-900 font-bold py-2 px-4 rounded hover:from-yellow-300 hover:to-yellow-400 transition-all duration-300 transform group-hover:scale-[1.02] shadow-md hover:shadow-yellow-500/30">
                                        {{ $ctaText }}
                                        <svg class="inline-block ml-2 w-4 h-4" fill="none" stroke="currentColor"
                                            stroke-width="3" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M17 8l4 4m0 0l-4 4m4-4H3">
                                            </path>
                                        </svg>
                                    </a>
                                @else
                                    <a href="#" onclick="event.preventDefault(); return false;"
                                        class="block w-full text-center bg-gradient-to-r from-yellow-400 to-yellow-500 text-blue-900 font-bold py-2 px-4 rounded hover:from-yellow-300 hover:to-yellow-400 transition-all duration-300 transform group-hover:scale-[1.02] shadow-md hover:shadow-yellow-500/30 opacity-75 cursor-not-allowed">
                                        {{ $ctaText }}
                                        <svg class="inline-block ml-2 w-4 h-4" fill="none" stroke="currentColor"
                                            stroke-width="3" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M17 8l4 4m0 0l-4 4m4-4H3">
                                            </path>
                                        </svg>
                                    </a>
                                @endif
                            @endauth
                        </div>
                    </div>

                    <!-- Billboard Pole -->
                    <div
                        class="absolute -bottom-12 left-1/2 transform -translate-x-1/2 w-3 h-12 bg-gradient-to-b from-gray-400 to-gray-600 rounded-b-lg">
                    </div>
                </div>
            </div>

            <!-- Right: Bike Animation -->
            <div class="col-span-4 flex justify-end items-center">
                <div class="w-[250px] md:w-[450px] h-[120px] md:h-[150px] relative overflow-hidden">
                    <!-- Loading Skeleton -->
                    <div id="bike-loading-desktop"
                        class="absolute inset-0 bg-gradient-to-br from-gray-200/40 to-gray-300/40 dark:from-blue-800/20 dark:to-blue-900/20 rounded-lg overflow-hidden">

                        <!-- Skeleton shapes for bike -->
                        <div class="absolute inset-0 flex flex-col justify-center items-center gap-4 p-8">
                            <!-- Main bike body skeleton -->
                            <div class="w-2/3 h-10 bg-gray-300/60 dark:bg-gray-600/40 rounded-lg animate-pulse"></div>
                            <!-- Bike details -->
                            <div class="flex gap-4 w-2/3 justify-center">
                                <div class="w-8 h-8 bg-gray-300/60 dark:bg-gray-600/40 rounded-full animate-pulse"
                                    style="animation-delay: 0.2s;"></div>
                                <div class="w-16 h-6 bg-gray-300/60 dark:bg-gray-600/40 rounded animate-pulse"
                                    style="animation-delay: 0.4s;"></div>
                                <div class="w-8 h-8 bg-gray-300/60 dark:bg-gray-600/40 rounded-full animate-pulse"
                                    style="animation-delay: 0.6s;"></div>
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
                        class="w-full h-full opacity-0 transition-opacity duration-500 scale-[235%] translate-y-8">
                    </div>
                </div>
            </div>
        </div>

        <!-- Mobile Layout: Grid 33% each, 1 column (3 rows) -->
        <div class="lg:hidden grid grid-cols-1 items-center gap-2 py-2">
            <!-- Row 1: Car Animation -->
            <div class="flex justify-center items-center">
                <div class="w-[320px] h-[140px] relative overflow-hidden">
                    <!-- Loading Skeleton -->
                    <div id="car-loading"
                        class="absolute pt-4 inset-0 bg-gradient-to-br from-gray-200/40 to-gray-300/40 dark:from-blue-800/20 dark:to-blue-900/20 rounded-lg overflow-hidden">

                        <!-- Skeleton shapes for car -->
                        <div class="absolute inset-0 flex flex-col justify-center items-center gap-4 p-8">
                            <!-- Main car body skeleton -->
                            <div class="w-3/4 h-12 bg-gray-300/60 dark:bg-gray-600/40 rounded-lg animate-pulse"></div>
                            <!-- Car details -->
                            <div class="flex gap-4 w-3/4 justify-center">
                                <div class="w-8 h-8 bg-gray-300/60 dark:bg-gray-600/40 rounded-full animate-pulse"
                                    style="animation-delay: 0.2s;"></div>
                                <div class="w-16 h-6 bg-gray-300/60 dark:bg-gray-600/40 rounded animate-pulse"
                                    style="animation-delay: 0.4s;"></div>
                                <div class="w-8 h-8 bg-gray-300/60 dark:bg-gray-600/40 rounded-full animate-pulse"
                                    style="animation-delay: 0.6s;"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Car Animation Container -->
                    <div id="car-animation" class="w-full h-full opacity-0 transition-opacity duration-500 scale-[300%] -translate-y-12">
                    </div>
                </div>
            </div>

            <!-- Row 2: Billboard CTA -->
            <div class="flex justify-center items-center px-4">
                <div class="relative inline-block group w-full max-w-md">
                    <!-- Billboard Frame -->
                    <div class="absolute -inset-0.5 bg-gradient-to-r from-yellow-400/20 to-yellow-500/20 rounded-md blur-sm opacity-80 group-hover:blur-md group-hover:opacity-100 transition-all duration-500 animate-pulse"
                        style="animation-duration: 3s;"></div>

                    <!-- Billboard Content -->
                    <div
                        class="relative bg-gradient-to-br from-blue-700/90 to-blue-800/90 text-white rounded-md border-2 border-yellow-400/30 p-4 shadow-lg overflow-hidden h-32 flex flex-col">
                        <!-- Messages Container -->
                        <div class="flex-1 flex flex-col justify-center items-center space-y-2 text-center">
                            <!-- Messages will cycle through these -->
                            <div class="message-slide text-yellow-300 font-bold text-sm">
                                {{ __('home.billboard.freeTrial') }}</div>
                            <div class="message-slide text-yellow-300 font-bold text-sm" style="animation-delay: 2s;">
                                {{ __('home.billboard.practiceAnytime') }}</div>
                            <div class="message-slide text-yellow-300 font-bold text-sm" style="animation-delay: 4s;">
                                {{ __('home.billboard.momoPay') }}</div>
                        </div>

                        <!-- CTA Button -->
                        <div class="relative z-10 mt-3">
                            @auth
                                <a href="{{ route('dashboard', ['locale' => app()->getLocale() ?: config('app.fallback_locale', 'en')]) }}"
                                    class="block w-full text-center bg-gradient-to-r from-yellow-400 to-yellow-500 text-blue-900 font-bold py-2 px-4 rounded hover:from-yellow-300 hover:to-yellow-400 transition-all duration-300 transform group-hover:scale-[1.02] shadow-md hover:shadow-yellow-500/30">
                                    {{ $ctaText }}
                                    <svg class="inline-block ml-2 w-4 h-4" fill="none" stroke="currentColor"
                                        stroke-width="3" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3">
                                        </path>
                                    </svg>
                                </a>
                            @else
                                @if ($hasGuestQuiz)
                                    <a href="{{ route('guest-quiz.show', ['locale' => app()->getLocale() ?: config('app.fallback_locale', 'en'), 'quiz' => $guestQuiz['id']]) }}"
                                        class="block w-full text-center bg-gradient-to-r from-yellow-400 to-yellow-500 text-blue-900 font-bold py-2 px-4 rounded hover:from-yellow-300 hover:to-yellow-400 transition-all duration-300 transform group-hover:scale-[1.02] shadow-md hover:shadow-yellow-500/30">
                                        {{ $ctaText }}
                                        <svg class="inline-block ml-2 w-4 h-4" fill="none" stroke="currentColor"
                                            stroke-width="3" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3">
                                            </path>
                                        </svg>
                                    </a>
                                @else
                                    <a href="#" onclick="event.preventDefault(); return false;"
                                        class="block w-full text-center bg-gradient-to-r from-yellow-400 to-yellow-500 text-blue-900 font-bold py-2 px-4 rounded hover:from-yellow-300 hover:to-yellow-400 transition-all duration-300 transform group-hover:scale-[1.02] shadow-md hover:shadow-yellow-500/30 opacity-75 cursor-not-allowed">
                                        {{ $ctaText }}
                                        <svg class="inline-block ml-2 w-4 h-4" fill="none" stroke="currentColor"
                                            stroke-width="3" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3">
                                            </path>
                                        </svg>
                                    </a>
                                @endif
                            @endauth
                        </div>
                    </div>

                    <!-- Billboard Pole -->
                    <div
                        class="absolute -bottom-10 left-1/2 transform -translate-x-1/2 w-3 h-10 bg-gradient-to-b from-gray-400 to-gray-600 rounded-b-lg">
                    </div>
                </div>
            </div>

            <!-- Row 3: Bike Animation -->
            <div class="flex justify-center items-center">
                <div class="w-[320px] h-[140px] relative overflow-hidden">
                    <!-- Loading Skeleton -->
                    <div id="bike-loading"
                        class="absolute inset-0 bg-gradient-to-br from-gray-200/40 to-gray-300/40 dark:from-blue-800/20 dark:to-blue-900/20 rounded-lg overflow-hidden">

                        <!-- Skeleton shapes for bike -->
                        <div class="absolute inset-0 flex flex-col justify-center items-center gap-4 p-8">
                            <!-- Main bike body skeleton -->
                            <div class="w-2/3 h-10 bg-gray-300/60 dark:bg-gray-600/40 rounded-lg animate-pulse"></div>
                            <!-- Bike details -->
                            <div class="flex gap-4 w-2/3 justify-center">
                                <div class="w-8 h-8 bg-gray-300/60 dark:bg-gray-600/40 rounded-full animate-pulse"
                                    style="animation-delay: 0.2s;"></div>
                                <div class="w-16 h-6 bg-gray-300/60 dark:bg-gray-600/40 rounded animate-pulse"
                                    style="animation-delay: 0.4s;"></div>
                                <div class="w-8 h-8 bg-gray-300/60 dark:bg-gray-600/40 rounded-full animate-pulse"
                                    style="animation-delay: 0.6s;"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Bike Animation Container -->
                    <div id="bike-animation"
                        class="w-full h-full opacity-0 transition-opacity duration-500 scale-[245%] translate-y-8">
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

        <!-- Lottie Library -->
        <link rel="preconnect" href="https://cdnjs.cloudflare.com">
        <link rel="preload" as="script"
            href="https://cdnjs.cloudflare.com/ajax/libs/lottie-web/5.12.2/lottie.min.js">
        <script src="https://cdnjs.cloudflare.com/ajax/libs/lottie-web/5.12.2/lottie.min.js" defer></script>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Cache configuration
                const CACHE_VERSION = 'v1';
                const CACHE_DURATION = 24 * 60 * 60 * 1000; // 24 hours
                const CACHE_KEYS = {
                    carAnimation: `car-animation-${CACHE_VERSION}`,
                    bikeAnimation: `bike-animation-${CACHE_VERSION}`
                };

                // Basic animation optimization settings
                const animationConfig = {
                    autoPlay: true,
                    loop: true,
                    renderer: 'svg',
                    rendererSettings: {
                        progressiveLoad: true,
                        hideOnTransparent: true,
                        preserveAspectRatio: 'xMidYMid meet'
                    }
                };

                // Check if cached data is still valid
                function isCacheValid(timestamp) {
                    return Date.now() - timestamp < CACHE_DURATION;
                }

                // Load animation data from cache or fetch
                async function loadAnimationData(url, cacheKey) {
                    try {
                        // Check localStorage first
                        const cached = localStorage.getItem(cacheKey);
                        if (cached) {
                            const {
                                data,
                                timestamp
                            } = JSON.parse(cached);
                            if (isCacheValid(timestamp)) {
                                console.log('Loading animation from cache:', cacheKey);
                                return data;
                            }
                        }

                        // Fetch fresh data
                        console.log('Fetching animation from network:', url);
                        const response = await fetch(url, {
                            cache: 'force-cache',
                            headers: {
                                'Cache-Control': 'max-age=86400'
                            }
                        });

                        if (!response.ok) {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }

                        const data = await response.json();

                        // Cache the response
                        localStorage.setItem(cacheKey, JSON.stringify({
                            data: data,
                            timestamp: Date.now()
                        }));

                        return data;
                    } catch (error) {
                        console.error('Failed to load animation data:', error);

                        // Fallback to cached data even if expired
                        const cached = localStorage.getItem(cacheKey);
                        if (cached) {
                            const {
                                data
                            } = JSON.parse(cached);
                            console.warn('Using expired cached data as fallback');
                            return data;
                        }

                        throw error;
                    }
                }

                // Load and initialize animation
                async function initAnimation(containerId, jsonUrl, cacheKey) {
                    try {
                        const container = document.getElementById(containerId);
                        if (!container) {
                            console.warn(`Animation container not found: ${containerId}`);
                            return;
                        }

                        // Hide loading skeleton
                        const loadingElement = document.getElementById(containerId.replace('-animation',
                            '-loading'));
                        if (loadingElement) {
                            loadingElement.style.display = 'none';
                        }

                        // Load animation data
                        const animationData = await loadAnimationData(jsonUrl, cacheKey);

                        // Initialize Lottie animation
                        const animation = window.lottie.loadAnimation({
                            container: container,
                            renderer: animationConfig.renderer,
                            loop: animationConfig.loop,
                            autoplay: animationConfig.autoPlay,
                            animationData: animationData,
                            rendererSettings: animationConfig.rendererSettings
                        });

                        // Show animation container
                        container.style.opacity = '1';

                        // Performance optimization: pause when not visible
                        const observer = new IntersectionObserver((entries) => {
                            entries.forEach(entry => {
                                if (entry.isIntersecting) {
                                    animation.play();
                                } else {
                                    animation.pause();
                                }
                            });
                        }, {
                            threshold: 0.1
                        });

                        observer.observe(container);

                        return animation;
                    } catch (error) {
                        console.error(`Failed to initialize animation ${containerId}:`, error);

                        // Show loading skeleton as fallback
                        const loadingElement = document.getElementById(containerId.replace('-animation',
                            '-loading'));
                        if (loadingElement) {
                            loadingElement.style.display = 'block';
                        }
                    }
                }

                function initAnimations() {
                    // Only load animations for current device size
                    const isDesktop = window.innerWidth >= 1024; // lg breakpoint

                    if (isDesktop) {
                        // Load desktop animations
                        initAnimation('car-animation-desktop', '/json/car-moving.json', CACHE_KEYS.carAnimation);
                        initAnimation('bike-animation-desktop', '/json/bike-moving.json', CACHE_KEYS.bikeAnimation);
                    } else {
                        // Load mobile animations
                        initAnimation('car-animation', '/json/car-moving.json', CACHE_KEYS.carAnimation);
                        initAnimation('bike-animation', '/json/bike-moving.json', CACHE_KEYS.bikeAnimation);
                    }
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
</div>

<!-- Decorative Elements -->
<div class="absolute bottom-0 left-0 right-0 h-12 bg-gradient-to-t from-white/10 dark:from-gray-900/50 to-transparent">
</div>
</div>
