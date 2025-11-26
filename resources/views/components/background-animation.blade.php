<!-- Background Elements - EXACT SAME POSITIONING AS BEFORE -->
<div
    class="fixed right-0 translate-x-[30%] sm:translate-x-[50%] -translate-y-1/3 rounded-full w-[20rem] sm:w-[30rem] md:w-[40rem] lg:w-[50rem] aspect-square bg-[#8ECAE680]/50 dark:bg-[#1E3A8A80]/40 pointer-events-none -z-10 transition-colors duration-300"
    @props([
        'class' => '',
        'mobileTranslate' => '-translate-x-1/5',
    ])
>
    <div
        class="absolute bottom-0 left-1/2 -translate-x-1/2 rounded-full w-[50%] aspect-square bg-[#8ECAE680]/70 dark:bg-blue-900/80 backdrop-blur-sm shadow-md transition-colors duration-300 overflow-hidden"
    >
        <!-- 1. Moving dashed road lines (3 lanes) -->
        <div class="absolute inset-y-0 left-0 w-full opacity-70 pointer-events-none">
            <div class="absolute inset-0 flex flex-col justify-center items-start gap-16">
                <div class="h-1 bg-gray-400/80 dark:bg-gray-800/60 w-full animate-roadLines animation-duration-1600">
                </div>
                <div
                    class="h-1 bg-gray-400/80 dark:bg-gray-800/60 w-full animate-roadLines animation-duration-1600 animation-delay-500">
                </div>
                <div
                    class="h-1 bg-gray-400/80 dark:bg-gray-800/60 w-full animate-roadLines animation-duration-1600 animation-delay-1000">
                </div>
            </div>
        </div>

        <!-- 2. Subtle moving ground layers -->
        <div class="absolute left-0 top-0 bottom-0 w-2/3 pointer-events-none overflow-hidden">
            <!-- Base layer - lightest shade with smoother gradient -->
            <div class="absolute inset-0 bg-gradient-to-r from-gray-300/20 via-transparent to-transparent dark:from-gray-800/30 dark:via-transparent animate-groundMove" style="animation-delay: 0s; mask-image: linear-gradient(to right, black, transparent);"></div>
            <!-- Mid layer - medium shade with smoother gradient -->
            <div class="absolute inset-0 bg-gradient-to-r from-gray-400/25 via-transparent to-transparent dark:from-gray-700/40 dark:via-transparent animate-groundMove" style="animation-duration: 8s; animation-delay: -2s; mask-image: linear-gradient(to right, black, transparent);"></div>
                    </div>


        <!-- 3. Car - NOW STATIC + tiny floating animation for life -->
        <div class="absolute {{ $mobileTranslate }} sm:-translate-x-1/4  inset-0 flex items-center justify-center p-4 text-center">
            <div class="text-5xl sm:text-6xl md:text-7xl lg:text-8xl drop-shadow-2xl select-none pointer-events-none animate-float"
                style="transform: rotate(90deg);">

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
    </div>
</div>

@push('styles')
    <style>
        @keyframes float {

            0%,
            100% {
                transform: translateY(0) rotate(0deg);
            }

            50% {
                transform: translateY(-0.5px) rotate(0.5deg);
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

        .animate-float {
            animation: float 7s ease-in-out infinite;
        }
    </style>
@endpush
