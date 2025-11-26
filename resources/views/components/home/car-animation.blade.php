<div class="relative w-full overflow-hidden bg-gradient-to-br from-blue-50 to-white dark:from-gray-900 dark:to-gray-800">
    <!-- Moving dashed road lines (3 lanes) -->
    <div class="absolute inset-0 opacity-70 pointer-events-none">
        <div class="absolute inset-0 flex flex-col justify-center items-start gap-16">
            <div class="h-1 bg-gray-400/80 dark:bg-gray-800/60 w-full animate-roadLines animation-duration-1600"></div>
            <div
                class="h-1 bg-gray-400/80 dark:bg-gray-800/60 w-full animate-roadLines animation-duration-1600 animation-delay-500">
            </div>
            <div
                class="h-1 bg-gray-400/80 dark:bg-gray-800/60 w-full animate-roadLines animation-duration-1600 animation-delay-1000">
            </div>
        </div>
    </div>

    <!-- Subtle moving ground layers -->
    <div class="absolute left-0 top-0 bottom-0 w-2/3 pointer-events-none overflow-hidden">
        <!-- Base layer - lightest shade with smoother gradient -->
        <div class="absolute inset-0 bg-gradient-to-r from-gray-300/20 via-transparent to-transparent dark:from-gray-800/30 dark:via-transparent animate-groundMove"
            style="animation-delay: 0s; mask-image: linear-gradient(to right, black, transparent);"></div>
        <!-- Mid layer - medium shade with smoother gradient -->
        <div class="absolute inset-0 bg-gradient-to-r from-gray-400/25 via-transparent to-transparent dark:from-gray-700/40 dark:via-transparent animate-groundMove"
            style="animation-duration: 8s; animation-delay: -2s; mask-image: linear-gradient(to right, black, transparent);">
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="text-center -mb-6 sm:-mb-8">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white sm:text-3xl">
                {{ __('home.car_animation.title') }}
            </h2>
            <p class="mt-1 text-gray-600 dark:text-gray-300">
                {{ __('home.car_animation.subtitle') }}
            </p>
        </div>

        <!-- Car Animation Container -->
        <div class="relative h-64 sm:h-80 md:h-96 flex items-center justify-center">
            <!-- Road -->
            <div class="absolute bottom-12 md:bottom-20 left-0 right-0 h-4 bg-gray-300 dark:bg-gray-800">
                <!-- Road edge shadow -->
                <div class="absolute -top-1 left-0 right-0 h-2 bg-gradient-to-b dark:from-gray-900/20 from-gray-500/20 to-transparent"></div>
            </div>

            <!-- Car SVG (facing left) - positioned to sit on the road -->
            <div class="relative z-20 w-80 sm:w-96 md:w-[32rem] -mb-4 translate-x-4">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 240 140" width="100%" height="100%"
                    class="drop-shadow-2xl scale-110 sm:scale-100">
                    <defs>
                        <!-- Headlight gradient -->
                        <radialGradient id="headlightGlow" cx="30%" cy="30%">
                            <stop offset="0%" stop-color="#ffffff" />
                            <stop offset="50%" stop-color="#fef3c7" />
                            <stop offset="100%" stop-color="#fbbf24" />
                        </radialGradient>

                        <!-- Taillight gradient -->
                        <radialGradient id="taillightGlow" cx="30%" cy="30%">
                            <stop offset="0%" stop-color="#fca5a5" />
                            <stop offset="50%" stop-color="#dc2626" />
                            <stop offset="100%" stop-color="#991b1b" />
                        </radialGradient>
                    </defs>

                    <g>
                        <!-- Main body -->
                        <rect x="40" y="50" width="140" height="45" rx="4"
                            class="fill-blue-900 dark:fill-blue-700" />

                        <!-- Roof/cabin -->
                        <rect x="70" y="25" width="108" height="25" rx="6"
                            class="fill-blue-900 dark:fill-blue-700" style="border-top-left-radius: 15px;" />

                        <!-- Windshield (front window) -->
                        <path d="M 60 50 L 70 30 L 95 30 L 95 50 Z" class="fill-blue-200 dark:fill-blue-300" />

                        <!-- Rear window -->
                        <rect x="135" y="30" width="25" height="18" rx="2"
                            class="fill-blue-200 dark:fill-blue-300" />

                        <!-- Door lines -->
                        <line x1="96" y1="50" x2="96" y2="95"
                            class="stroke-gray-900 dark:stroke-gray-700" stroke-width="0.5" />
                        <line x1="135" y1="50" x2="135" y2="95"
                            class="stroke-gray-900 dark:stroke-gray-700" stroke-width="0.5" />

                        <!-- Side mirror (driver side, near windshield) -->
                        <ellipse cx="62" cy="52" rx="4" ry="2.5"
                            class="fill-gray-900 dark:fill-gray-700" transform="rotate(-5, 62, 52)" />

                        <!-- Front of car - Headlights and grille (right side) -->
                        <rect x="37" y="60" width="8" height="25" rx="2"
                            class="fill-gray-900 dark:fill-gray-800" />

                        <!-- Modern LED headlight (larger, bright) -->
                        <ellipse cx="41" cy="68" rx="3.5" ry="5"
                            fill="url(#headlightGlow)" />

                        <!-- Lower fog light -->
                        <ellipse cx="41" cy="78" rx="2.5" ry="3" fill="#fef3c7"
                            opacity="0.9" />

                        <!-- LED strip accent -->
                        <rect x="38.5" y="84" width="6" height="1" rx="0.5" fill="white"
                            opacity="0.8" />

                        <!-- Rear of car - Taillights (left side) -->
                        <rect x="175" y="60" width="8" height="25" rx="2"
                            class="fill-gray-900 dark:fill-gray-800" />

                        <!-- Vertical brake light (red) -->
                        <rect x="176.5" y="62" width="5" height="14" rx="1"
                            fill="url(#taillightGlow)" />

                        <!-- Reverse/turn signal light (amber/white) -->
                        <rect x="176.5" y="78" width="5" height="6" rx="0.8" fill="#fef3c7"
                            opacity="0.9" />

                        <!-- Exhaust pipe -->
                        <ellipse cx="32" cy="93" rx="2" ry="1.5" fill="#374151"
                            stroke="#1f2937" stroke-width="0.5" />
                    </g>

                    <!-- Wheels with animations -->
                    <g transform="translate(150, 95)">
                        <circle r="18" class="fill-gray-800 dark:fill-gray-900" />
                        <circle r="12" class="fill-gray-600 dark:fill-gray-700" />
                        <circle r="5" class="fill-gray-900 dark:fill-gray-200" />
                        <g class="stroke-gray-400 dark:stroke-gray-300">
                            <line x1="-10" y1="0" x2="10" y2="0" stroke-width="2.5" />
                            <line x1="0" y1="-10" x2="0" y2="10" stroke-width="2.5" />
                            <line x1="-7" y1="-7" x2="7" y2="7" stroke-width="2" />
                            <line x1="-7" y1="7" x2="7" y2="-7" stroke-width="2" />
                            <animateTransform attributeName="transform" attributeType="XML" type="rotate"
                                from="360 0 0" to="0 0 0" dur="0.8s" repeatCount="indefinite" />
                        </g>
                    </g>

                    <g transform="translate(65, 95)">
                        <circle r="18" class="fill-gray-800 dark:fill-gray-900" />
                        <circle r="12" class="fill-gray-600 dark:fill-gray-700" />
                        <circle r="5" class="fill-gray-900 dark:fill-gray-200" />
                        <g class="stroke-gray-400 dark:stroke-gray-300">
                            <line x1="-10" y1="0" x2="10" y2="0" stroke-width="2.5" />
                            <line x1="0" y1="-10" x2="0" y2="10" stroke-width="2.5" />
                            <line x1="-7" y1="-7" x2="7" y2="7" stroke-width="2" />
                            <line x1="-7" y1="7" x2="7" y2="-7" stroke-width="2" />
                            <animateTransform attributeName="transform" attributeType="XML" type="rotate"
                                from="360 0 0" to="0 0 0" dur="0.8s" repeatCount="indefinite" />
                        </g>
                    </g>
                </svg>
            </div>
        </div>

        <!-- CTA Button -->
        <div class="text-center -mt-6">
            <a href="{{ route('register', app()->getLocale()) }}"
                class="inline-flex items-center justify-center px-8 py-3 border border-transparent text-base font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 md:py-4 md:text-lg md:px-10 transition-colors duration-300">
                {{ __('home.car_animation.cta') }}
                <svg class="ml-2 -mr-1 w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M10.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L12.586 11H5a1 1 0 110-2h7.586l-2.293-2.293a1 1 0 010-1.414z"
                        clip-rule="evenodd" />
                </svg>
            </a>
        </div>
    </div>

    <!-- Moving road effect -->
    <div class="absolute bottom-0 left-0 right-0 h-1/3 bg-gradient-to-t from-gray-800 to-transparent opacity-30"></div>
</div>

@push('styles')
    <style>
        @keyframes float {

            0%,
            100% {
                transform: translateY(0) rotate(0deg);
            }

            50% {
                transform: translateY(-5px) rotate(0.5deg);
            }
        }

        /* Moving dashed road lines */
        @keyframes roadLines {
            from {
                transform: translateX(120%);
            }

            to {
                transform: translateX(-120%);
            }
        }

        /* Subtle moving ground / horizon */
        @keyframes groundMove {
            from {
                background-position-x: 0;
            }

            to {
                background-position-x: -200%;
            }
        }

        .fade-in {
            animation: fadeIn 0.8s ease-out forwards;
        }

        .fade-in.delay-200 {
            animation-delay: 0.2s;
        }

        .animate-roadLines {
            animation: roadLines 2.2s linear infinite;
        }

        .animation-duration-1600 {
            animation-duration: 1.6s;
        }

        .animation-delay-500 {
            animation-delay: -0.5s;
        }

        .animation-delay-1000 {
            animation-delay: -1s;
        }

        .animate-groundMove {
            animation: groundMove 5s linear infinite;
            background-size: 200% 100%;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
@endpush
