<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
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

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.tailwindcss.com"></script>
    
    <style>
        [x-cloak] { display: none !important; }
        body { 
            font-family: 'Figtree', Arial, sans-serif; 
            background-color: #f5f5f5;
            min-height: 100vh;
            line-height: 1.5;
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
            background-color: #1e40af;
            color: white;
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
        .table {
            width: 100%;
            border-collapse: collapse;
            margin: 1.5rem 0;
            background: white;
            border-radius: 0.5rem;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }
        .table th, .table td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid #e5e7eb;
        }
        .table th {
            background-color: #1e40af;
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
            background-color: #f8fafc;
        }
        .card {
            background: white;
            border-radius: 0.5rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            padding: 1.5rem;
            margin-bottom: 1.5rem;
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
        .card-title {
            font-size: 1.125rem;
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 0.75rem;
        }
        .card-text {
            color: #475569;
            line-height: 1.6;
        }
    </style>
</head>
<body class="antialiased bg-gray-100 dark:bg-gray-900 text-gray-900 dark:text-gray-100">
    <div class="flex h-screen">
        <!-- Sidebar -->
        <div class="hidden md:flex md:flex-shrink-0">
            <div class="flex flex-col w-64 bg-blue-800 text-white h-screen sticky top-0">
                <!-- Logo -->
                <a href="{{ route('home', ['locale' => app()->getLocale()]) }}" class="block">
                    <div class="flex items-center justify-left h-16 px-4 bg-blue-900 hover:bg-blue-800 transition-colors">
                        <span class="text-xl font-bold">MK Driving</span>
                    </div>
                </a>
                
                <!-- Navigation -->
                <nav class="flex-1 px-2 py-4 space-y-1">
                    <!-- Admin Portal Link -->
                    <a href="{{ route('admin.portal') }}" class="group flex items-center px-4 py-3 text-sm font-medium rounded-md {{ request()->routeIs('admin.portal') ? 'bg-blue-700 text-white' : 'text-blue-100 hover:bg-blue-700 hover:text-white' }}">
                        <svg class="mr-3 h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                        {{ __('navigation.admin_portal') }}
                    </a>

                    <!-- Subscriptions Dropdown -->
                    <div x-data="{ open: false }" class="relative">
                        <button @click="open = !open" class="w-full flex items-center justify-between px-4 py-3 text-sm font-medium rounded-md {{ request()->routeIs('admin.subscriptions.*') ? 'bg-blue-700 text-white' : 'text-blue-100 hover:bg-blue-700 hover:text-white' }}">
                            <div class="flex items-center">
                                <svg class="mr-3 h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                {{ __('dashboard.navigation.subscriptions') }}
                                @php
                                    $pendingCount = \App\Models\Subscription::where('status', 'pending')->count();
                                @endphp
                                @if($pendingCount > 0)
                                    <span class="ml-2 inline-block py-0.5 px-2 text-xs font-medium rounded-full bg-red-500 text-white">
                                        {{ $pendingCount }}
                                    </span>
                                @endif
                            </div>
                            <svg :class="{'transform -rotate-90': open}" class="ml-2 h-4 w-4 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        
                        <!-- Dropdown menu -->
                        <div x-show="open" @click.away="open = false" class="mt-1 ml-2 space-y-1">
                            <a href="{{ route('admin.subscriptions.pending') }}" class="group flex items-center px-4 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.subscriptions.pending') ? 'bg-blue-600 text-white' : 'text-blue-100 hover:bg-blue-600 hover:text-white' }}">
                                <span class="mr-2">•</span>
                                Pending Subscriptions
                                @if($pendingCount > 0)
                                    <span class="ml-auto inline-block py-0.5 px-2 text-xs font-medium rounded-full bg-red-500 text-white">
                                        {{ $pendingCount }}
                                    </span>
                                @endif
                            </a>
                            <a href="{{ route('admin.subscriptions.active') }}" class="group flex items-center px-4 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.subscriptions.active') ? 'bg-blue-600 text-white' : 'text-blue-100 hover:bg-blue-600 hover:text-white' }}">
                                <span class="mr-2">•</span>
                                Active Subscriptions
                            </a>
                        </div>
                    </div>

                    <!-- Users Link -->
                    <a href="{{ route('admin.users.index') }}" class="group flex items-center px-4 py-3 text-sm font-medium rounded-md {{ request()->routeIs('admin.users.*') ? 'bg-blue-700 text-white' : 'text-blue-100 hover:bg-blue-700 hover:text-white' }}">
                        <svg class="mr-3 h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                        {{ __('Users') }}
                    </a>
                </nav>
                
                <!-- User Section -->
                <div class="p-4 border-t border-blue-700">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-10 w-10 rounded-full bg-blue-600 p-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-white">{{ Auth::user()->name }}</p>
                            <p class="text-xs font-medium text-blue-200">{{ Auth::user()->email }}</p>
                        </div>
                        <div class="ml-auto">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="text-blue-300 hover:text-white">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col overflow-hidden bg-white dark:bg-gray-900">
            <!-- Top Navigation -->
            <nav class="bg-white border-b border-gray-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex">
                        <!-- Logo -->
                        <div class="shrink-0 flex items-center">
                            <a href="{{ route('admin.portal', ['locale' => app()->getLocale()]) }}" class="text-xl font-bold text-indigo-600">
                                Management
                            </a>
                        </div>

                        
                    </div>

                    <!-- Settings Dropdown -->
                    <div class="hidden sm:flex sm:items-center sm:ml-6">
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" class="flex items-center text-sm font-medium text-gray-700 hover:text-gray-900 focus:outline-none">
                                <div>{{ Auth::user()->first_name }} {{ Auth::user()->last_name }}</div>
                                <div class="ml-1">
                                    <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </button>

                            <div x-show="open" @click.away="open = false" 
                                 class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg py-1 bg-white ring-1 ring-black ring-opacity-5 focus:outline-none z-50"
                                 role="menu" aria-orientation="vertical" aria-labelledby="user-menu">
                                <a href="{{ route('dashboard', ['locale' => app()->getLocale()]) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem">
                                    {{ __('Go to User Dashboard') }}
                                </a>
                                <a href="{{ route('profile.show', ['locale' => app()->getLocale()]) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem">
                                    {{ __('navigation.profile') }}
                                </a>
                                <form method="POST" action="{{ route('logout', ['locale' => app()->getLocale()]) }}">
                                    @csrf
                                    <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem">
                                        {{ __('navigation.logout') }}
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Mobile menu button -->
                    <div class="-mr-2 flex items-center sm:hidden">
                        <button @click="mobileMenuOpen = !mobileMenuOpen" type="button" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-indigo-500">
                            <span class="sr-only">Open main menu</span>
                            <svg class="block h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Mobile menu, show/hide based on menu state. -->
            <div x-show="mobileMenuOpen" class="sm:hidden" id="mobile-menu">
                <div class="pt-2 pb-3 space-y-1">
                    <a href="{{ route('admin.portal', ['locale' => app()->getLocale()]) }}" class="block pl-3 pr-4 py-2 border-l-4 {{ request()->routeIs('admin.portal') ? 'bg-indigo-50 border-indigo-500 text-indigo-700' : 'border-transparent text-gray-600 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-800' }} text-base font-medium">
                        {{ __('navigation.admin_portal') }}
                    </a>
                    <a href="{{ route('admin.subscriptions.pending', ['locale' => app()->getLocale()]) }}" class="block pl-3 pr-4 py-2 border-l-4 {{ request()->routeIs('admin.subscriptions.pending') ? 'bg-indigo-50 border-indigo-500 text-indigo-700' : 'border-transparent text-gray-600 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-800' }} text-base font-medium pl-10">
                        • Pending Subscriptions
                        @php
                            $pendingCount = \App\Models\Subscription::where('status', 'pending')->count();
                        @endphp
                        @if($pendingCount > 0)
                            <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                {{ $pendingCount }}
                            </span>
                        @endif
                    </a>
                    <a href="{{ route('admin.subscriptions.active', ['locale' => app()->getLocale()]) }}" class="block pl-3 pr-4 py-2 border-l-4 {{ request()->routeIs('admin.subscriptions.active') ? 'bg-indigo-50 border-indigo-500 text-indigo-700' : 'border-transparent text-gray-600 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-800' }} text-base font-medium pl-10">
                        • Active Subscriptions
                    </a>
                    <a href="{{ route('admin.users.index', ['locale' => app()->getLocale()]) }}" class="block pl-3 pr-4 py-2 border-l-4 {{ request()->routeIs('admin.users.*') ? 'bg-indigo-50 border-indigo-500 text-indigo-700' : 'border-transparent text-gray-600 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-800' }} text-base font-medium">
                        {{ __('Users') }}
                    </a>
                </div>
                <div class="pt-4 pb-3 border-t border-gray-200">
                    <div class="flex items-center px-4">
                        <div class="flex-shrink-0">
                            <svg class="h-10 w-10 rounded-full bg-gray-200 text-gray-400" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M24 20.993V24H0v-2.996A14.977 14.977 0 0112.004 15c4.904 0 9.26 2.354 11.996 5.993zM16.002 8.999a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <div class="text-base font-medium text-gray-800">{{ Auth::user()->first_name }} {{ Auth::user()->last_name }}</div>
                            <div class="text-sm font-medium text-gray-500">{{ Auth::user()->email }}</div>
                        </div>
                    </div>
                    <div class="mt-3 space-y-1">
                        <a href="{{ route('dashboard', ['locale' => app()->getLocale()]) }}" class="block px-4 py-2 text-base font-medium text-gray-500 hover:text-gray-800 hover:bg-gray-100">
                            {{ __('Go to User Dashboard') }}
                        </a>
                        <a href="{{ route('profile.show', ['locale' => app()->getLocale()]) }}" class="block px-4 py-2 text-base font-medium text-gray-500 hover:text-gray-800 hover:bg-gray-100">
                            {{ __('navigation.profile') }}
                        </a>
                        <form method="POST" action="{{ route('logout', ['locale' => app()->getLocale()]) }}">
                            @csrf
                            <button type="submit" class="block w-full text-left px-4 py-2 text-base font-medium text-gray-500 hover:text-gray-800 hover:bg-gray-100">
                                {{ __('navigation.logout') }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </nav>

        

        <!-- Main Content Wrapper -->
        <div class="flex-1 overflow-auto">
            <!-- This div is a spacer to account for the fixed sidebar -->
            <!-- Mobile header -->
            <div class="md:hidden">
                <!-- Mobile header content here -->
            </div>
            
            <!-- Mobile header -->
            <header class="md:hidden bg-blue-800 text-white shadow">
                <div class="flex items-center justify-between px-4 py-3">
                    <div class="flex items-center">
                        <button @click="sidebarOpen = true" class="text-white focus:outline-none">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>
                        <a href="{{ route('home', ['locale' => app()->getLocale()]) }}" class="ml-4 text-xl font-bold">MK Driving</a>
                    </div>
                    <div class="flex items-center">
                        <button @click="mobileMenuOpen = !mobileMenuOpen" class="text-white focus:outline-none">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>
                    </div>
                </div>
            </header>

            <!-- Mobile sidebar (hidden by default) -->
            <div x-show="sidebarOpen" @click.away="sidebarOpen = false" class="md:hidden fixed inset-0 z-40" x-cloak style="display: none;">
                <div class="fixed inset-0 bg-gray-600 bg-opacity-75" @click="sidebarOpen = false"></div>
                <div class="relative flex-1 flex flex-col max-w-xs w-full bg-blue-800">
                    <div class="absolute top-0 right-0 -mr-14 p-1">
                        <button @click="sidebarOpen = false" class="flex items-center justify-center h-12 w-12 rounded-full focus:outline-none focus:bg-gray-600">
                            <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                    <div class="flex-1 h-0 pt-5 pb-4 overflow-y-auto">
                        <div class="flex-shrink-0 flex items-center px-4">
                            <span class="text-xl font-bold text-white">MK Driving Admin</span>
                        </div>
                        <nav class="mt-5 px-2 space-y-1">
                            <a href="{{ route('admin.portal', ['locale' => app()->getLocale()]) }}" class="group flex items-center px-2 py-2 text-base font-medium rounded-md text-white bg-blue-700">
                                <svg class="mr-4 h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                                </svg>
                                Dashboard
                            </a>
                            <a href="{{ route('admin.subscriptions.pending') }}" class="group flex items-center px-2 py-2 text-base font-medium rounded-md text-blue-100 hover:text-white hover:bg-blue-600">
                                <svg class="mr-4 h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Subscription Requests
                                @if($pendingCount > 0)
                                    <span class="ml-auto inline-block py-0.5 px-2 text-xs font-medium rounded-full bg-red-500 text-white">
                                        {{ $pendingCount }}
                                    </span>
                                @endif
                            </a>
                        </nav>
                    </div>
                </div>
            </div>

            <!-- Main content -->
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 w-full">
                    @if(session('success'))
                        <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                            <span class="block sm:inline">{{ session('success') }}</span>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                            <span class="block sm:inline">{{ session('error') }}</span>
                        </div>
                    @endif

                    @yield('content')
                </div>
            </main>
            </main>
        </div>
    </div>

    <!-- Alpine.js for mobile menu -->
    <script src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.8.2/dist/alpine.min.js" defer></script>
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('admin', () => ({
                sidebarOpen: false,
                mobileMenuOpen: false,
                userMenuOpen: false,
                
                toggleSidebar() {
                    this.sidebarOpen = !this.sidebarOpen;
                },
                
                closeSidebar() {
                    this.sidebarOpen = false;
                },
                
                toggleMobileMenu() {
                    this.mobileMenuOpen = !this.mobileMenuOpen;
                },
                
                closeMobileMenu() {
                    this.mobileMenuOpen = false;
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
                        const mobileMenuButton = document.querySelector('[x-on:click="toggleMobileMenu"]');
                        const userMenu = document.querySelector('[x-show="userMenuOpen"]');
                        const userMenuButton = document.querySelector('[x-on:click="toggleUserMenu"]');
                        
                        if (mobileMenu && mobileMenuButton) {
                            if (!mobileMenu.contains(e.target) && !mobileMenuButton.contains(e.target)) {
                                this.mobileMenuOpen = false;
                            }
                        }
                        
                        if (userMenu && userMenuButton) {
                            if (!userMenu.contains(e.target) && !userMenuButton.contains(e.target)) {
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
                }
            }));
        });
    </script>
    
    @stack('scripts')
</body>
</html>
