<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- Flash-prevention script for theme -->
    <script>
        (function() {
            const savedTheme = localStorage.getItem('theme');
            const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            const shouldUseDark = savedTheme === 'dark' || (!savedTheme && prefersDark);
            
            if (shouldUseDark) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
            
            // Set meta theme-color
            const metaThemeColor = document.querySelector('meta[name="theme-color"]');
            if (metaThemeColor) {
                metaThemeColor.content = shouldUseDark ? '#1e3a8a' : '#1e40af';
            }
        })();
    </script>

@php
    $isRwanda = request()->segment(1) === 'rw';
    $description = $isRwanda ? 'Itegure ikizami cy\'amategeko y\'umuhanda hamwe na MK Driving School - Uburyo bwa mbere bwizewe naba jeune mu Rwanda' : 'Prepare for your driving test with MK Driving School - The best way to practice and pass your driving theory test in Rwanda';
    $titleSuffix = $isRwanda ? 'Tsinda ikizami cyo gutwara mu Rwanda' : 'Pass Your Driving Test in Rwanda';
    $ogDescription = $isRwanda ? 'Urubuga rwizewe rugufasha kubona ibibazo by\'kizamini cya provisoire n\'ibisubizo byabyo, bigufasha kwiga vuba, neza no gutsinda byoroshye - aho waba uri hose. 🚗' : '🚗✨ Prepare & pass your driving test with MK Driving School. Practice tests, expert tips, and everything you need to get your driver\'s license in Rwanda!';
@endphp

    <title>{{ config('app.name', 'MK Driving School') }}</title>

    <!-- General Meta Tags -->
    <meta name="description" content="{{ $description }}">
    
    <!-- Open Graph / Social Media Meta Tags (used by Facebook, Instagram, WhatsApp, etc.) -->
    <meta property="og:title" content="{{ config('app.name', 'MK Driving School') }} - {{ $titleSuffix }}">
    <meta property="og:description" content="{{ $ogDescription }}">
    <meta property="og:image" content="{{ url('/og-image.png') }}">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="MK Driving School">
    
    <!-- WhatsApp Specific -->
    <meta property="og:image:secure_url" content="{{ url('/og-image.png') }}">
    <meta property="og:image:alt" content="MK Driving School - {{ $titleSuffix }}">
    
    <!-- Twitter Card Meta Tags -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ config('app.name', 'MK Driving School') }} - {{ $titleSuffix }}">
    <meta name="twitter:description" content="{{ $ogDescription }}">
    <meta name="twitter:image" content="{{ url('/og-image.png') }}">
    
    <!-- Additional Meta Tags for Better Sharing -->
    <meta name="theme-color" content="#1a365d"> <!-- Dark blue from your color scheme -->
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-title" content="MK Driving School">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="application-name" content="MK Driving School">
    <meta name="msapplication-TileColor" content="#1a365d">
    
    <!-- SEO Tags for Kinyarwanda Focus -->
    <link rel="canonical" href="{{ request()->segment(1) === 'en' ? str_replace('/en/', '/rw/', url()->current()) : url()->current() }}">
    <link rel="alternate" hreflang="rw" href="{{ request()->segment(1) === 'en' ? str_replace('/en/', '/rw/', url()->current()) : url()->current() }}">
    <link rel="alternate" hreflang="x-default" href="{{ request()->segment(1) === 'en' ? str_replace('/en/', '/rw/', url()->current()) : url()->current() }}">
    
    @if(request()->segment(1) === 'en')
        <!-- Noindex for English pages -->
        <meta name="robots" content="noindex, nofollow">
    @else
        <!-- Index for Kinyarwanda pages -->
        <meta name="robots" content="index, follow">
    @endif

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('favicon.ico') }}">
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Figtree', 'sans-serif'],
                    },
                }
            }
        }
    </script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    
    <!-- Global Scrollbar Styles -->
    <style>
        /* Fade-in animations */
        .fade-in {
            opacity: 0;
            transform: translateY(20px);
            transition: opacity 0.6s ease-out, transform 0.6s ease-out;
        }

        .fade-in.visible {
            opacity: 1;
            transform: translateY(0);
        }

        .fade-in.delay-100 {
            transition-delay: 0.1s;
        }

        .fade-in.delay-200 {
            transition-delay: 0.2s;
        }

        .fade-in.delay-300 {
            transition-delay: 0.3s;
        }

        .fade-in.delay-400 {
            transition-delay: 0.4s;
        }

        /* Standardized scrollbar styles */
        /* Dark mode scrollbar styles for Webkit browsers */
        .dark ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }

        .dark ::-webkit-scrollbar-track {
            background: rgb(31 41 55);
        }

        .dark ::-webkit-scrollbar-thumb {
            background-color: rgb(55 65 81);
            border-radius: 3px;
        }

        .dark ::-webkit-scrollbar-thumb:hover {
            background-color: rgb(75 85 99);
        }

        /* Light mode scrollbar styles for Webkit browsers */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }

        ::-webkit-scrollbar-track {
            background: transparent;
        }

        ::-webkit-scrollbar-thumb {
            background-color: rgb(203 213 225);
            border-radius: 3px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background-color: rgb(189 197 209);
        }

        /* Firefox scrollbar styles */
        html {
            scrollbar-width: thin;
            scrollbar-color: rgb(203 213 225) transparent;
        }

        .dark html {
            scrollbar-color: rgb(55 65 81) rgb(31 41 55);
        }

        /* Utility classes for scrollable containers */
        .scrollbar-thin {
            scrollbar-width: thin;
            scrollbar-color: rgb(203 213 225) transparent;
        }

        .dark .scrollbar-thin {
            scrollbar-color: rgb(55 65 81) rgb(31 41 55);
        }

        .scrollbar-hide::-webkit-scrollbar {
            display: none;
        }

        .scrollbar-hide {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Figtree', 'sans-serif'],
                    },
                    spacing: {
                        '128': '32rem',
                        '144': '36rem',
                    },
                    colors: {
                        primary: {
                            50: '#f0f9ff',
                            100: '#e0f2fe',
                            200: '#bae6fd',
                            300: '#7dd3fc',
                            400: '#38bdf8',
                            500: '#0ea5e9',
                            600: '#0284c7',
                            700: '#0369a1',
                            800: '#075985',
                            900: '#0c4a6e',
                        },
                        dark: {
                            50: '#f8fafc',
                            100: '#f1f5f9',
                            200: '#e2e8f0',
                            300: '#cbd5e1',
                            400: '#94a3b8',
                            500: '#64748b',
                            600: '#475569',
                            700: '#334155',
                            800: '#1e293b',
                            900: '#0f172a',
                        },
                    },
                    boxShadow: {
                        'dark': '0 4px 6px -1px rgba(0, 0, 0, 0.5), 0 2px 4px -1px rgba(0, 0, 0, 0.3)',
                    },
                },
            },
            plugins: [
                require('@tailwindcss/forms'),
                require('@tailwindcss/typography'),
            ],
        }
    </script>

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    @stack('styles')
    <style>
        /* Alpine.js x-cloak styling to prevent flash */
        [x-cloak] { 
            display: none !important; 
        }
        
        /* Ensure full height and proper scrolling */
        html, body {
            min-height: 100%;
            scroll-behavior: smooth;
            max-width: 100%;
            overflow-x: hidden;
            position: relative;
            width: 100%;
        }
        
        /* Prevent horizontal scrolling */
        html {
            overflow-x: hidden;
            width: 100%;
        }
        
        /* Ensure all elements stay within viewport */
        * {
            max-width: 100%;
            box-sizing: border-box;
        }
        
        /* Dark mode scrollbar styles for Webkit browsers */
        .dark ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        
        .dark ::-webkit-scrollbar-track {
            background: rgb(31 41 55);
        }
        
        .dark ::-webkit-scrollbar-thumb {
            background-color: rgb(55 65 81);
            border-radius: 3px;
        }
        
        .dark ::-webkit-scrollbar-thumb:hover {
            background-color: rgb(75 85 99);
        }
        
        /* Light mode scrollbar styles for Webkit browsers */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        
        ::-webkit-scrollbar-track {
            background: rgb(249 250 251);
        }
        
        ::-webkit-scrollbar-thumb {
            background-color: rgb(203 213 225);
            border-radius: 4px;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background-color: rgb(189 197 209);
        }
        
        /* Better touch targets for mobile */
        @media (max-width: 640px) {
            button, a, [role="button"], [type="button"], [type="submit"] {
                min-height: 44px;
                min-width: 44px;
            }
        }
        
        /* Smooth scrolling for anchor links */
        html {
            scroll-padding-top: 1rem; /* Default padding for mobile */
            margin: 0;
            padding: 0;
        }
        
        /* Adjust for desktop with fixed header */
        @media (min-width: 768px) {
            html {
                scroll-padding-top: 5rem; /* Height of the fixed header */
            }
        }
    </style>
</head>
<body class="font-sans antialiased text-gray-900 dark:text-gray-100 min-h-screen transition-colors duration-200 bg-gradient-to-br from-white to-gray-50 dark:from-gray-900 dark:to-gray-800 bg-fixed overflow-x-hidden m-0 p-0">
    <!-- App Loading State -->
    <div id="app-loader" class="fixed inset-0 bg-gray-50 dark:bg-gray-900 z-[9999] flex items-center justify-center transition-opacity duration-300">
        <div class="text-center max-w-sm mx-auto px-6">
            <!-- App Logo -->
            <div class="flex items-center justify-center mb-8">
                <img src="{{ asset('logo.png') }}" alt="MK Driving School Logo" 
                     class="h-16 w-16 md:h-20 md:w-20 rounded-lg shadow-lg"
                     onerror="this.onerror=null; this.src='{{ asset('images/logo.png') }}'">
            </div>
            
            <!-- Logo Text -->
            <div class="mb-8">
                <h1 class="text-xl md:text-2xl font-bold text-gray-900 dark:text-white leading-tight">
                    MK Driving School
                </h1>
            </div>
            
            <!-- Loading Progress Bar -->
            <div class="w-full">
                <div class="h-2 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
                    <div id="loading-progress" 
                         class="h-full bg-gradient-to-r from-blue-500 to-blue-600 dark:from-blue-400 dark:to-blue-500 rounded-full transition-all duration-300 ease-out" 
                         style="width: 0%;"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main App Content (Hidden Initially) -->
    <div id="app-content" class="opacity-0 transition-opacity duration-500">
    <!-- Header -->
    @if (!request()->is('dashboard*') && !request()->is('login'))
        <x-unified-navbar :showUserStats="true" />
    @endif

    <!-- Page Content -->
    <main class="flex-grow">
      <div class="md:pt-16">
        @yield('content')
      </div>
    </main>

    <!-- Footer -->
    @include('components.footer')
    </div>

    @stack('scripts')
    
    <!-- Notifications Component -->
    <x-notifications />
    
    <!-- Global Toast Component -->
    <x-global-toast />
    
    <script>
        // App Loading System
        class AppLoader {
            constructor() {
                this.loader = document.getElementById('app-loader');
                this.content = document.getElementById('app-content');
                this.progressBar = document.getElementById('loading-progress');
                this.resources = new Set();
                this.loadedResources = 0;
                this.totalResources = 0;
                this.minLoadTime = 800; // Minimum loading time for UX
                this.startTime = Date.now();
            }

            // Track all resources
            trackResources() {
                // Track images
                document.querySelectorAll('img').forEach(img => {
                    this.resources.add('image:' + img.src);
                    this.totalResources++;
                });

                // Track stylesheets
                document.querySelectorAll('link[rel="stylesheet"]').forEach(link => {
                    this.resources.add('style:' + link.href);
                    this.totalResources++;
                });

                // Track scripts
                document.querySelectorAll('script[src]').forEach(script => {
                    if (!script.defer) {
                        this.resources.add('script:' + script.src);
                        this.totalResources++;
                    }
                });

                // Track fonts
                if (document.fonts) {
                    document.fonts.forEach(font => {
                        if (font.status === 'unloaded') {
                            this.resources.add('font:' + font.family);
                            this.totalResources++;
                        }
                    });
                }

                // Add some buffer time for dynamic content
                this.totalResources += 2;
            }

            // Update progress
            updateProgress() {
                const progress = Math.min((this.loadedResources / this.totalResources) * 100, 95);
                if (this.progressBar) {
                    this.progressBar.style.width = progress + '%';
                }
            }

            // Mark resource as loaded
            resourceLoaded(type) {
                this.loadedResources++;
                this.updateProgress();
                
                if (this.loadedResources >= this.totalResources) {
                    this.complete();
                }
            }

            // Complete loading
            complete() {
                const elapsed = Date.now() - this.startTime;
                const remainingTime = Math.max(0, this.minLoadTime - elapsed);
                
                setTimeout(() => {
                    // Final progress to 100%
                    if (this.progressBar) {
                        this.progressBar.style.width = '100%';
                    }
                    
                    // Fade out loader
                    setTimeout(() => {
                        if (this.loader) {
                            this.loader.style.opacity = '0';
                            setTimeout(() => {
                                this.loader.style.display = 'none';
                            }, 300);
                        }
                        
                        // Fade in content
                        if (this.content) {
                            this.content.style.opacity = '1';
                        }
                        
                        // Enable scrolling
                        document.body.style.overflow = '';
                    }, 200);
                }, remainingTime);
            }

            // Initialize
            init() {
                // Prevent scrolling during load
                document.body.style.overflow = 'hidden';
                
                // Track resources
                this.trackResources();
                
                // Monitor resource loading
                this.monitorResources();
                
                // Fallback timeout
                setTimeout(() => {
                    this.complete();
                }, 5000);
            }

            // Monitor resource loading
            monitorResources() {
                // Monitor images
                document.querySelectorAll('img').forEach(img => {
                    if (img.complete) {
                        this.resourceLoaded('image');
                    } else {
                        img.addEventListener('load', () => this.resourceLoaded('image'));
                        img.addEventListener('error', () => this.resourceLoaded('image'));
                    }
                });

                // Monitor stylesheets
                document.querySelectorAll('link[rel="stylesheet"]').forEach(link => {
                    if (link.sheet) {
                        this.resourceLoaded('style');
                    } else {
                        link.addEventListener('load', () => this.resourceLoaded('style'));
                        link.addEventListener('error', () => this.resourceLoaded('style'));
                    }
                });

                // Monitor fonts
                if (document.fonts) {
                    document.fonts.ready.then(() => {
                        this.resourceLoaded('font');
                    });
                }

                // Monitor DOM ready
                if (document.readyState === 'complete') {
                    this.resourceLoaded('dom');
                } else {
                    window.addEventListener('load', () => this.resourceLoaded('dom'));
                }
            }
        }

        // Initialize app loader
        document.addEventListener('DOMContentLoaded', function() {
            const appLoader = new AppLoader();
            appLoader.init();
        });

        document.addEventListener('alpine:init', () => {
            // Display any server-side flash notifications
            @if(session('notification'))
                const notification = @json(session('notification'));
                window.notify[notification.type](notification.message, notification.duration || 5000);
            @endif

            // Intercept browser alerts and show them as notifications
            const originalAlert = window.alert;
            window.alert = function(message) {
                window.notify.info(message);
                // Uncomment the line below if you want to keep the original alert as well
                // originalAlert(message);
            };
        });

        // Scroll reveal animation
        document.addEventListener('DOMContentLoaded', function() {
            const fadeElements = document.querySelectorAll('.fade-in');
            
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('visible');
                    }
                });
            }, {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            });

            fadeElements.forEach(element => {
                observer.observe(element);
            });
        });

        // Quiz Companion Live Competition System
        document.addEventListener('alpine:init', () => {
            Alpine.data('quizCompanion', (config) => ({
                quizId: config.quizId,
                isGuest: config.isGuest,
                showLeaderboard: config.showLeaderboard,
                showQA: config.showQA,
                showRobots: config.showRobots,
                
                // Data
                robotMessages: [],
                leaderboard: [],
                questions: [],
                liveActivities: [],
                notification: null,
                activeUsersCount: 0,
                
                // UI State
                isMobile: false,
                mobileOpen: false,
                unreadCount: 0,
                newQuestion: '',
                submittingQuestion: false,
                
                init() {
                    this.isMobile = window.innerWidth < 1024;
                    this.checkUnreadMessages();
                    
                    // Listen for live activity updates from other users
                    window.addEventListener('liveActivityUpdate', (event) => {
                        console.log('Live activity update received:', event.detail);
                        this.updateLiveActivities(event.detail.activities || []);
                        this.updateNotification(event.detail.notification);
                    });
                    
                    // Listen for window resize
                    window.addEventListener('resize', () => {
                        this.isMobile = window.innerWidth < 1024;
                    });
                },
                
                destroy() {
                    console.log('Cleaning up companion sidebar state...');
                    // Clear robot messages and activities
                    this.robotMessages = [];
                    this.liveActivities = [];
                    this.leaderboard = [];
                    this.questions = [];
                    this.notification = null;
                    this.activeUsersCount = 0;
                    this.unreadCount = 0;
                    this.newQuestion = '';
                    this.submittingQuestion = false;
                    console.log('Companion sidebar state cleaned up');
                },

                updateLiveActivities(activities) {
                    console.log('Updating live activities with:', activities);
                    
                    // Ensure activities is always an array
                    const activitiesArray = Array.isArray(activities) ? activities : [];
                    
                    if (activitiesArray.length > 0) {
                        // Get existing activity timestamps to avoid duplicates
                        const existingTimestamps = new Set(
                            (this.liveActivities || []).map(activity => 
                                activity.timestamp + '_' + (activity.learner_id || activity.user_id)
                            )
                        );
                        
                        // Filter only truly new activities
                        const newActivities = activitiesArray.filter(activity => {
                            const activityKey = activity.timestamp + '_' + (activity.learner_id || activity.user_id);
                            return !existingTimestamps.has(activityKey);
                        });
                        
                        console.log('Filtered new activities:', newActivities.length, 'items');
                        
                        if (newActivities.length > 0) {
                            // Prepend new activities (newest first)
                            this.liveActivities = [...newActivities, ...(this.liveActivities || [])];
                            
                            // Keep only the last 50 activities
                            if (this.liveActivities.length > 50) {
                                this.liveActivities = this.liveActivities.slice(-50);
                            }
                            
                            // Extract new robot messages
                            const newRobotMessages = newActivities
                                .filter(activity => activity.type === 'learner_answer')
                                .map((activity, index) => ({
                                    id: `${activity.learner_id}_${activity.question_id}_${activity.timestamp}_${index}_${Date.now()}`,
                                    robot_name: activity.learner_name,
                                    message: activity.message,
                                    timestamp_human: activity.timestamp_human,
                                    is_correct: activity.is_correct
                                }));
                            
                            // Prepend new robot messages (newest first)
                            this.robotMessages = [...newRobotMessages, ...(this.robotMessages || [])];
                            
                            // Keep only the last 50 robot messages
                            if (this.robotMessages.length > 50) {
                                this.robotMessages = this.robotMessages.slice(-50);
                            }
                            
                            console.log('Robot messages updated:', this.robotMessages.length, 'Total activities:', this.liveActivities.length);
                        }
                    }
                    
                    // Dispatch event for global toast
                    console.log('Dispatching robotCompanionUpdate event with:', {
                        robotMessages: this.robotMessages || [],
                        activitiesCount: (this.liveActivities || []).length
                    });
                    window.dispatchEvent(new CustomEvent('robotCompanionUpdate', {
                        detail: {
                            robotMessages: this.robotMessages || [],
                            activitiesCount: (this.liveActivities || []).length
                        }
                    }));
                    
                    // Update unread count based on new activities
                    if (activitiesArray.length > previousCount) {
                        this.unreadCount = Math.min(activitiesArray.length - previousCount + this.unreadCount, 99);
                    }
                },
                
                updateNotification(notification) {
                    if (notification && notification.type === 'live_competition') {
                        this.notification = {
                            message: notification.message,
                            timestamp: notification.timestamp,
                            active_users: notification.active_users,
                            robot_responses: notification.robot_responses || []
                        };
                    }
                },
                
                formatTime(timestamp) {
                    const date = new Date(timestamp);
                    const now = new Date();
                    const diff = now - date;
                    
                    if (diff < 60000) return 'just now';
                    if (diff < 3600000) return `${Math.floor(diff / 60000)}m ago`;
                    if (diff < 86400000) return `${Math.floor(diff / 3600000)}h ago`;
                    return date.toLocaleDateString();
                },
                
                toggleMobileOpen() {
                    this.mobileOpen = !this.mobileOpen;
                    if (this.mobileOpen) {
                        this.unreadCount = 0;
                    }
                },
                
                checkUnreadMessages() {
                    // Check for existing unread messages
                    const unread = this.robotMessages.filter(msg => !msg.read).length;
                    this.unreadCount = Math.min(unread, 99);
                },
                
                async submitQuestion() {
                    if (!this.newQuestion.trim() || this.submittingQuestion) return;
                    
                    this.submittingQuestion = true;
                    
                    try {
                        const response = await fetch('/api/forum/questions', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
                                'Content-Type': 'application/json',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                question: this.newQuestion.trim(),
                                quiz_id: this.quizId
                            })
                        });
                        
                        if (response.ok) {
                            this.newQuestion = '';
                            // Show success notification
                            if (window.notify?.success) {
                                window.notify.success('Question posted successfully!');
                            }
                        } else {
                            throw new Error('Failed to post question');
                        }
                    } catch (error) {
                        console.error('Error posting question:', error);
                        if (window.notify?.error) {
                            window.notify.error('Failed to post question');
                        }
                    } finally {
                        this.submittingQuestion = false;
                    }
                }
            }));
        });
    </script>
</body>
</html>
