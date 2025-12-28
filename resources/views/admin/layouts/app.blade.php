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
    <title>@yield('title', config('app.name', 'MK Driving')) - Admin</title>

    <!-- Favicon -->
    <link rel="icon" href="{{ asset('favicon.ico') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Styles -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.8.2/dist/alpine.min.js" defer></script>

    <style>
        [x-cloak] {
            display: none !important;
        }

        body {
            font-family: 'Figtree', Arial, sans-serif;
            background-color: #f5f5f5;
            min-height: 100vh;
            line-height: 1.5;
        }
        
        .dark body {
            background-color: #1f2937;
        }

        .admin-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 2rem;
        }

        .alert {
            padding: 1rem;
            margin-bottom: 1.5rem;
            border-radius: 0.375rem;
            border: 1px solid transparent;
        }

        .alert-success {
            background-color: #d1fae5;
            color: #065f46;
            border-color: #a7f3d0;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.5rem 1rem;
            border-radius: 0.375rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
            text-decoration: none;
            border: 1px solid transparent;
        }

        .btn-primary {
            background: linear-gradient(135deg, #1e40af 0%, #2563eb 100%);
            color: white;
            box-shadow: 0 2px 4px rgba(30, 64, 175, 0.2);
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #1d4ed8 0%, #2563eb 100%);
            box-shadow: 0 4px 8px rgba(30, 64, 175, 0.3);
        }

        .btn-success {
            background-color: #10b981;
            color: white;
        }

        .btn-danger {
            background-color: #ef4444;
            color: white;
        }

        .btn:hover {
            opacity: 0.9;
            transform: translateY(-1px);
        }

        /* Enhanced form inputs with blue focus states */
        input[type="text"],
        input[type="email"],
        input[type="password"],
        input[type="number"],
        select,
        textarea {
            border: 1px solid #d1d5db;
            transition: all 0.2s ease;
        }
        
        input[type="text"]:focus,
        input[type="email"]:focus,
        input[type="password"]:focus,
        input[type="number"]:focus,
        select:focus,
        textarea:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }
        
        .dark input[type="text"],
        .dark input[type="email"],
        .dark input[type="password"],
        .dark input[type="number"],
        .dark select,
        .dark textarea {
            border-color: #4b5563;
            background-color: #374151;
        }
        
        .dark input[type="text"]:focus,
        .dark input[type="email"]:focus,
        .dark input[type="password"]:focus,
        .dark input[type="number"]:focus,
        .dark select:focus,
        .dark textarea:focus {
            border-color: #60a5fa;
            box-shadow: 0 0 0 3px rgba(96, 165, 250, 0.2);
        }

        /* Enhanced links with blue hover states */
        a:not(.btn):not(.group) {
            color: #2563eb;
            transition: color 0.2s ease;
        }
        
        a:not(.btn):not(.group):hover {
            color: #1d4ed8;
        }
        
        .dark a:not(.btn):not(.group) {
            color: #60a5fa;
        }
        
        .dark a:not(.btn):not(.group):hover {
            color: #93c5fd;
        }

        /* Pagination enhancements */
        .pagination a {
            color: #6b7280;
            border-color: #d1d5db;
            transition: all 0.2s ease;
        }
        
        .pagination a:hover {
            color: #2563eb;
            border-color: #2563eb;
            background-color: #eff6ff;
        }
        
        .pagination .current {
            background-color: #2563eb;
            border-color: #2563eb;
            color: white;
        }
        
        .dark .pagination a {
            color: #9ca3af;
            border-color: #4b5563;
        }
        
        .dark .pagination a:hover {
            color: #60a5fa;
            border-color: #60a5fa;
            background-color: #1e3a8a;
        }
        
        .dark .pagination .current {
            background-color: #3b82f6;
            border-color: #3b82f6;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin: 1.5rem 0;
            background: white;
            border-radius: 0.5rem;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }
        
        .dark .table {
            background: #1f2937;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.3);
        }

        .table th,
        .table td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid #e5e7eb;
        }
        
        .dark .table th,
        .dark .table td {
            border-bottom: 1px solid #374151;
        }

        .table th {
            background: linear-gradient(135deg, #1e40af 0%, #2563eb 100%);
            color: white;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.05em;
        }

        .table tr:last-child td {
            border-bottom: none;
        }

        .table tr:hover {
            background-color: #f0f9ff;
        }
        
        .dark .table tr:hover {
            background-color: #1e3a8a;
        }

        .card {
            background: white;
            border-radius: 0.5rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            border-top: 3px solid transparent;
            transition: all 0.2s ease;
        }
        
        .card:hover {
            border-top-color: #1e40af;
            box-shadow: 0 4px 12px rgba(30, 64, 175, 0.15);
        }
        
        .dark .card {
            background: #1f2937;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.3);
        }
        
        .dark .card:hover {
            border-top-color: #3b82f6;
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.2);
        }

        .text-blue-600 {
            color: #2563eb;
        }

        .text-blue-600:hover {
            color: #1d4ed8;
            text-decoration: underline;
        }

        .text-2xl {
            font-size: 1.5rem;
            line-height: 2rem;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 1.5rem;
        }
        
        .dark .text-2xl {
            color: #f3f4f6;
        }

        .mb-4 {
            margin-bottom: 1rem;
        }

        .mb-8 {
            margin-bottom: 2rem;
        }

        .h3 {
            font-size: 1.25rem;
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 1rem;
        }
        
        .dark .h3 {
            color: #f3f4f6;
        }

        .card-title {
            font-size: 1.125rem;
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 0.75rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid #e5e7eb;
            position: relative;
        }
        
        .card-title::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 60px;
            height: 2px;
            background: linear-gradient(90deg, #1e40af, #3b82f6);
        }
        
        .dark .card-title {
            color: #f3f4f6;
            border-bottom-color: #374151;
        }
        
        .dark .card-title::after {
            background: linear-gradient(90deg, #3b82f6, #60a5fa);
        }

        .card-text {
            color: #475569;
            line-height: 1.6;
        }
        
        .dark .card-text {
            color: #d1d5db;
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
            scrollbar-color: rgb(55 65 81) rgb(31 41 55);
        }

        .dark html {
            scrollbar-color: rgb(55 65 81) rgb(31 41 55);
        }

        /* Utility classes for scrollable containers */
        .scrollbar-thin {
            scrollbar-width: thin;
            scrollbar-color: rgb(55 65 81) rgb(31 41 55);
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
    /* Fade-in animations */
        @keyframes fadeIn {
            from { 
                opacity: 0; 
                transform: translateY(20px); 
            }
            to { 
                opacity: 1; 
                transform: translateY(0); 
            }
        }
        
        .animate-fade-in {
            animation: fadeIn 0.6s ease-out forwards;
        }
    </style>
</head>

<body class="antialiased bg-gray-100 dark:bg-gray-900 text-gray-900 dark:text-gray-100" x-data="{ mobileMenuOpen: false }" x-init="console.log('Body x-data initialized, mobileMenuOpen:', mobileMenuOpen)">
    <div class="flex h-screen">
        <!-- Sidebar -->
        <div class="hidden md:flex md:flex-shrink-0">
            <div class="fixed left-0 top-0 flex flex-col w-64 h-screen bg-gradient-to-b from-blue-600 to-blue-700 dark:from-gray-900 dark:to-blue-950 text-white z-30">
                <!-- Logo -->
                <a href="{{ route('home', ['locale' => app()->getLocale()]) }}" class="block">
                    <div
                        class="flex items-center justify-between h-16 px-4 bg-gradient-to-r from-blue-600 to-blue-700 dark:from-gray-800 dark:to-gray-900 hover:from-blue-700 hover:to-blue-800 transition-all duration-200 shadow-lg">
                        <span class="text-xl font-bold text-white">MK Driving</span>
                        
                        <!-- Notifications Bell -->
                        <div x-data="{ 
                            notificationsOpen: false,
                            notifications: @json(\App\Models\Notification::unread()->recent()->latest()->limit(10)->get()),
                            unreadCount: {{ \App\Models\Notification::unread()->recent()->count() }}
                        }" class="relative">
                            <!-- Bell Icon -->
                            <button @click="notificationsOpen = !notificationsOpen" 
                                    class="relative p-2 text-white/80 hover:text-white transition-colors duration-200">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                          d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9">
                                    </path>
                                </svg>
                                <!-- Notification Badge -->
                                <span x-show="unreadCount > 0" 
                                      x-text="unreadCount > 99 ? '99+' : unreadCount"
                                      class="absolute -top-1 -right-1 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white bg-red-500 rounded-full min-w-[1.25rem] h-5">
                                </span>
                            </button>
                            
                            <!-- Notifications Dropdown -->
                            <div x-show="notificationsOpen" 
                                 @click.away="notificationsOpen = false"
                                 x-cloak
                                 class="absolute right-0 mt-2 w-80 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 z-50"
                                 style="display: none;">
                                <!-- Header -->
                                <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700">
                                    <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Notifications</h3>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                        <span x-text="unreadCount"></span> unread notifications
                                    </p>
                                </div>
                                
                                <!-- Notifications List -->
                                <div class="max-h-96 overflow-y-auto">
                                    <template x-for="notification in notifications" :key="notification.id">
                                        <a :href="notification.url" 
                                           @click="markAsRead(notification.id)"
                                           class="block px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-150 border-b border-gray-100 dark:border-gray-700">
                                            <div class="flex items-start">
                                                <div class="flex-1 min-w-0">
                                                    <p class="text-sm font-medium text-gray-900 dark:text-white" 
                                                       x-text="notification.title"></p>
                                                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1" 
                                                       x-text="notification.message"></p>
                                                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-1" 
                                                       x-text="new Date(notification.notified_at).toLocaleString()"></p>
                                                </div>
                                                <div class="ml-3 flex-shrink-0">
                                                    <div class="h-2 w-2 bg-blue-500 rounded-full" 
                                                         x-show="!notification.is_read"></div>
                                                </div>
                                            </div>
                                        </a>
                                    </template>
                                    
                                    <!-- Empty State -->
                                    <div x-show="notifications.length === 0" 
                                         class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                                        <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                  d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9">
                                            </path>
                                        </svg>
                                        <p class="mt-2 text-sm">No new notifications</p>
                                    </div>
                                </div>
                                
                                <!-- Footer -->
                                <div x-show="notifications.length > 0" 
                                     class="px-4 py-3 border-t border-gray-200 dark:border-gray-700">
                                    <button @click="markAllAsRead()" 
                                            class="text-sm text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300">
                                        Mark all as read
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>

                <!-- Navigation -->
                <nav class="flex-1 px-2 py-4 space-y-1 overflow-y-auto scrollbar-thin">
                    <!-- Dashboard Link -->
                    <a href="{{ route('admin.portal') }}"
                        class="group flex items-center px-4 py-3 text-sm font-medium rounded-md {{ request()->routeIs('admin.portal') ? 'bg-white/20 text-white shadow-md' : 'text-white/80 hover:bg-white/10 hover:text-white' }} transition-colors duration-200">
                        <svg class="mr-3 h-6 w-6 {{ request()->routeIs('admin.portal') ? 'text-white' : 'text-white/60 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 12l2-2m0 0l7-7 7 7v6a3 3 0 11-3 3H6a3 3 0 01-3-3v-6a3 3 0 00-3-3h6a3 3 0 013 3v6a3 3 0 013-3z">
                            </path>
                        </svg>
                        Dashboard
                    </a>

                    <!-- User Management -->
                    <a href="{{ route('admin.users.index') }}"
                        class="group flex items-center px-4 py-3 text-sm font-medium rounded-md {{ request()->routeIs('admin.users.*') ? 'bg-white/20 text-white shadow-md' : 'text-white/80 hover:bg-white/10 hover:text-white' }} transition-colors duration-200">
                        <svg class="mr-3 h-6 w-6 {{ request()->routeIs('admin.users.*') ? 'text-white' : 'text-white/60 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z">
                            </path>
                        </svg>
                        User Management
                    </a>

                    <!-- Subscription Management -->
                    <a href="{{ route('admin.subscriptions.index') }}"
                        class="group flex items-center px-4 py-3 text-sm font-medium rounded-md {{ request()->routeIs('admin.subscriptions.*') ? 'bg-white/20 text-white shadow-md' : 'text-white/80 hover:bg-white/10 hover:text-white' }} transition-colors duration-200">
                        <svg class="mr-3 h-6 w-6 {{ request()->routeIs('admin.subscriptions.*') ? 'text-white' : 'text-white/60 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5v-2a3 3 0 00-5.356-3.356A3 3 0 0014 12V4a3 3 0 00-3-3H7a3 3 0 00-3 3v8a3 3 0 003 3h14v-2h-5a2 2 0 00-2-2v-4a2 2 0 012-2h4a2 2 0 012 2V4a2 2 0 01-2 2z">
                            </path>
                        </svg>
                        Subscriptions
                        @php
                            $pendingCount = \App\Models\Subscription::where('status', 'pending')->count();
                        @endphp
                        @if ($pendingCount > 0)
                            <span
                                class="ml-2 inline-block py-0.5 px-2 text-xs font-medium rounded-full bg-red-500 text-white">
                                {{ $pendingCount }}
                            </span>
                        @endif
                    </a>

                    <!-- Plan Management -->
                    <a href="{{ route('admin.subscription-plans.index') }}"
                        class="group flex items-center px-4 py-3 text-sm font-medium rounded-md {{ request()->routeIs('admin.subscription-plans.*') ? 'bg-white/20 text-white shadow-md' : 'text-white/80 hover:bg-white/10 hover:text-white' }} transition-colors duration-200">
                        <svg class="mr-3 h-6 w-6 {{ request()->routeIs('admin.subscription-plans.*') ? 'text-white' : 'text-white/60 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10">
                            </path>
                        </svg>
                        Plan Management
                    </a>

                    <!-- Quiz Management -->
                    <a href="{{ route('admin.quizzes.index') }}"
                        class="group flex items-center px-4 py-3 text-sm font-medium rounded-md {{ request()->routeIs('admin.quizzes.*') || request()->routeIs('admin.questions.*') || request()->routeIs('admin.quiz.attempts.*') || request()->routeIs('admin.test.*') ? 'bg-white/20 text-white shadow-md' : 'text-white/80 hover:bg-white/10 hover:text-white' }} transition-colors duration-200">
                        <svg class="mr-3 h-6 w-6 {{ request()->routeIs('admin.quizzes.*') || request()->routeIs('admin.questions.*') || request()->routeIs('admin.quiz.attempts.*') || request()->routeIs('admin.test.*') ? 'text-white' : 'text-white/60 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01">
                            </path>
                        </svg>
                        Quiz Management
                    </a>

                    
                    <!-- Forum Management -->
                    <a href="{{ route('admin.forum.index') }}"
                        class="group flex items-center px-4 py-3 text-sm font-medium rounded-md {{ request()->routeIs('admin.forum.*') ? 'bg-white/20 text-white shadow-md' : 'text-white/80 hover:bg-white/10 hover:text-white' }} transition-colors duration-200">
                        <svg class="mr-3 h-6 w-6 {{ request()->routeIs('admin.forum.*') ? 'text-white' : 'text-white/60 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4h.01z">
                            </path>
                        </svg>
                        Forum Management
                    </a>

                    <!-- Reports & Analytics -->
                    <a href="{{ route('admin.reports.index') }}"
                        class="group flex items-center px-4 py-3 text-sm font-medium rounded-md {{ request()->routeIs('admin.reports.*') ? 'bg-white/20 text-white shadow-md' : 'text-white/80 hover:bg-white/10 hover:text-white' }} transition-colors duration-200">
                        <svg class="mr-3 h-6 w-6 {{ request()->routeIs('admin.reports.*') ? 'text-white' : 'text-white/60 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2V14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                            </path>
                        </svg>
                        Reports
                    </a>

                    <!-- Settings -->
                    <a href="{{ route('admin.settings.index') }}"
                        class="group flex items-center px-4 py-3 text-sm font-medium rounded-md {{ request()->routeIs('admin.settings.*') ? 'bg-white/20 text-white shadow-md' : 'text-white/80 hover:bg-white/10 hover:text-white' }} transition-colors duration-200">
                        <svg class="mr-3 h-6 w-6 {{ request()->routeIs('admin.settings.*') ? 'text-white' : 'text-white/60 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c-.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426-1.756-2.924-1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c.94-1.543.826-3.31 2.37-2.37.996.608l-2.829-2.829m4.244 4.244l2.829-2.829m0 0L16.973 11.025c-.678-.678-1.778-1.778-2.456 0">
                            </path>
                        </svg>
                        Settings
                    </a>
                </nav>

                <!-- User Section -->
                <div class="p-4 border-t border-white/20">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-10 w-10 rounded-full bg-white/20 p-2" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <a href="{{ route('profile.show', ['locale' => app()->getLocale()]) }}" 
                               class="block hover:cursor-pointer">
                                <p class="text-sm font-medium text-white">{{ Auth::user()->name }}</p>
                                <p class="text-xs font-medium text-white/80">{{ Auth::user()->email }}</p>
                            </a>
                        </div>
                        <div class="ml-auto">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="text-white/80 hover:text-white">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1">
                                        </path>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col overflow-hidden bg-white dark:bg-gray-900 md:ml-64">
            <!-- Main Content Wrapper -->
            <div class="flex-1 overflow-auto pb-16 md:pb-0">

                <!-- Mobile sidebar (hidden by default) -->
                <div x-show="mobileMenuOpen" @click.away="mobileMenuOpen = false" class="md:hidden fixed inset-0 z-40"
                    x-cloak style="display: none;">
                    <div class="fixed inset-0 bg-gray-600 bg-opacity-75" @click="mobileMenuOpen = false"></div>
                    <div class="relative flex-1 flex flex-col max-w-xs w-full bg-white dark:bg-gray-800" style="height: calc(100vh - 4rem);">
                        <div class="absolute top-0 right-0 -mr-14 p-1">
                            <button @click="mobileMenuOpen = false"
                                class="flex items-center justify-center h-12 w-12 rounded-full focus:outline-none focus:bg-gray-600">
                                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                        <div class="flex-1 h-0 pt-5 pb-4 overflow-y-auto">
                            <div class="flex-shrink-0 flex items-center px-4 mb-4">
                                <a href="{{ route('home', ['locale' => app()->getLocale()]) }}" class="text-xl font-bold text-gray-900 dark:text-white">
                                    MK Driving 
                                </a>
                            </div>
                            <nav class="px-2 space-y-1">
                                <!-- Dashboard -->
                                <a href="{{ route('admin.portal') }}" class="group flex items-center px-2 py-2 text-base font-medium rounded-md {{ request()->routeIs('admin.portal') ? 'bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white' }}">
                                    <svg class="mr-4 h-6 w-6 {{ request()->routeIs('admin.portal') ? 'text-gray-500 dark:text-gray-300' : 'text-gray-400 dark:text-gray-400 group-hover:text-gray-500 dark:group-hover:text-gray-300' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                                    </svg>
                                    Dashboard
                                </a>

                                <!-- Users -->
                                <a href="{{ route('admin.users.index') }}" class="group flex items-center px-2 py-2 text-base font-medium rounded-md {{ request()->routeIs('admin.users.*') ? 'bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white' }}">
                                    <svg class="mr-4 h-6 w-6 {{ request()->routeIs('admin.users.*') ? 'text-gray-500 dark:text-gray-300' : 'text-gray-400 dark:text-gray-400 group-hover:text-gray-500 dark:group-hover:text-gray-300' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                    </svg>
                                    Users
                                </a>

                                <!-- Roles & Permissions -->
                                <a href="{{ route('admin.settings.index') }}" class="group flex items-center px-2 py-2 text-base font-medium rounded-md {{ request()->routeIs('admin.settings.*') ? 'bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white' }}">
                                    <svg class="mr-4 h-6 w-6 {{ request()->routeIs('admin.settings.*') ? 'text-gray-500 dark:text-gray-300' : 'text-gray-400 dark:text-gray-400 group-hover:text-gray-500 dark:group-hover:text-gray-300' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                                    </svg>
                                    Roles & Permissions
                                </a>

                                <!-- Quizzes & Questions -->
                                <a href="{{ route('admin.quizzes.index') }}" class="group flex items-center px-2 py-2 text-base font-medium rounded-md {{ request()->routeIs('admin.quizzes.*') || request()->routeIs('admin.questions.*') || request()->routeIs('admin.quiz.attempts.*') || request()->routeIs('admin.test.*') ? 'bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white' }}">
                                    <svg class="mr-4 h-6 w-6 {{ request()->routeIs('admin.quizzes.*') || request()->routeIs('admin.questions.*') || request()->routeIs('admin.quiz.attempts.*') || request()->routeIs('admin.test.*') ? 'text-gray-500 dark:text-gray-300' : 'text-gray-400 dark:text-gray-400 group-hover:text-gray-500 dark:group-hover:text-gray-300' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                                    </svg>
                                    Quiz Management
                                </a>

                                <!-- Plan Management -->
                                <a href="{{ route('admin.subscription-plans.index') }}" class="group flex items-center px-2 py-2 text-base font-medium rounded-md {{ request()->routeIs('admin.subscription-plans.*') ? 'bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white' }}">
                                    <svg class="mr-4 h-6 w-6 {{ request()->routeIs('admin.subscription-plans.*') ? 'text-gray-500 dark:text-gray-300' : 'text-gray-400 dark:text-gray-400 group-hover:text-gray-500 dark:group-hover:text-gray-300' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                    </svg>
                                    Plan Management
                                </a>

                                <!-- Subscriptions & Payments -->
                                <a href="{{ route('admin.subscriptions.index') }}" class="group flex items-center px-2 py-2 text-base font-medium rounded-md {{ request()->routeIs('admin.subscriptions.*') || request()->routeIs('admin.payments.*') || request()->routeIs('admin.plans.*') ? 'bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white' }}">
                                    <svg class="mr-4 h-6 w-6 {{ request()->routeIs('admin.subscriptions.*') || request()->routeIs('admin.payments.*') || request()->routeIs('admin.plans.*') ? 'text-gray-500 dark:text-gray-300' : 'text-gray-400 dark:text-gray-400 group-hover:text-gray-500 dark:group-hover:text-gray-300' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 3v2m10-2v2m-10 5h.01M12 12h.01M16 12h.01M7 16h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v7a2 2 0 002 2z"></path>
                                    </svg>
                                    Subscriptions
                                </a>

                                <!-- Forum Management -->
                                <a href="{{ route('admin.forum.index') }}" class="group flex items-center px-2 py-2 text-base font-medium rounded-md {{ request()->routeIs('admin.forum.*') ? 'bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white' }}">
                                    <svg class="mr-4 h-6 w-6 {{ request()->routeIs('admin.forum.*') ? 'text-gray-500 dark:text-gray-300' : 'text-gray-400 dark:text-gray-400 group-hover:text-gray-500 dark:group-hover:text-gray-300' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4h.01z"></path>
                                    </svg>
                                    Forum Management
                                </a>

                                <!-- Reports & Analytics -->
                                <a href="{{ route('admin.reports.index') }}" class="group flex items-center px-2 py-2 text-base font-medium rounded-md {{ request()->routeIs('admin.reports.*') ? 'bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white' }}">
                                    <svg class="mr-4 h-6 w-6 {{ request()->routeIs('admin.reports.*') ? 'text-gray-500 dark:text-gray-300' : 'text-gray-400 dark:text-gray-400 group-hover:text-gray-500 dark:group-hover:text-gray-300' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2V14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                    </svg>
                                    Reports
                                </a>

                                <!-- Settings -->
                                <a href="{{ route('admin.settings.index') }}" class="group flex items-center px-2 py-2 text-base font-medium rounded-md {{ request()->routeIs('admin.settings.*') ? 'bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white' }}">
                                    <svg class="mr-4 h-6 w-6 {{ request()->routeIs('admin.settings.*') ? 'text-gray-500 dark:text-gray-300' : 'text-gray-400 dark:text-gray-400 group-hover:text-gray-500 dark:group-hover:text-gray-300' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c-.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426-1.756-2.924-1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c.94-1.543.826-3.31 2.37-2.37.996.608l-2.829-2.829m4.244 4.244l2.829-2.829m0 0L16.973 11.025c-.678-.678-1.778-1.778-2.456 0"></path>
                                    </svg>
                                    Settings
                                </a>
                            </nav>
                        </div>
                        <div class="flex-shrink-0 flex border-t border-gray-200 dark:border-gray-700 p-4">
                            <a href="#" class="flex-shrink-0 group block">
                                <div class="flex items-center">
                                    <div>
                                        <div class="flex items-center">
                                            <img class="inline-block h-9 w-9 rounded-full" src="{{ Auth::user()->profile_photo_url ?? 'https://ui-avatars.com/api/?name='.urlencode(Auth::user()->name).'&color=7F9CF5&background=EBF4FF' }}" alt="{{ Auth::user()->name }}" />
                                            <div class="ml-3">
                                                <p class="text-sm font-medium text-gray-700 dark:text-gray-200 group-hover:text-gray-900 dark:group-hover:text-white">
                                                    {{ Auth::user()->name }}
                                                </p>
                                                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 group-hover:text-gray-700 dark:group-hover:text-gray-300">
                                                    View profile
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                            </div>
                            <nav class="mt-5 px-2 space-y-1">
                                <a href="{{ route('admin.portal', ['locale' => app()->getLocale()]) }}"
                                    class="group flex items-center px-2 py-2 text-base font-medium rounded-md text-gray-900 dark:text-white bg-gray-200 dark:bg-gray-700">
                                    <svg class="mr-4 h-6 w-6" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
                                        </path>
                                    </svg>
                                    Dashboard
                                </a>
                                <a href="{{ route('admin.subscriptions.pending') }}"
                                    class="group flex items-center px-2 py-2 text-base font-medium rounded-md text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-600">
                                    <svg class="mr-4 h-6 w-6" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    Subscription Requests
                                    @php
                                        $pendingCount = \App\Models\Subscription::where('status', 'pending')->count();
                                    @endphp
                                    @if ($pendingCount > 0)
                                        <span
                                            class="ml-auto inline-block py-0.5 px-2 text-xs font-medium rounded-full bg-red-500 text-white">
                                            {{ $pendingCount }}
                                        </span>
                                    @endif
                                </a>
                            </nav>
                        </div>
                    </div>
                </div>

                <!-- Main content -->
                <div class="max-w-7xl mx-auto px-2 py-6 w-full">
                    @if (session('success'))
                        <div class="mb-6 bg-green-100 dark:bg-green-900/20 border border-green-400 dark:border-green-600 text-green-700 dark:text-green-300 px-4 py-3 rounded relative"
                            role="alert">
                            <span class="block sm:inline">{{ session('success') }}</span>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="mb-6 bg-red-100 dark:bg-red-900/20 border border-red-400 dark:border-red-600 text-red-700 dark:text-red-300 px-4 py-3 rounded relative"
                            role="alert">
                            <span class="block sm:inline">{{ session('error') }}</span>
                        </div>
                    @endif

                    @yield('content')
                </div>
                </main>
            </div>
            
            <!-- Mobile Bottom Navigation -->
            <div class="md:hidden fixed bottom-0 left-0 right-0 bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 z-50">
                <div class="flex items-center justify-between px-4 py-2">
                    <!-- Menu Button -->
                    <button @click="mobileMenuOpen = !mobileMenuOpen; console.log('Menu button clicked, mobileMenuOpen:', mobileMenuOpen)" onclick="console.log('Native onclick triggered')" class="flex items-center justify-center p-3 text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors duration-200">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path x-show="!mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                            <path x-show="mobileMenuOpen" x-cloak stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        <span class="ml-2 text-sm font-medium">Menu</span>
                    </button>
                    
                    <!-- Logout Button -->
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="flex items-center justify-center p-3 text-gray-600 dark:text-gray-300 hover:text-red-600 dark:hover:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors duration-200">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                            </svg>
                            <span class="ml-2 text-sm font-medium">Logout</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <script>
            document.addEventListener('alpine:init', () => {
                console.log('AlpineJS initializing...');
                Alpine.data('admin', () => ({
                    sidebarOpen: false,
                    mobileMenuOpen: false,
                    userMenuOpen: false,

                    init() {
                        console.log('Admin component initialized, mobileMenuOpen:', this.mobileMenuOpen);
                    },

                    toggleSidebar() {
                        this.sidebarOpen = !this.sidebarOpen;
                        console.log('Sidebar toggled:', this.sidebarOpen);
                    },

                    closeSidebar() {
                        this.sidebarOpen = false;
                        console.log('Sidebar closed');
                    },

                    toggleMobileMenu() {
                        this.mobileMenuOpen = !this.mobileMenuOpen;
                        console.log('Mobile menu toggled:', this.mobileMenuOpen);
                    },

                    closeMobileMenu() {
                        this.mobileMenuOpen = false;
                        console.log('Mobile menu closed');
                    },

                    toggleUserMenu() {
                        this.userMenuOpen = !this.userMenuOpen;
                    },

                    closeUserMenu() {
                        this.userMenuOpen = false;
                    },

                    init() {
                        // Close menus when clicking outside
                        document.addEventListener('click', (e) => {
                            const mobileMenu = document.getElementById('mobile-menu');
                            const mobileMenuButton = document.querySelector(
                                '[x-on:click="toggleMobileMenu"]');
                            const userMenu = document.querySelector('[x-show="userMenuOpen"]');
                            const userMenuButton = document.querySelector(
                                '[x-on:click="toggleUserMenu"]');

                            if (mobileMenu && mobileMenuButton) {
                                if (!mobileMenu.contains(e.target) && !mobileMenuButton.contains(e
                                        .target)) {
                                    this.mobileMenuOpen = false;
                                }
                            }

                            if (userMenu && userMenuButton) {
                                if (!userMenu.contains(e.target) && !userMenuButton.contains(e
                                        .target)) {
                                    this.userMenuOpen = false;
                                }
                            }
                        });

                        // Close mobile menu when navigating
                        const mobileLinks = document.querySelectorAll('#mobile-menu a');
                        mobileLinks.forEach(link => {
                            link.addEventListener('click', () => {
                                this.mobileMenuOpen = false;
                            });
                        });
                    },

                    // Notification functions
                    markAsRead(notificationId) {
                        fetch('/admin/notifications/' + notificationId + '/read', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                'Content-Type': 'application/json'
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                // Update the notification in the list
                                const notification = this.notifications.find(n => n.id == notificationId);
                                if (notification) {
                                    notification.is_read = true;
                                    this.unreadCount = Math.max(0, this.unreadCount - 1);
                                }
                            }
                        })
                        .catch(error => console.error('Error marking notification as read:', error));
                    },

                    markAllAsRead() {
                        fetch('/admin/notifications/mark-all-read', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                'Content-Type': 'application/json'
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                // Mark all notifications as read
                                this.notifications.forEach(notification => {
                                    notification.is_read = true;
                                });
                                this.unreadCount = 0;
                            }
                        })
                        .catch(error => console.error('Error marking all notifications as read:', error));
                    }
                }));
            });
        </script>

        @stack('scripts')
</body>

</html>
