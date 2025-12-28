@props(['showUserStats' => false])

<nav class="w-full bg-[#2563eb] dark:bg-blue-900 shadow-lg z-50 overflow-hidden md:overflow-visible md:fixed transition-colors duration-200"
    x-data="navbarComponent()"
    <div class="w-full max-w-full md:max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 md:ml-1 md:mr-1">
        <div class="flex items-center justify-between h-16">
            <!-- Mobile menu button (left on mobile, hidden on desktop) -->
            <div class="flex-shrink-0 flex items-center md:hidden">
                <button @click="mobileMenuOpen = !mobileMenuOpen"
                    class="inline-flex items-center justify-center p-2 rounded-md text-white hover:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white transition-colors duration-200">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path x-show="!mobileMenuOpen" x-cloak x-transition:enter="transition-opacity duration-200"
                            x-transition:leave="transition-opacity duration-200" class="inline-flex"
                            stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                        <path x-show="mobileMenuOpen" x-cloak x-transition:enter="transition-opacity duration-200"
                            x-transition:leave="transition-opacity duration-200" class="inline-flex"
                            stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Logo (hidden on mobile, left on desktop) -->
            <div class="flex-shrink-0 hidden md:flex items-center">
                <a href="{{ route('home', ['locale' => app()->getLocale()]) }}" class="flex items-center space-x-1">
                    <img src="{{ asset('logo.png') }}" alt="MK Driving School Logo" class="h-8 w-8 rounded-lg shadow-md"
                        onerror="this.onerror=null; this.src='{{ asset('images/logo.png') }}'">
                    <div class="ml-1">
                        <span class="text-xl font-bold text-white leading-none">
                            MK DRIVING
                        </span>
                    </div>
                </a>
            </div>

            <!-- Mobile Stats or Auth Buttons (right side on mobile) -->
            <div class="flex-1 flex justify-end md:hidden">
                @auth
                    @if ($showUserStats)
                        <!-- Mobile Stats -->
                        <div class="flex items-center space-x-3 flex-wrap mr-2">
                            <div @click="navigateTo('{{ route('dashboard.progress', ['locale' => app()->getLocale()]) }}')" class="h-8 flex items-center space-x-1 bg-blue-500/20 rounded-lg px-2 cursor-pointer">
                                <span class="text-blue-100 text-sm leading-none">🎯</span>
                                <span class="text-blue-100 text-sm font-bold leading-none" x-text="Math.round(userStats.averageScore) + '%'"></span>
                            </div>
                            <div @click="navigateTo('{{ route('forum.index', ['locale' => app()->getLocale(), 'see' => 'leaderboard']) }}')" class="h-8 flex items-center space-x-1 bg-orange-500/20 rounded-lg px-2 cursor-pointer">
                                <span class="text-orange-100 text-sm leading-none">🥇</span>
                                <span class="text-orange-100 text-xs font-bold leading-none" x-text="userStats.leaderboardPosition === 'N/A' ? 'N/A' : '#' + userStats.leaderboardPosition"></span>
                            </div>
                            <div @click="navigateTo('{{ route('dashboard', ['locale' => app()->getLocale()]) }}')" class="h-8 flex items-center space-x-1 bg-purple-500/20 rounded-lg px-2 cursor-pointer">
                                <span class="text-purple-100 text-sm leading-none">🔥</span>
                                <span class="text-purple-100 text-xs font-bold leading-none" x-text="userStats.streak"></span>
                            </div>
                            <div @click="navigateTo('{{ route('forum.index', ['locale' => app()->getLocale(), 'see' => 'leaderboard']) }}')" class="h-8 flex items-center space-x-1 bg-yellow-500/20 rounded-lg px-2 cursor-pointer">
                                <span class="text-yellow-100 text-sm leading-none">💎</span>
                                <span class="text-yellow-100 text-xs font-bold leading-none" x-text="userStats.xp"></span>
                            </div>
                        </div>
                    @endif
                @else
                    <!-- Mobile Auth Buttons -->
                    <div class="flex items-center space-x-3 mr-2">
                        <form action="{{ route('login', ['locale' => app()->getLocale()]) }}" method="GET" class="flex-shrink-0">
                            <button type="submit"
                                class="h-8 px-3 text-sm font-medium text-white bg-white/20 hover:bg-white/30 backdrop-blur-sm border border-white/30 rounded-lg transition-all duration-200 shadow-sm hover:shadow-md flex items-center justify-center">
                                {{ __('navigation.login') }}
                            </button>
                        </form>
                        <form action="{{ route('register', ['locale' => app()->getLocale()]) }}" method="GET" class="flex-shrink-0">
                            <button type="submit"
                                class="h-8 px-3 text-sm font-medium text-blue-100 bg-white/10 hover:bg-white/20 backdrop-blur-sm border border-white/20 rounded-lg transition-all duration-200 shadow-sm hover:shadow-md flex items-center justify-center">
                                {{ __('navigation.register') }}
                            </button>
                        </form>
                    </div>
                @endauth
            </div>

            <!-- Desktop Navigation (centered) -->
            <div class="hidden md:flex md:flex-1 md:justify-center md:items-center md:space-x-8">
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
                            'route' => 'dashboard',
                            'text' => __('navigation.dashboard'),
                            'routes' => ['dashboard', 'dashboard.progress'],
                            'fragment' => null,
                            'is_home' => false,
                        ],
                        [
                            'route' => 'dashboard.quizzes.index',
                            'text' => __('navigation.quizzes'),
                            'routes' => ['dashboard.quizzes.*', 'guest-quiz.*'],
                            'fragment' => null,
                            'is_home' => false,
                        ],
                        [
                            'route' => 'forum.index',
                            'text' => __('forum.page_title'),
                            'routes' => ['forum.*'],
                            'fragment' => null,
                            'is_home' => false,
            ],
                        [
                            'route' => 'plans',
                            'text' => __('navigation.pricing_plans'),
                            'routes' => ['plans'],
                            'fragment' => null,
                            'is_home' => false,
                        ]
                    ];
                @endphp

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
                        $classes = $isActive
                            ? 'text-white font-semibold border-white'
                            : 'text-blue-200 hover:text-white hover:border-blue-200 border-transparent';
                    @endphp
                    @php
                        $routeName = $link['route'];
                        $routeParams = [];
                        if ($routeName !== '#') {
                            $routeParams['locale'] = app()->getLocale();
                        }
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

                <!-- Admin Navigation -->
                @auth
                    @can('isAdmin')
                        @php
                            $isAdminActive = request()->routeIs('admin.*');
                            $adminClasses = $isAdminActive
                                ? 'text-orange-300 font-semibold border-orange-300'
                                : 'text-orange-200 hover:text-orange-300 hover:border-orange-200 border-transparent';
                        @endphp
                        <a href="{{ route('admin.portal', ['locale' => app()->getLocale()]) }}"
                            class="{{ $adminClasses }} inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium transition-colors duration-200">
                            {{ __('navigation.admin') }}
                        </a>
                    @endcan
                @endauth
            </div>

            <!-- Right side (auth + stats) -->
            <div class="hidden md:flex md:items-center md:space-x-4">
                @auth
                    @if ($showUserStats)
                        <!-- User Stats -->
                        
                        <div class="flex items-center space-x-3 flex-wrap mr-2">
                            <div @click="navigateTo('{{ route('dashboard.progress', ['locale' => app()->getLocale()]) }}')" class="h-6 py-2 flex items-center space-x-1 bg-blue-500/20 rounded-lg px-2 cursor-pointer">
                                <span class="text-blue-100 text-sm leading-none">🎯</span>
                                <span class="text-blue-100 text-sm font-bold leading-none" x-text="Math.round(userStats.averageScore) + '%'"></span>
                            </div>
                            <div @click="navigateTo('{{ route('forum.index', ['locale' => app()->getLocale(), 'see' => 'leaderboard']) }}')" class="h-6 py-2 flex items-center space-x-1 bg-orange-500/20 rounded-lg px-2 cursor-pointer">
                                <span class="text-orange-100 text-sm leading-none">🥇</span>
                                <span class="text-orange-100 text-xs font-bold leading-none" x-text="userStats.leaderboardPosition === 'N/A' ? 'N/A' : '#' + userStats.leaderboardPosition"></span>
                            </div>
                            <div @click="navigateTo('{{ route('dashboard', ['locale' => app()->getLocale()]) }}')" class="h-6 py-2 flex items-center space-x-1 bg-purple-500/20 rounded-lg px-2 cursor-pointer">
                                <span class="text-purple-100 text-sm leading-none">🔥</span>
                                <span class="text-purple-100 text-xs font-bold leading-none" x-text="userStats.streak"></span>
                            </div>
                            <div @click="navigateTo('{{ route('forum.index', ['locale' => app()->getLocale(), 'see' => 'leaderboard']) }}')" class="h-6 py-2 flex items-center space-x-1 bg-yellow-500/20 rounded-lg px-2 cursor-pointer">
                                <span class="text-yellow-100 text-sm leading-none">💎</span>
                                <span class="text-yellow-100 text-xs font-bold leading-none" x-text="userStats.xp"></span>
                            </div>
                        </div>
                    @endif

                    <div class="flex items-center space-x-3">
                        <!-- Language Selector -->
                        <div class="flex items-center space-x-1">
                            @foreach (config('app.available_locales') as $locale => $name)
                                @php
                                    $isCurrent = app()->getLocale() === $locale;
                                    $languageClasses = [
                                        'px-2 py-0.5 text-xs rounded-md font-medium transition-all duration-200',
                                        $isCurrent
                                            ? 'bg-white/20 text-white border border-white/30'
                                            : 'text-blue-200 hover:text-white hover:bg-white/10 border border-transparent',
                                    ];
                                    
                                    // Build URL to stay on current page with new locale
                                    $routeName = request()->route() ? request()->route()->getName() : 'home';
                                    $baseRouteParams = request()->route() ? request()->route()->parameters() : [];
                                    $routeParams = $baseRouteParams;
                                    $routeParams['locale'] = $locale;
                                    $url = route($routeName, $routeParams, false);
                                    $url = '/' . ltrim($url, '/');
                                @endphp
                                <a href="{{ $url }}" class="{{ implode(' ', $languageClasses) }}"
                                   title="{{ $name }}" hreflang="{{ $locale }}">
                                    {{ strtoupper($locale) }}
                                </a>
                            @endforeach
                        </div>
                        
                        <!-- Profile Icon Link -->
                        <a href="{{ route('profile.show', ['locale' => app()->getLocale()]) }}" 
                           class="flex items-center justify-center w-8 h-8 rounded-full bg-white/20 hover:bg-white/30 text-white transition-all duration-200 group"
                           title="{{ __('navigation.profile') }}">
                            <i class="fas fa-user text-sm"></i>
                        </a>
                    </div>
                @else
                    <div class="flex items-center space-x-3">
                        <!-- Language Selector -->
                        <div class="flex items-center space-x-1">
                            @foreach (config('app.available_locales') as $locale => $name)
                                @php
                                    $isCurrent = app()->getLocale() === $locale;
                                    $languageClasses = [
                                        'px-2 py-0.5 text-xs rounded-md font-medium transition-all duration-200',
                                        $isCurrent
                                            ? 'bg-white/20 text-white border border-white/30'
                                            : 'text-blue-200 hover:text-white hover:bg-white/10 border border-transparent',
                                    ];
                                    
                                    // Build URL to stay on current page with new locale
                                    $routeName = request()->route() ? request()->route()->getName() : 'home';
                                    $baseRouteParams = request()->route() ? request()->route()->parameters() : [];
                                    $routeParams = $baseRouteParams;
                                    $routeParams['locale'] = $locale;
                                    $url = route($routeName, $routeParams, false);
                                    $url = '/' . ltrim($url, '/');
                                @endphp
                                <a href="{{ $url }}" class="{{ implode(' ', $languageClasses) }}"
                                   title="{{ $name }}" hreflang="{{ $locale }}">
                                    {{ strtoupper($locale) }}
                                </a>
                            @endforeach
                        </div>
                        
                        <a href="{{ route('login', app()->getLocale()) }}"
                            class="text-blue-200 hover:text-white hover:border-blue-200 border-transparent border-b-2 px-1 pt-1 text-sm font-medium transition-colors duration-200">
                            {{ __('navigation.login') }}
                        </a>
                        <a href="{{ route('register', app()->getLocale()) }}"
                            class="text-blue-200 hover:text-white hover:border-blue-200 border-transparent border-b-2 px-1 pt-1 text-sm font-medium transition-colors duration-200">
                            {{ __('navigation.register') }}
                        </a>
                    </div>
                @endauth
            </div>

        </div>
    </div>

    <!-- Mobile menu overlay with backdrop blur -->
    <div x-show="mobileMenuOpen" @click.self="mobileMenuOpen = false"
        x-transition:enter="transition-opacity ease-linear duration-200" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="transition-opacity ease-linear duration-150"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
        class="md:hidden fixed inset-0 bg-black/70 backdrop-blur-sm z-50" x-cloak>

        <!-- Mobile menu panel -->
        <div class="fixed inset-y-0 left-0 w-80 max-w-full bg-white dark:bg-gray-800 shadow-2xl overflow-y-auto z-[60] transform transition-transform duration-300 ease-in-out"
            :class="{ 'translate-x-0': mobileMenuOpen, '-translate-x-full': !mobileMenuOpen }"
            @click.away="mobileMenuOpen = false" role="dialog" aria-modal="true" x-show="mobileMenuOpen">

            <!-- Logo Section -->
            <div class="px-6 py-6 border-b border-gray-200 dark:border-gray-700">
                <a href="{{ route('home', ['locale' => app()->getLocale()]) }}" class="flex items-center space-x-3">
                    <img src="{{ asset('logo.png') }}" alt="MK Driving School Logo"
                        class="h-12 w-12 rounded-lg shadow-md"
                        onerror="this.onerror=null; this.src='{{ asset('images/logo.png') }}'">
                    <div>
                        <span class="text-xl font-bold text-gray-900 dark:text-white">
                            MK DRIVING
                        </span>
                    </div>
                </a>
            </div>

            <!-- Main Navigation Links -->
            <nav class="px-4 py-6 space-y-2">
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
                        class="group flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors duration-150 {{ $activeClasses }}"
                        @if ($link['fragment']) x-data="{}" 
                           @click="$event.preventDefault(); $dispatch('close-mobile-menu');
                                  const target = document.querySelector('#{{ $link['fragment'] }}');
                                  if (target) {
                                      target.scrollIntoView({ behavior: 'smooth' });
                                      window.history.pushState(null, '', '{{ route($link['route'], ['locale' => app()->getLocale()]) }}#{{ $link['fragment'] }}');
                                  }" @endif
                        @click="mobileMenuOpen = false">
                        <span class="flex-1">{{ $link['text'] }}</span>
                        @if ($isActive)
                            <svg class="h-4 w-4 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                    clip-rule="evenodd" />
                            </svg>
                        @endif
                    </a>
                @endforeach

                <!-- Admin Navigation -->
                @auth
                    @can('isAdmin')
                        @php
                            $isAdminMobileActive = request()->routeIs('admin.*');
                            $adminMobileClasses = $isAdminMobileActive
                                ? 'bg-orange-50 dark:bg-orange-900/30 border-orange-500 text-orange-700 dark:text-orange-300'
                                : 'border-transparent text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 hover:border-gray-300 dark:hover:border-gray-600';
                        @endphp
                        <a href="{{ route('admin.portal', ['locale' => app()->getLocale()]) }}"
                            class="group flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors duration-150 {{ $adminMobileClasses }}"
                            @click="mobileMenuOpen = false">
                            <span class="flex-1">{{ __('navigation.admin') }}</span>
                            @if ($isAdminMobileActive)
                                <svg class="h-4 w-4 text-orange-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                        clip-rule="evenodd" />
                                </svg>
                            @endif
                        </a>
                    @endcan
                @endauth
            </nav>

            <!-- Language Switcher -->
            <div class="border-t border-gray-200 dark:border-gray-700 px-6 py-4">
                <div class="space-y-2">
                    @foreach (config('app.available_locales') as $locale => $name)
                        @php
                            $isCurrent = app()->getLocale() === $locale;
                            $languageClasses = [
                                'px-3 py-2 text-sm rounded-md font-medium transition-all duration-200 flex items-center w-full text-left',
                                $isCurrent
                                    ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/50 dark:text-blue-100'
                                    : 'bg-gray-100 text-gray-700 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600',
                            ];
                            
                            // Build URL to stay on current page with new locale
                            $routeName = request()->route() ? request()->route()->getName() : 'home';
                            $baseRouteParams = request()->route() ? request()->route()->parameters() : [];
                            $routeParams = $baseRouteParams;
                            $routeParams['locale'] = $locale;
                            $url = route($routeName, $routeParams, false);
                            $url = '/' . ltrim($url, '/');
                        @endphp
                        <a href="{{ $url }}" class="{{ implode(' ', $languageClasses) }}" title="{{ $name }}" hreflang="{{ $locale }}">
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
            <div class="border-t border-gray-200 dark:border-gray-700 px-6 py-4">
                @auth
                    <!-- User info (clickable) -->
                    <a href="{{ route('profile.show', ['locale' => app()->getLocale()]) }}" 
                       @click="mobileMenuOpen = false"
                       class="flex items-center mb-4 p-3 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors duration-150">
                        <div
                            class="h-10 w-10 rounded-full bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white font-semibold text-sm flex-shrink-0">
                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                        </div>
                        <div class="ml-3 overflow-hidden">
                            <p class="text-sm font-medium text-gray-900 dark:text-white truncate">
                                {{ Auth::user()->name }}
                            </p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 truncate">
                                {{ Auth::user()->email }}
                            </p>
                        </div>
                    </a>

                    <div class="space-y-2">
                        <!-- Logout -->
                        <form method="POST" action="{{ route('logout', ['locale' => app()->getLocale()]) }}" id="mobile-logout-form" x-data="{ submitting: false }" @submit.prevent="
                            submitting = true;
                            const form = document.getElementById('mobile-logout-form');
                            form.submit();
                        ">
                            @csrf
                            <button type="submit"
                                :disabled="submitting"
                                class="w-full flex items-center justify-center px-4 py-2 text-sm font-medium text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors duration-150 disabled:opacity-75 disabled:cursor-not-allowed">
                                <svg x-show="!submitting" class="mr-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                </svg>
                                <svg x-show="submitting" class="animate-spin -ml-1 mr-2 h-4 w-4 text-red-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span x-text="submitting ? '{{ __('Signing out...') }}' : '{{ __('navigation.logout') }}'"></span>
                            </button>
                        </form>
                    </div>
                @else
                    <!-- Guest user section -->
                    <div class="space-y-3">
                        <a href="{{ route('login', ['locale' => app()->getLocale()]) }}"
                            class="w-full flex items-center justify-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-150">
                            {{ __('navigation.login') }}
                        </a>
                        <a href="{{ route('register', ['locale' => app()->getLocale()]) }}"
                            class="w-full flex items-center justify-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-150">
                            {{ __('navigation.register') }}
                        </a>
                    </div>
                @endauth
            </div>
        </div>
    </div>
</nav>

<script>
function navbarComponent() {
    return {
        open: false,
        mobileMenuOpen: false,
        userStats: {
            averageScore: {{ Auth::user()->average_score ?? 97 }},
            leaderboardPosition: {{ Auth::user()->leaderboard_position ?? 1 }},
            streak: {{ Auth::user()->streak_days ?? 0 }},
            xp: {{ Auth::user()->points ?? 0 }}
        },
        async fetchUserStats() {
            console.log('Fetching user stats...');
            try {
                const response = await fetch('/api/user/stats', {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    credentials: 'same-origin'
                });
                
                console.log('Response status:', response.status);
                
                if (response.ok) {
                    const stats = await response.json();
                    console.log('Received stats:', stats);
                    this.userStats = {
                        averageScore: stats.averageScore,
                        leaderboardPosition: stats.leaderboardPosition,
                        streak: stats.streak,
                        xp: stats.xp
                    };
                    console.log('Updated userStats:', this.userStats);
                } else {
                    console.error('Failed to fetch stats:', response.statusText);
                }
            } catch (error) {
                console.error('Error fetching user stats:', error);
            }
        },
        navigateTo(url) {
            window.location.href = url;
        },
        init() {
            // Delay fetchUserStats to ensure Alpine is fully initialized
            this.$nextTick(() => {
                setTimeout(() => {
                    this.fetchUserStats();
                }, 100);
            });
            
            // Listen for stats updates from quiz completion
            this.$el.addEventListener('statsUpdated', (event) => {
                this.userStats = { ...this.userStats, ...event.detail };
            });
        }
    }
}
</script>
