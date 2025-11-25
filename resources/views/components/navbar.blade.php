<nav class="w-full bg-[#0640d48e] shadow-lg z-50 overflow-hidden md:fixed" x-data="{ open: false, mobileMenuOpen: false }">
    <div class="w-full max-w-full md:max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-20">
            <!-- Mobile menu button (left on mobile, hidden on desktop) -->
            <div class="flex-shrink-0 flex items-center md:hidden">
                <button @click="mobileMenuOpen = !mobileMenuOpen" class="inline-flex items-center justify-center p-2 rounded-md text-white hover:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white transition-colors duration-200">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path x-show="!mobileMenuOpen" x-transition:enter="transition-opacity duration-200" x-transition:leave="transition-opacity duration-200" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path x-show="mobileMenuOpen" x-transition:enter="transition-opacity duration-200" x-transition:leave="transition-opacity duration-200" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Logo (centered on mobile, left on desktop) -->
            <div class="flex-1 flex justify-center md:justify-start">
                <a href="{{ route('home', ['locale' => app()->getLocale()]) }}" class="flex items-center space-x-2">
                    <img 
                        src="{{ asset('logo.png') }}" 
                        alt="MK Driving School Logo" 
                        class="h-12 w-12 sm:h-14 sm:w-14 md:h-14 md:w-14 rounded-lg shadow-md"
                        onerror="this.onerror=null; this.src='{{ asset('images/logo.png') }}'"
                    >
                    <div class="hidden sm:block ml-2">
                        <span class="text-2xl font-bold text-white leading-tight">
                            MK Driving School
                        </span>
                        <div class="text-xs  text-blue-200 font-medium mt-1">{{ __('navigation.tagline') }}</div>
                    </div>
                </a>
            </div>

            <!-- Desktop Navigation -->
            <div class="hidden md:flex md:items-center md:space-x-8">
                @php
                    $navLinks = [
                        [
                            'route' => 'home',
                            'text' => __('navigation.home'),
                            'routes' => ['home'],
                            'fragment' => null,
                            'is_home' => true
                        ],
                        [
                            'route' => 'plans',
                            'text' => __('navigation.pricing_plans'),
                            'routes' => ['plans'],
                            'fragment' => null,
                            'is_home' => false
                        ],
                        [
                            'route' => 'news.index',
                            'text' => __('navigation.news'),
                            'routes' => ['news.*', 'news.show'],
                            'fragment' => null,
                            'is_home' => false
                        ],
                        [
                            'route' => 'forum.index',
                            'text' => __('forum.page_title'),
                            'routes' => ['forum.*'],
                            'fragment' => null,
                            'is_home' => false
                        ]
                    ];
                @endphp

                @foreach($navLinks as $link)
                    @php
                        $isActive = false;
                        if ($link['route'] !== '#') {
                            foreach ($link['routes'] as $route) {
                                if (request()->routeIs($route)) {
                                    // If this is a home route, check if we're on the home page
                                    if ($link['is_home'] && $link['fragment']) {
                                        // For subscription plans, check the URL fragment
                                        $isActive = request()->is(trim(route($link['route'], [], false), '/') . '#' . $link['fragment']);
                                    } else {
                                        $isActive = true;
                                    }
                                    break;
                                }
                            }
                        }
                        $classes = $isActive 
                            ? 'text-white border-white' 
                            : 'text-blue-200 hover:text-white hover:border-blue-200 border-transparent';
                    @endphp
                    @php
                        $routeName = $link['route'];
                        $routeParams = [];
                        
                        // Always include locale parameter for all routes except #
                        if ($routeName !== '#') {
                            $routeParams['locale'] = app()->getLocale();
                        }
                        
                        // Merge any additional route parameters
                        if (isset($link['route_params']) && is_array($link['route_params'])) {
                            $routeParams = array_merge($routeParams, $link['route_params']);
                        }
                    @endphp
                    <a 
                        href="{{ $link['route'] === '#' ? '#' : route($routeName, $routeParams) }}{{ $link['fragment'] ? '#' . $link['fragment'] : '' }}"
                        class="{{ $classes }} inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium transition-colors duration-200"
                        @if($link['fragment']) 
                            x-data="{}" 
                            @click="$event.preventDefault(); 
                                   document.querySelector('#{{ $link['fragment'] }}').scrollIntoView({ behavior: 'smooth' });
                                   window.history.pushState(null, '', '{{ route($link['route'], ['locale' => app()->getLocale()]) }}#{{ $link['fragment'] }}');"
                        @endif
                    >
                        {{ $link['text'] }}
                    </a>
                @endforeach
            </div>

            <!-- Right side navigation (auth, language switcher) -->
            <div class="hidden md:flex md:items-center md:space-x-4">
                <!-- Language Switcher -->
                <div class="ml-4">
                    <x-language-switcher :currentLocale="app()->getLocale()" />
                </div>

                @auth
                    <div class="ml-6 relative">
                        <div class="flex items-center">
                            <a 
                                href="{{ route('dashboard', ['locale' => app()->getLocale()]) }}" 
                                class="text-gray-500 hover:text-gray-700 px-3 py-2 rounded-md text-sm font-medium"
                            >
                                {{ __('navigation.dashboard') }}
                            </a>
                            <form method="POST" action="{{ route('logout', ['locale' => app()->getLocale()]) }}" class="ml-2">
                                @csrf
                                <button 
                                    type="submit" 
                                    class="bg-[#023047] text-white px-4 py-2 rounded-full text-sm font-medium hover:bg-[#023047]/90"
                                >
                                    {{ __('navigation.logout') }}
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                    <div class="flex items-center space-x-4">
                        <a 
                            href="{{ route('login', app()->getLocale()) }}" 
                            class="text-gray-500 hover:text-gray-700 px-3 py-2 rounded-md text-sm font-medium"
                        >
                            {{ __('navigation.login') }}
                        </a>
                        <a 
                            href="{{ route('register', app()->getLocale()) }}" 
                            class="bg-[#023047] text-white px-4 py-2 rounded-full text-sm font-medium hover:bg-[#023047]/90"
                        >
                            {{ __('navigation.register') }}
                        </a>
                    </div>
                @endauth
            </div>

            <!-- Mobile auth (right side) -->
            <div class="flex items-center md:hidden">
                <!-- Mobile auth (always visible on mobile) -->
                <div class="md:hidden">
                    @auth
                        <a href="{{ route('logout') }}" 
                           onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                           class="text-sm font-medium text-white hover:text-blue-200 whitespace-nowrap">
                            {{ __('Sign Out') }}
                        </a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                            @csrf
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="text-sm font-medium text-white hover:text-blue-200 whitespace-nowrap">
                            {{ __('Sign In') }}
                        </a>
                    @endauth
                </div>
        </div>
    </div>

    <!-- Mobile menu overlay with backdrop blur -->
    <div x-show="mobileMenuOpen" 
        @click.self="mobileMenuOpen = false"
        x-transition:enter="transition-opacity ease-linear duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition-opacity ease-linear duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-gray-900/80 backdrop-blur-sm z-40 md:hidden"
        x-cloak>
        
        <!-- Mobile menu panel -->
        <div class="fixed inset-y-0 left-0 w-80 max-w-full bg-white dark:bg-gray-800 shadow-2xl overflow-y-auto z-50 transform transition-transform duration-300 ease-in-out"
            :class="mobileMenuOpen ? 'translate-x-0' : '-translate-x-full'"
            @click.away="mobileMenuOpen = false"
        >
            <div class="pt-2 pb-3 space-y-1">
                @foreach($navLinks as $link)
                    @php
                        $isActive = false;
                        if ($link['route'] !== '#') {
                            foreach ($link['routes'] as $route) {
                                if (request()->routeIs($route)) {
                                    // If this is a home route, check if we're on the home page
                                    if ($link['is_home'] && $link['fragment']) {
                                        // For subscription plans, check the URL fragment
                                        $isActive = request()->is(trim(route($link['route'], [], false), '/') . '#' . $link['fragment']);
                                    } else {
                                        $isActive = true;
                                    }
                                    break;
                                }
                            }
                        }
                        $routeName = $link['route'];
                        $mobileClasses = $isActive 
                            ? 'bg-blue-50 border-blue-500 text-blue-700' 
                            : 'border-transparent text-gray-600 hover:bg-gray-300 hover:border-gray-300 hover:text-gray-800';
                        
                        // Generate route parameters
                        $routeParams = [];
                        if ($routeName !== '#') {
                            $routeParams['locale'] = app()->getLocale();
                        }
                        
                        // Merge any additional route parameters
                        if (isset($link['route_params']) && is_array($link['route_params'])) {
                            $routeParams = array_merge($routeParams, $link['route_params']);
                        }
                        if (isset($link['route_params']) && is_array($link['route_params'])) {
                            $routeParams = array_merge($routeParams, $link['route_params']);
                        }
                    @endphp
                    <a 
                        href="{{ route($routeName, $routeParams) }}{{ $link['fragment'] ? '#' . $link['fragment'] : '' }}" 
                        class="block pl-3 pr-4 py-2 border-l-4 text-base font-medium {{ $mobileClasses }}"
                        @if($link['fragment']) 
                            x-data="{}" 
                            @click="$event.preventDefault(); 
                                   $dispatch('close-mobile-menu');
                                   document.querySelector('#{{ $link['fragment'] }}').scrollIntoView({ behavior: 'smooth' });
                                   window.history.pushState(null, '', '{{ route($link['route'], ['locale' => app()->getLocale()]) }}#{{ $link['fragment'] }}');"
                        @endif
                    >
                        {{ $link['text'] }}
                    </a>
                @endforeach
                
                <!-- Mobile Language Switcher -->
                <div class="pt-2 pb-2 border-t border-gray-700 dark:border-gray-600">
                    <div class="px-4 py-2">
                        <p class="text-xs font-medium text-gray-300 dark:text-gray-400 uppercase tracking-wider mb-2">{{ __('Change Language') }}</p>
                        <div class="flex flex-wrap gap-2">
                            @foreach(config('app.available_locales') as $locale => $name)
                                <a 
                                    href="{{ route('language.switch', $locale) }}" 
                                    class="px-3 py-1.5 text-sm rounded-md font-medium transition-colors duration-200
                                    {{ app()->getLocale() === $locale 
                                        ? 'bg-[#0369a1] text-white hover:bg-[#047ab6]' 
                                        : 'bg-gray-700 text-gray-200 hover:bg-gray-600 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600' }}"
                                >
                                    {{ strtoupper($locale) }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            <div class="pt-4 pb-3 border-t border-gray-200">
                @auth
                    <div class="flex items-center px-4">
                        <div class="flex-shrink-0">
                            <svg class="h-10 w-10 rounded-full text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <div class="text-base font-medium text-gray-800">{{ Auth::user()->name }}</div>
                            <div class="text-sm font-medium text-gray-500">{{ Auth::user()->email }}</div>
                        </div>
                    </div>
                    <div class="mt-3 space-y-1">
                        <a 
                            href="{{ route('dashboard', ['locale' => app()->getLocale()]) }}" 
                            class="block px-4 py-2 text-base font-medium text-gray-500 hover:text-gray-800 hover:bg-gray-100"
                        >
                            {{ __('navigation.dashboard') }}
                        </a>
                        <form method="POST" action="{{ route('logout', ['locale' => app()->getLocale()]) }}">
                            @csrf
                            <button 
                                type="submit" 
                                class="w-full text-left px-4 py-2 text-base font-medium text-gray-500 hover:text-gray-800 hover:bg-gray-100"
                            >
                                {{ __('navigation.logout') }}
                            </button>
                        </form>
                    </div>
                @else
                    <div class="space-y-1">
                        <a 
                            href="{{ route('login', app()->getLocale()) }}" 
                            class="block w-full px-4 py-2 text-base font-medium text-gray-500 hover:text-gray-800 hover:bg-gray-100"
                        >
                            {{ __('navigation.login') }}
                        </a>
                        <a 
                            href="{{ route('register', app()->getLocale()) }}" 
                            class="block w-full px-4 py-2 text-base font-medium text-[#023047] hover:bg-gray-100"
                        >
                            {{ __('navigation.register') }}
                        </a>
                    </div>
                @endauth
            </div>
        </div>
    </div>
</nav>

<!-- Add Alpine.js for mobile menu functionality -->
<style>
    /* Ensure content doesn't hide behind fixed header */
    @media (min-width: 768px) {
        body {
            padding-top: 5rem; /* Match the height of the header */
        }
    }
    
    /* Smooth scrolling */
    html {
        scroll-behavior: smooth;
    }
    
    /* Hide scrollbar for Chrome, Safari and Opera */
    .no-scrollbar::-webkit-scrollbar {
        display: none;
    }
    
    /* Hide scrollbar for IE, Edge and Firefox */
    .no-scrollbar {
        -ms-overflow-style: none;  /* IE and Edge */
        scrollbar-width: none;  /* Firefox */
    }
    
    /* Custom scrollbar for webkit browsers */
    ::-webkit-scrollbar {
        width: 8px;
        height: 8px;
    }
    
    ::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }
    
    ::-webkit-scrollbar-thumb {
        background: #888;
        border-radius: 10px;
    }
    
    ::-webkit-scrollbar-thumb:hover {
        background: #555;
    }
    
    /* Dark mode scrollbar */
    .dark ::-webkit-scrollbar-track {
        background: #1e293b;
    }
    
    .dark ::-webkit-scrollbar-thumb {
        background: #475569;
    }
    
    .dark ::-webkit-scrollbar-thumb:hover {
        background: #64748b;
    }
    
    /* Smooth transitions for theme toggle */
    .theme-transition *,
    .theme-transition *:before,
    .theme-transition *:after {
        transition: all 0.3s ease;
        transition-property: background-color, border-color, color, fill, stroke, opacity, box-shadow, transform;
    }
    
    /* Animation for dropdown menus */
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    .animate-fade-in {
        animation: fadeIn 0.2s ease-out forwards;
    }
    
    /* Custom focus styles */
    .focus-visible-ring:focus-visible {
        outline: none;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.5);
        border-radius: 0.375rem;
    }
    
    /* Hover effect for buttons */
    .btn-hover-effect {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    
    .btn-hover-effect:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    }
    
    .btn-hover-effect:active {
        transform: translateY(0);
    }
    
    /* Mobile menu styles */
    .mobile-menu-item {
        @apply px-4 py-3 text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-200 flex items-center space-x-3;
    }
    
    .mobile-menu-item svg {
        @apply h-5 w-5 text-gray-500 dark:text-gray-300;
    }
    
    /* Dark mode overrides for mobile menu */
    .dark .mobile-menu-item {
        @apply text-gray-200 hover:bg-gray-700;
    }
    
    .dark .mobile-menu-item svg {
        @apply text-gray-300;
    }
</style>

<script>
    // Initialize menu item animations when menu opens
    document.addEventListener('alpine:init', () => {
        Alpine.data('mobileMenu', () => ({
            open: false,
            init() {
                this.$watch('open', value => {
                    if (value) {
                        // Add animation classes after the menu is shown
                        this.$nextTick(() => {
                            const items = this.$refs.mobileMenu.querySelectorAll('.menu-item');
                            items.forEach((item, index) => {
                                setTimeout(() => {
                                    item.classList.add('animate-in');
                                }, index * 50);
                            });
                        });
                    } else {
                        // Remove animation classes when menu closes
                        const items = document.querySelectorAll('.menu-item');
                        items.forEach(item => {
                            item.classList.remove('animate-in');
                        });
                    }
                });
            }
        }));
    });
</script>
