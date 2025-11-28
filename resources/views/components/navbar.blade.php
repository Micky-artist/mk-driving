<nav class="w-full bg-[#0640d4] dark:bg-blue-900 shadow-lg z-50 overflow-hidden md:fixed transition-colors duration-200"
    x-data="{ open: false, mobileMenuOpen: false }">
    <div class="w-full max-w-full md:max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-20">
            <!-- Mobile menu button (left on mobile, hidden on desktop) -->
            <div class="flex-shrink-0 flex items-center md:hidden">
                <button @click="mobileMenuOpen = !mobileMenuOpen"
                    class="inline-flex items-center justify-center p-2 rounded-md text-white hover:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white transition-colors duration-200">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path x-show="!mobileMenuOpen" x-transition:enter="transition-opacity duration-200"
                            x-transition:leave="transition-opacity duration-200" class="inline-flex"
                            stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                        <path x-show="mobileMenuOpen" x-transition:enter="transition-opacity duration-200"
                            x-transition:leave="transition-opacity duration-200" class="inline-flex"
                            stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Logo (centered on mobile, left on desktop) -->
            <div class="flex-1 flex justify-center md:justify-start">
                <a href="{{ route('home', ['locale' => app()->getLocale()]) }}" class="flex items-center space-x-2">
                    <img src="{{ asset('logo.png') }}" alt="MK Driving School Logo"
                        class="h-12 w-12 sm:h-14 sm:w-14 md:h-14 md:w-14 rounded-lg shadow-md"
                        onerror="this.onerror=null; this.src='{{ asset('images/logo.png') }}'">
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
                            'is_home' => true,
                        ],
                        [
                            'route' => 'plans',
                            'text' => __('navigation.pricing_plans'),
                            'routes' => ['plans'],
                            'fragment' => null,
                            'is_home' => false,
                        ],
                        // [
                        //     'route' => 'news.index',
                        //     'text' => __('navigation.news'),
                        //     'routes' => ['news.*', 'news.show'],
                        //     'fragment' => null,
                        //     'is_home' => false
                        // ],
                        [
                            'route' => 'forum.index',
                            'text' => __('forum.page_title'),
                            'routes' => ['forum.*'],
                            'fragment' => null,
                            'is_home' => false,
                        ],
                    ];
                @endphp

                @foreach ($navLinks as $link)
                    @php
                        $isActive = false;
                        if ($link['route'] !== '#') {
                            foreach ($link['routes'] as $route) {
                                if (request()->routeIs($route)) {
                                    // If this is a home route, check if we're on the home page
            if ($link['is_home'] && $link['fragment']) {
                // For subscription plans, check the URL fragment
                $isActive = request()->is(
                    trim(route($link['route'], [], false), '/') . '#' . $link['fragment'],
                );
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
                    <a href="{{ $link['route'] === '#' ? '#' : route($routeName, $routeParams) }}{{ $link['fragment'] ? '#' . $link['fragment'] : '' }}"
                        class="{{ $classes }} inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium transition-colors duration-200"
                        @if ($link['fragment']) x-data="{}" 
                            @click="$event.preventDefault(); 
                                   document.querySelector('#{{ $link['fragment'] }}').scrollIntoView({ behavior: 'smooth' });
                                   window.history.pushState(null, '', '{{ route($link['route'], ['locale' => app()->getLocale()]) }}#{{ $link['fragment'] }}');" @endif>
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
                            @php
                                $isDashboardActive = request()->routeIs('dashboard*');
                                $dashboardClasses = $isDashboardActive
                                    ? 'bg-white text-blue-600 hover:bg-gray-50'
                                    : 'bg-white text-blue-600 hover:bg-gray-50';
                            @endphp
                            <a href="{{ route('dashboard', ['locale' => app()->getLocale()]) }}"
                                class="{{ $dashboardClasses }} inline-flex items-center px-4 py-2 rounded-full text-sm font-medium transition-colors duration-200 shadow-sm">
                                {{ __('navigation.dashboard') }}
                            </a>
                            <form method="POST" action="{{ route('logout', ['locale' => app()->getLocale()]) }}"
                                class="ml-2">
                                @csrf
                                <button type="submit"
                                    class="bg-[#023047] text-white px-4 py-2 rounded-full text-sm font-medium hover:bg-[#023047]/90">
                                    {{ __('navigation.logout') }}
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                    <div class="flex items-center space-x-3">
                        <a href="{{ route('login', app()->getLocale()) }}"
                            class="bg-white text-[#023047] px-4 py-2 rounded-full text-sm font-medium hover:bg-gray-100 border border-[#023047] transition-colors duration-200">
                            {{ __('navigation.login') }}
                        </a>
                        <a href="{{ route('register', app()->getLocale()) }}"
                            class="bg-[#023047] text-white px-4 py-2 rounded-full text-sm font-medium hover:bg-[#023047]/90 transition-colors duration-200">
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
                            {{ __('navigation.sign_out') }}
                        </a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                            @csrf
                        </form>
                    @else
                        <a href="{{ route('login') }}"
                            class="text-sm font-medium text-white hover:text-blue-200 whitespace-nowrap">
                            {{ __('navigation.sign_in') }}
                        </a>
                    @endauth
                </div>
            </div>
        </div>

        <!-- Mobile menu overlay with backdrop blur -->
        <div x-show="mobileMenuOpen" @click.self="mobileMenuOpen = false"
            x-transition:enter="transition-opacity ease-linear duration-200" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100" x-transition:leave="transition-opacity ease-linear duration-150"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
            class="md:hidden fixed inset-0 bg-black/70 backdrop-blur-sm z-40" x-cloak
            x-transition:enter="transition ease-in-out duration-300 transform"
            x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0"
            x-transition:leave="transition ease-in-out duration-300 transform" x-transition:leave-start="translate-x-0"
            x-transition:leave-end="-translate-x-full">

            <!-- Backdrop -->
            <div x-show="mobileMenuOpen" @click="mobileMenuOpen = false"
                class="fixed inset-0 bg-gray-900 bg-opacity-50 z-40 lg:hidden"
                x-transition:enter="transition-opacity ease-linear duration-300" x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100" x-transition:leave="transition-opacity ease-linear duration-300"
                x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
            </div>

            <!-- Mobile menu panel -->
            <div class="fixed inset-y-0 left-0 w-80 max-w-full bg-white dark:bg-gray-800 shadow-2xl overflow-y-auto z-50 transform transition-transform duration-300 ease-in-out"
                :class="{ 'translate-x-0': mobileMenuOpen, '-translate-x-full': !mobileMenuOpen }"
                @click.away="mobileMenuOpen = false" role="dialog" aria-modal="true" x-show="mobileMenuOpen">

                <!-- Navigation Links -->
                <nav class="flex-1 pt-2 pb-4 space-y-1 px-4">
                    @foreach ($navLinks as $link)
                        @php
                            $isActive = false;
                            if ($link['route'] !== '#') {
                                foreach ($link['routes'] as $route) {
                                    if (request()->routeIs($route)) {
                                        if ($link['is_home'] && $link['fragment']) {
                                            $isActive = request()->is(
                                                trim(route($link['route'], [], false), '/') . '#' . $link['fragment'],
                                            );
                                        } else {
                                            $isActive = true;
                                        }
                                        break;
                                    }
                                }
                            }

                            $routeName = $link['route'];
                            $routeParams = $routeName !== '#' ? ['locale' => app()->getLocale()] : [];
                            if (isset($link['route_params']) && is_array($link['route_params'])) {
                                $routeParams = array_merge($routeParams, $link['route_params']);
                            }

                            $activeClasses = $isActive
                                ? 'bg-blue-50 dark:bg-blue-900/30 border-blue-500 text-blue-700 dark:text-blue-300'
                                : 'border-transparent text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 hover:border-gray-300 dark:hover:border-gray-600';
                        @endphp

                        <a href="{{ $routeName !== '#' ? route($routeName, $routeParams) : '#' }}{{ $link['fragment'] ? '#' . $link['fragment'] : '' }}"
                            class="group flex items-center px-3 py-3 text-sm font-medium rounded-md transition-colors duration-150 {{ $activeClasses }}"
                            @if ($link['fragment']) x-data="{}" 
                           @click="$event.preventDefault();
                                  $dispatch('close-mobile-menu');
                                  const target = document.querySelector('#{{ $link['fragment'] }}');
                                  if (target) {
                                      target.scrollIntoView({ behavior: 'smooth' });
                                      window.history.pushState(null, '', '{{ route($link['route'], ['locale' => app()->getLocale()]) }}#{{ $link['fragment'] }}');
                                  }" @endif>
                            <span class="flex-1">{{ $link['text'] }}</span>
                        </a>
                    @endforeach
                </nav>

                <!-- Language Switcher -->
                <div class="border-t border-gray-200 dark:border-gray-700 px-4 py-4">
                    <h3
                        class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-3 px-3">
                        {{ __('Change Language') }}
                    </h3>
                    <div class="flex flex-wrap gap-2">
                        @foreach (config('app.available_locales') as $locale => $name)
                            @php
                                $isCurrent = app()->getLocale() === $locale;
                                $languageClasses = [
                                    'px-3 py-2 text-sm rounded-md font-medium transition-all duration-200 flex items-center',
                                    $isCurrent
                                        ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/50 dark:text-blue-100'
                                        : 'bg-gray-100 text-gray-700 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600',
                                ];
                            @endphp
                            <a href="{{ route('language.switch', $locale) }}"
                                class="{{ implode(' ', $languageClasses) }}" title="{{ $name }}">
                                {{ strtoupper($locale) }}
                                @if ($isCurrent)
                                    <svg class="ml-1.5 h-4 w-4 text-blue-500" fill="currentColor"
                                        viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                            clip-rule="evenodd" />
                                    </svg>
                                @endif
                            </a>
                        @endforeach
                    </div>
                </div>

                <!-- User Section -->
                <div class="border-t border-gray-200 dark:border-gray-700 pt-4 pb-3">
                    @auth
                        <div class="flex items-center px-4">
                            <div class="flex-shrink-0">
                                <div
                                    class="h-10 w-10 rounded-full bg-gradient-to-br from-blue-500 to-blue-700 flex items-center justify-center text-white font-semibold">
                                    {{ substr(Auth::user()->name, 0, 1) }}
                                </div>
                            </div>
                            <div class="ml-3 min-w-0 flex-1">
                                <div class="text-base font-medium text-gray-800 dark:text-white truncate">
                                    {{ Auth::user()->name }}</div>
                                <div class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">
                                    {{ Auth::user()->email }}</div>
                            </div>
                        </div>
                        <div class="mt-3 space-y-1">
                            @php
                                $isDashboardActive = request()->routeIs('dashboard*');
                                $dashboardMobileClasses = $isDashboardActive 
                                    ? 'bg-white text-blue-600' 
                                    : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700';
                            @endphp
                            <a href="{{ route('dashboard', ['locale' => app()->getLocale()]) }}"
                                class="group flex items-center px-4 py-2 text-sm font-medium rounded-md transition-colors duration-200 {{ $dashboardMobileClasses }}">
                                <svg class="mr-3 h-5 w-5 {{ $isDashboardActive ? 'text-blue-600' : 'text-gray-400 group-hover:text-gray-500 dark:group-hover:text-gray-300' }}"
                                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                                </svg>
                                {{ __('navigation.dashboard') }}
                            </a>
                            <form method="POST" action="{{ route('logout', ['locale' => app()->getLocale()]) }}">
                                @csrf
                                <button type="submit"
                                    class="group w-full flex items-center px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-md">
                                    <svg class="mr-3 h-5 w-5 text-gray-400 group-hover:text-red-500"
                                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                    </svg>
                                    {{ __('navigation.logout') }}
                                </button>
                            </form>
                        </div>
                    @else
                        <div class="space-y-2 px-4">
                            <a href="{{ route('login', app()->getLocale()) }}"
                                class="w-full flex items-center justify-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-150">
                                {{ __('navigation.login') }}
                            </a>
                            <a href="{{ route('register', app()->getLocale()) }}"
                                class="w-full flex items-center justify-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-150">
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
            padding-top: 5rem;
            /* Match the height of the header */
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
        -ms-overflow-style: none;
        /* IE and Edge */
        scrollbar-width: none;
        /* Firefox */
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
        from {
            opacity: 0;
            transform: translateY(-10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
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
                            const items = this.$refs.mobileMenu.querySelectorAll(
                                '.menu-item');
                            items.forEach((item, index) => {
                                setTimeout(() => {
                                    item.classList.add(
                                    'animate-in');
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
