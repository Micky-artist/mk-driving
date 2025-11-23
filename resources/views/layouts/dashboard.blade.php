<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? config('app.name', 'MK Driving') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- Base Styles -->
    <style>
        [x-cloak] { 
            display: none !important; 
        }
        
        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
        }
    </style>
    
    <!-- Page-specific Styles -->
    @stack('styles')
</head>
<body class="font-sans antialiased bg-gray-50 h-full">
    <div id="app" x-data="{ sidebarOpen: false }" class="h-screen flex overflow-hidden bg-gray-50">
        <!-- Mobile sidebar overlay -->
        <div 
            x-show="sidebarOpen"
            @click="sidebarOpen = false"
            x-transition:enter="transition-opacity ease-linear duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition-opacity ease-linear duration-300"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 bg-gray-900 bg-opacity-50 z-40 lg:hidden"
            x-cloak
        ></div>

        <!-- Sidebar -->
        <div 
            class="fixed inset-y-0 left-0 z-40 w-64 bg-white border-r border-gray-200 transform transition-transform duration-300 ease-in-out lg:translate-x-0 lg:static lg:z-auto flex-shrink-0 h-screen overflow-y-auto"
            :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
            style="scrollbar-width: thin;"
        >
            <x-sidebar :active="$activeRoute ?? ''" />
        </div>

        <!-- Main content wrapper -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Header -->
            <header class="bg-white border-b border-gray-200 shadow-sm z-30">
                <div class="flex items-center justify-between h-[63px] px-4 sm:px-6 lg:px-8">
                    <!-- Mobile menu button -->
                    <button 
                        @click="sidebarOpen = true"
                        class="lg:hidden -ml-2 p-2 rounded-md text-gray-500 hover:text-gray-600 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-blue-500"
                        aria-label="Open sidebar"
                    >
                        <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                    
                    <!-- Page title -->
                    <h1 class="text-xl font-semibold text-gray-900">
                        <a href="{{ route('dashboard', ['locale' => app()->getLocale()]) }}" class="hover:text-blue-600 transition-colors">
                            {{ $title ?? __('navigation.dashboard') }}
                        </a>
                    </h1>
                    
                    <!-- Header actions -->
                    <x-dashboard.header />
                </div>
            </header>

            <!-- Main content -->
            <main class="flex-1 overflow-y-auto focus:outline-none">
                <div class="py-6 px-4 sm:px-6 lg:px-8">
                    <div class="max-w-7xl mx-auto">
                        @yield('dashboard-content')
                    </div>
                </div>
            </main>
        </div>
    </div>

    @stack('scripts')
    
    <!-- Notifications Component -->
    <x-notifications />
    
    <script>
        document.addEventListener('alpine:init', () => {
            // Close mobile sidebar when clicking a link
            document.addEventListener('click', function(event) {
                const link = event.target.closest('a[href]');
                if (link && window.innerWidth < 1024) {
                    // Get Alpine data
                    const app = document.querySelector('[x-data]');
                    if (app && app.__x) {
                        app.__x.$data.sidebarOpen = false;
                    }
                }
            });

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