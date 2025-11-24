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

    <title>{{ config('app.name', 'MK Driving') }}</title>

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
        /* Gradient background is now applied directly to the body */
        html {
            min-height: 100%;
        }
    </style>
</head>
<body class="font-sans antialiased text-gray-900 dark:text-gray-100 min-h-screen transition-colors duration-200 bg-gradient-to-br from-gray-50 to-gray-100 dark:from-[#0f172a] dark:to-[#1e293b] bg-fixed">
    <!-- Header -->
    @if (!request()->is('dashboard*'))
        @include('components.navbar')
    @endif

    <!-- Page Content -->
    <main class="flex-grow pt-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
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
    </script>
</body>
</html>
