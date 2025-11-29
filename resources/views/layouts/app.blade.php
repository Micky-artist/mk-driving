<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full" x-data="{ darkMode: localStorage.getItem('darkMode') === 'true' }" x-init="
    // Check for saved user preference, if any, on load
    if (localStorage.getItem('darkMode') === null) {
        // If no preference saved, check system preference
        darkMode = window.matchMedia('(prefers-color-scheme: dark)').matches;
    }
    
    // Apply the initial theme
    $watch('darkMode', value => {
        localStorage.setItem('darkMode', value);
        document.documentElement.classList.toggle('dark', value);
    });
    
    // Watch for system theme changes
    window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', e => {
        if (!('darkMode' in localStorage)) {  // Only if user hasn't explicitly set a preference
            darkMode = e.matches;
        }
    });
">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'MK Driving School') }}</title>

    <!-- General Meta Tags -->
    <meta name="description" content="Prepare for your driving test with MK Driving - The best way to practice and pass your driving theory test in Rwanda">
    
    <!-- Open Graph / Social Media Meta Tags (used by Facebook, Instagram, WhatsApp, etc.) -->
    <meta property="og:title" content="{{ config('app.name', 'MK Driving School') }} - Pass Your Driving Test in Rwanda">
    <meta property="og:description" content="🚗✨ Prepare & pass your driving test with MK Driving School. Practice tests, expert tips, and everything you need to get your driver's license in Rwanda!">
    <meta property="og:image" content="{{ url('/og-image.png') }}">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="MK Driving School">
    
    <!-- WhatsApp Specific -->
    <meta property="og:image:secure_url" content="{{ url('/og-image.png') }}">
    <meta property="og:image:alt" content="MK Driving School - Pass Your Driving Test in Rwanda">
    
    <!-- Twitter Card Meta Tags -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ config('app.name', 'MK Driving School') }} - Pass Your Driving Test in Rwanda">
    <meta name="twitter:description" content="🚗✨ Prepare & pass your driving test with MK Driving School. Practice tests, expert tips, and everything you need to get your driver's license in Rwanda!">
    <meta name="twitter:image" content="{{ url('/og-image.png') }}">
    
    <!-- Additional Meta Tags for Better Sharing -->
    <meta name="theme-color" content="#1a365d"> <!-- Dark blue from your color scheme -->
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-title" content="MK Driving">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="application-name" content="MK Driving">
    <meta name="msapplication-TileColor" content="#1a365d">

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('favicon.ico') }}">
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    
    <!-- Scroll Animation Styles -->
    <style>
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
    <!-- Header -->
    @if (!request()->is('dashboard*') && !request()->is('login'))
        @include('components.navbar')
    @endif

    <!-- Page Content -->
    <main class="flex-grow">
      <div class="md:pt-20">
        @yield('content')
      </div>
    </main>

    <!-- Footer -->
    @include('components.footer')

    @stack('scripts')
    
    <!-- Notifications Component -->
    <x-notifications />
    
    <script>
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
    </script>
</body>
</html>
