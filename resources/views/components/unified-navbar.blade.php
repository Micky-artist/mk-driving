@props(['showUserStats' => false])

<nav class="w-full bg-blue-800 dark:bg-blue-900 shadow-lg z-50 px-2 overflow-hidden md:overflow-visible md:fixed transition-colors duration-200"
    x-data="navbarComponent()"> <div class="w-full max-w-full md:max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 md:ml-1 md:mr-1">
    <div class="flex items-center justify-between h-16">
        <!-- Mobile menu button (left on mobile, hidden on desktop) -->
        <div class="flex-shrink-0 flex items-center md:hidden">
            <button @click="mobileMenuOpen = !mobileMenuOpen"
                class="inline-flex items-center justify-center p-2 rounded-md text-white hover:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white transition-colors duration-200">
                <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                    <path x-show="!mobileMenuOpen" x-cloak x-transition:enter="transition-opacity duration-200"
                        x-transition:leave="transition-opacity duration-200" class="inline-flex" stroke-linecap="round"
                        stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    <path x-show="mobileMenuOpen" x-cloak x-transition:enter="transition-opacity duration-200"
                        x-transition:leave="transition-opacity duration-200" class="inline-flex" stroke-linecap="round"
                        stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- Logo and Language Selector (hidden on mobile, left on desktop) -->
        <div class="flex-shrink-0 hidden md:flex items-center space-x-4">
            <a href="{{ route('home', ['locale' => app()->getLocale()]) }}" class="flex items-center space-x-1">
                <img src="{{ asset('logo.png') }}" alt="MK Driving Academy Logo" class="h-8 w-8 rounded-lg shadow-md"
                    onerror="this.onerror=null; this.src='{{ asset('images/logo.png') }}'">
                <div class="ml-1">
                    <span class="text-xl font-bold text-white leading-none">
                        MK Driving Academy
                    </span>
                </div>
            </a>

            <!-- Language Selector (desktop dropdown) -->
            <div class="relative" x-data="{ languageOpen: false }">
                <button @click="languageOpen = !languageOpen"
                    class="flex items-center space-x-1 px-3 py-1 text-xs rounded-md font-medium transition-all duration-200 bg-white/20 text-white border border-white/30 hover:bg-white/30">
                    <i class="fas fa-globe mr-1"></i>
                    {{ strtoupper(app()->getLocale()) }}
                    <i class="fas fa-chevron-down ml-1 text-xs"></i>
                </button>

                <div x-show="languageOpen" x-cloak @click.away="languageOpen = false"
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 transform scale-95"
                    x-transition:enter-end="opacity-100 transform scale-100"
                    x-transition:leave="transition ease-in duration-75"
                    x-transition:leave-start="opacity-100 transform scale-100"
                    x-transition:leave-end="opacity-0 transform scale-95"
                    class="absolute right-0 mt-2 w-24 bg-white dark:bg-gray-800 rounded-md shadow-lg z-50">
                    <div class="py-1">
                        @foreach (config('app.available_locales') as $locale => $name)
                            @php
                                $isCurrent = app()->getLocale() === $locale;
                                $routeName = request()->route() ? request()->route()->getName() : 'home';
                                $baseRouteParams = request()->route() ? request()->route()->parameters() : [];
                                $routeParams = $baseRouteParams;
                                $routeParams['locale'] = $locale;
                                $url = route($routeName, $routeParams, false);
                                $url = '/' . ltrim($url, '/');
                            @endphp
                            <a href="{{ $url }}"
                                class="{{ $isCurrent ? 'bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }} block px-4 py-2 text-sm font-medium transition-colors duration-150"
                                title="{{ $name }}" hreflang="{{ $locale }}">
                                <span class="flex items-center">
                                    {{ strtoupper($locale) }}
                                    @if ($isCurrent)
                                        <i class="fas fa-check ml-auto text-blue-600"></i>
                                    @endif
                                </span>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Mobile Stats or Auth Buttons (right side on mobile) -->
        <div class="flex-1 flex justify-end md:hidden">
            @auth
                @if ($showUserStats)
                    <!-- Mobile Stats -->
                    <div class="flex items-center space-x-3 flex-wrap mr-2">
                        <div @click="navigateTo('{{ route('dashboard', ['locale' => app()->getLocale()]) }}')"
                            class="h-8 flex items-center space-x-1 bg-blue-500/20 rounded-lg px-2 cursor-pointer"
                            title="{{ __('navigation.average_score') }}">
                            <span class="text-blue-100 text-sm leading-none">🎯</span>
                            <span class="text-blue-100 text-sm font-bold leading-none"
                                x-text="Math.round(userStats.averageScore) + '%'"></span>
                        </div>
                        <div @click="navigateTo('{{ route('leaderboard', ['locale' => app()->getLocale()]) }}')"
                            class="h-8 flex items-center space-x-1 bg-orange-500/20 rounded-lg px-2 cursor-pointer"
                            title="{{ __('navigation.leaderboard') }}">
                            <span class="text-orange-100 text-sm leading-none">🥇</span>
                            <span class="text-orange-100 text-xs font-bold leading-none"
                                x-text="userStats.leaderboardPosition === 'N/A' ? 'N/A' : '#' + userStats.leaderboardPosition"></span>
                        </div>
                        <div @click="navigateTo('{{ route('dashboard.quizzes.index', ['locale' => app()->getLocale()]) }}')"
                            class="h-8 flex items-center space-x-1 bg-purple-500/20 rounded-lg px-2 cursor-pointer"
                            title="{{ __('navigation.streak') }}">
                            <span class="text-purple-100 text-sm leading-none">🔥</span>
                            <span class="text-purple-100 text-xs font-bold leading-none" x-text="userStats.streak"></span>
                        </div>
                        <div @click="navigateTo('{{ route('dashboard.progress', ['locale' => app()->getLocale()]) }}')"
                            class="h-8 flex items-center space-x-1 bg-yellow-500/20 rounded-lg px-2 cursor-pointer"
                            title="{{ __('navigation.xp_points') }}">
                            <span class="text-yellow-100 text-sm leading-none">💎</span>
                            <span class="text-yellow-100 text-xs font-bold leading-none" x-text="userStats.xp"></span>
                        </div>
                    </div>
                @endif
            @else
                <!-- Mobile Auth Buttons -->
                <div class="flex items-center space-x-3 mr-2">
                    <form action="{{ route('login', ['locale' => app()->getLocale()]) }}" method="GET"
                        class="flex-shrink-0">
                        <button type="submit"
                            class="h-8 px-3 text-sm font-medium text-white bg-white/20 hover:bg-white/30 backdrop-blur-sm border border-white/30 rounded-lg transition-all duration-200 shadow-sm hover:shadow-md flex items-center justify-center">
                            {{ __('navigation.login') }}
                        </button>
                    </form>
                    <form action="{{ route('register', ['locale' => app()->getLocale()]) }}" method="GET"
                        class="flex-shrink-0">
                        <button type="submit"
                            class="h-8 px-3 text-sm font-medium text-blue-100 bg-white/10 hover:bg-white/20 backdrop-blur-sm border border-white/20 rounded-lg transition-all duration-200 shadow-sm hover:shadow-md flex items-center justify-center">
                            {{ __('navigation.register') }}
                        </button>
                    </form>
                </div>
            @endauth
        </div>

        <!-- Desktop Navigation (centered) -->
        <div class="hidden md:flex md:flex-1 md:justify-center md:items-center md:space-x-4">
            @php
                $navLinks = [
                    [
                        'route' => 'dashboard',
                        'text' => __('navigation.quizzes'),
                        'routes' => ['dashboard', 'dashboard.progress', 'dashboard.quizzes.*', 'guest-quiz.*'],
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
                        'route' => 'news.index',
                        'route_params' => ['locale' => app()->getLocale()],
                        'text' => __('navigation.news'),
                        'routes' => ['news.*'],
                        'fragment' => null,
                        'is_home' => false,
                    ],
                    [
                        'route' => 'plans',
                        'text' => __('navigation.pricing_plans'),
                        'routes' => ['plans'],
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
                                if ($link['is_home'] && $link['fragment']) {
                                    $isActive = request()->is(
                                        trim(route($link['route'], ['locale' => app()->getLocale()], false), '/') .
                                            '#' .
                                            $link['fragment'],
                                    );
                                } else {
                                    $isActive = true;
                                }
                                break;
                            }
                        }
                    }
                    $classes = $isActive
                        ? 'text-white font-semibold bg-blue-600/30 rounded-lg'
                        : 'text-white hover:text-white/90 hover:bg-white/10 hover:rounded-lg border-transparent';
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
                    class="{{ $classes }} inline-flex items-center px-3 py-2 text-base font-semibold transition-colors duration-200"
                    @if ($link['fragment']) x-data="{}" 
                            @click="$event.preventDefault(); 
                                   document.querySelector('#{{ $link['fragment'] }}').scrollIntoView({ behavior: 'smooth' });
                                   window.history.pushState(null, '', '{{ route($link['route'], array_merge(['locale' => app()->getLocale()], $routeParams)) }}#{{ $link['fragment'] }}');" @endif>
                    {!! $link['text'] !!}
                </a>
            @endforeach

        </div>

        <!-- Right side (auth + stats) -->
        <!-- Right side (auth + stats) -->
        <div class="hidden md:flex md:items-center md:space-x-4">
            @auth
                @if ($showUserStats)
                    <!-- User Stats Dropdown -->
                    <div class="relative" x-data="{ statsOpen: false }">
                        <button @click="statsOpen = !statsOpen"
                            class="flex items-center space-x-2 bg-orange-500/20 hover:bg-orange-500/30 rounded-lg px-3 py-2 cursor-pointer transition-all duration-200">
                            <span class="text-orange-100 text-sm leading-none">🥇</span>
                            <span class="text-orange-100 text-sm font-bold leading-none"
                                x-text="userStats.leaderboardPosition === 'N/A' ? 'N/A' : '#' + userStats.leaderboardPosition"></span>
                            <i class="fas fa-chevron-down ml-1 text-xs text-orange-200"></i>
                        </button>

                        <div x-show="statsOpen" x-cloak @click.away="statsOpen = false"
                            x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 transform scale-95"
                            x-transition:enter-end="opacity-100 transform scale-100"
                            x-transition:leave="transition ease-in duration-75"
                            x-transition:leave-start="opacity-100 transform scale-100"
                            x-transition:leave-end="opacity-0 transform scale-95"
                            class="absolute right-0 mt-2 w-auto min-w-[280px] bg-white dark:bg-gray-800 rounded-lg shadow-xl z-50 overflow-hidden border border-gray-100 dark:border-gray-700">
                            <div class="py-1.5">
                                <!-- Stats Header -->
                                <div
                                    class="px-4 py-2.5 border-b border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/80">
                                    <h3 class="text-sm font-semibold text-gray-800 dark:text-gray-200">{{ __('navigation.your_stats') }}</h3>
                                </div>

                                <div class="divide-y divide-gray-100 dark:divide-gray-700">
                                    <!-- Average Score -->
                                    <div @click="navigateTo('{{ route('dashboard', ['locale' => app()->getLocale()]) }}')"
                                        class="group flex items-center justify-between px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-700/50 cursor-pointer transition-colors duration-150">
                                        <div class="flex items-center min-w-0">
                                            <div
                                                class="flex items-center justify-center w-8 h-8 rounded-full bg-blue-500/10 group-hover:bg-blue-500/20 transition-colors duration-200 mr-3 flex-shrink-0">
                                                <span class="text-blue-500 text-sm">🎯</span>
                                            </div>
                                            <div class="min-w-0">
                                                <p class="text-sm font-medium text-gray-900 dark:text-gray-100 truncate">
                                                    {{ __('navigation.average_score') }}</p>
                                            </div>
                                        </div>
                                        <span
                                            class="ml-2 text-sm font-semibold text-blue-600 dark:text-blue-400 whitespace-nowrap"
                                            x-text="Math.round(userStats.averageScore) + '%'"></span>
                                    </div>

                                    <!-- Leaderboard Position -->
                                    <div @click="navigateTo('{{ route('leaderboard', ['locale' => app()->getLocale()]) }}')"
                                        class="group flex items-center justify-between px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-700/50 cursor-pointer transition-colors duration-150">
                                        <div class="flex items-center min-w-0">
                                            <div
                                                class="flex items-center justify-center w-8 h-8 rounded-full bg-orange-500/10 group-hover:bg-orange-500/20 transition-colors duration-200 mr-3 flex-shrink-0">
                                                <span class="text-orange-500 text-sm">🥇</span>
                                            </div>
                                            <div class="min-w-0">
                                                <p class="text-sm font-medium text-gray-900 dark:text-gray-100 truncate">
                                                    {{ __('navigation.leaderboard') }}</p>
                                            </div>
                                        </div>
                                        <span
                                            class="ml-2 text-sm font-semibold text-orange-600 dark:text-orange-400 whitespace-nowrap"
                                            x-text="userStats.leaderboardPosition === 'N/A' ? 'N/A' : '#' + userStats.leaderboardPosition"></span>
                                    </div>

                                    <!-- Streak -->
                                    <div @click="navigateTo('{{ route('dashboard.quizzes.index', ['locale' => app()->getLocale()]) }}')"
                                        class="group flex items-center justify-between px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-700/50 cursor-pointer transition-colors duration-150">
                                        <div class="flex items-center min-w-0">
                                            <div
                                                class="flex items-center justify-center w-8 h-8 rounded-full bg-purple-500/10 group-hover:bg-purple-500/20 transition-colors duration-200 mr-3 flex-shrink-0">
                                                <span class="text-purple-500 text-sm">🔥</span>
                                            </div>
                                            <div class="min-w-0">
                                                <p class="text-sm font-medium text-gray-900 dark:text-gray-100 truncate">
                                                    {{ __('navigation.streak') }}</p>
                                            </div>
                                        </div>
                                        <span
                                            class="ml-2 text-sm font-semibold text-purple-600 dark:text-purple-400 whitespace-nowrap"
                                            x-text="userStats.streak"></span>
                                    </div>

                                    <!-- XP Points -->
                                    <div @click="navigateTo('{{ route('dashboard.progress', ['locale' => app()->getLocale()]) }}')"
                                        class="group flex items-center justify-between px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-700/50 cursor-pointer transition-colors duration-150">
                                        <div class="flex items-center min-w-0">
                                            <div
                                                class="flex items-center justify-center w-8 h-8 rounded-full bg-yellow-500/10 group-hover:bg-yellow-500/20 transition-colors duration-200 mr-3 flex-shrink-0">
                                                <span class="text-yellow-500 text-sm">💎</span>
                                            </div>
                                            <div class="min-w-0">
                                                <p class="text-sm font-medium text-gray-900 dark:text-gray-100 truncate">{{ __('navigation.xp_points') }}</p>
                                            </div>
                                        </div>
                                        <span
                                            class="ml-2 text-sm font-semibold text-yellow-600 dark:text-yellow-400 whitespace-nowrap"
                                            x-text="userStats.xp"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Leaderboard Icon for logged-in users -->
                <a href="{{ route('leaderboard', ['locale' => app()->getLocale()]) }}"
                    class="flex items-center justify-center w-8 h-8 rounded-full bg-yellow-500/20 hover:bg-yellow-500/30 text-white transition-all duration-200 group"
                    title="{{ __('navigation.leaderboard') }}">
                    <i class="fas fa-trophy text-sm"></i>
                </a>

                <div class="flex items-center space-x-3">
                    <!-- Profile Dropdown -->
                    <div class="relative" x-data="{ profileOpen: false }">
                        <button @click="profileOpen = !profileOpen"
                            class="flex items-center justify-center w-8 h-8 rounded-full bg-white/20 hover:bg-white/30 text-white transition-all duration-200 group"
                            title="{{ __('navigation.profile') }}">
                            <i class="fas fa-user text-sm"></i>
                        </button>

                        <div x-show="profileOpen" x-cloak @click.away="profileOpen = false"
                            x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 transform scale-95"
                            x-transition:enter-end="opacity-100 transform scale-100"
                            x-transition:leave="transition ease-in duration-75"
                            x-transition:leave-start="opacity-100 transform scale-100"
                            x-transition:leave-end="opacity-0 transform scale-95"
                            class="absolute right-0 mt-2 w-auto min-w-[280px] bg-white dark:bg-gray-800 rounded-lg shadow-xl z-50 overflow-hidden border border-gray-100 dark:border-gray-700">
                            <!-- Header -->
                            <div
                                class="px-4 py-2.5 border-b border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/80">
                                <h3 class="text-sm font-semibold text-gray-800 dark:text-gray-200">
                                    {{ __('navigation.account') }}</h3>
                            </div>

                            <div class="divide-y divide-gray-100 dark:divide-gray-700">
                                <!-- Profile Link -->
                                <a href="{{ route('profile.show', ['locale' => app()->getLocale()]) }}"
                                    class="group flex items-center px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-150">
                                    <div
                                        class="flex items-center justify-center w-8 h-8 rounded-full bg-blue-500/10 group-hover:bg-blue-500/20 transition-colors duration-200 mr-3 flex-shrink-0">
                                        <i class="fas fa-user text-blue-500 text-sm"></i>
                                    </div>
                                    <span
                                        class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ __('navigation.profile') }}</span>
                                </a>

                                <!-- Admin Link (only for admins) -->
                                @can('isAdmin')
                                    <a href="{{ route('admin.portal', ['locale' => app()->getLocale()]) }}"
                                        class="group flex items-center px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-150">
                                        <div
                                            class="flex items-center justify-center w-8 h-8 rounded-full bg-orange-500/10 group-hover:bg-orange-500/20 transition-colors duration-200 mr-3 flex-shrink-0">
                                            <i class="fas fa-cog text-orange-500 text-sm"></i>
                                        </div>
                                        <span
                                            class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ __('navigation.admin') }}</span>
                                    </a>
                                @endcan

                                <!-- Theme Switcher -->
                                <div class="group flex items-center px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-150">
                                    <div class="flex items-center justify-center w-8 h-8 rounded-full bg-purple-500/10 group-hover:bg-purple-500/20 transition-colors duration-200 mr-3 flex-shrink-0">
                                        <i class="fas fa-moon text-purple-500 text-sm dark:hidden"></i>
                                        <i class="fas fa-sun text-purple-500 text-sm hidden dark:block"></i>
                                    </div>
                                    <div class="flex items-center justify-between flex-1">
                                        <span class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                            {{ __('navigation.theme') }}
                                        </span>
                                        <button @click.stop="window.navbarComponent.toggleTheme()" 
                                                class="relative inline-flex h-6 w-11 items-center rounded-full bg-gray-200 dark:bg-purple-600 transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2">
                                            <span class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform duration-200 translate-x-1 dark:translate-x-6"></span>
                                        </button>
                                    </div>
                                </div>

                                <!-- Logout -->
                                <form method="POST" action="{{ route('logout', ['locale' => app()->getLocale()]) }}">
                                    @csrf
                                    <button type="submit"
                                        class="w-full text-left group flex items-center px-4 py-3 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors duration-150">
                                        <div
                                            class="flex items-center justify-center w-8 h-8 rounded-full bg-red-500/10 group-hover:bg-red-500/20 transition-colors duration-200 mr-3 flex-shrink-0">
                                            <i class="fas fa-sign-out-alt text-red-500 text-sm"></i>
                                        </div>
                                        <span
                                            class="text-sm font-medium text-red-600 dark:text-red-400">{{ __('navigation.logout') }}</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <!-- Leaderboard Icon for non-logged-in users -->
                <a href="{{ route('leaderboard', ['locale' => app()->getLocale()]) }}"
                    class="flex items-center justify-center w-8 h-8 rounded-full bg-yellow-500/20 hover:bg-yellow-500/30 text-white transition-all duration-200 group"
                    title="{{ __('navigation.leaderboard') }}">
                    <i class="fas fa-trophy text-sm"></i>
                </a>
                <div class="flex items-center space-x-3">
                    <a href="{{ route('login', app()->getLocale()) }}"
                        class="px-4 py-2 text-base font-medium text-white bg-blue-600/20 hover:bg-blue-500/30 backdrop-blur-sm border border-blue-400/30 hover:border-blue-300/50 rounded-lg transition-all duration-200 shadow-sm hover:shadow-md flex items-center justify-center">
                        <i class="fas fa-sign-in-alt mr-2"></i>{{ __('navigation.login') }}
                    </a>
                    <a href="{{ route('register', app()->getLocale()) }}"
                        class="px-4 py-2 text-base font-medium text-blue-100 bg-blue-500/20 hover:bg-blue-400/30 backdrop-blur-sm border border-blue-300/30 hover:border-blue-200/50 rounded-lg transition-all duration-200 shadow-sm hover:shadow-md flex items-center justify-center">
                        <i class="fas fa-user-plus mr-2"></i>{{ __('navigation.register') }}
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
                    <img src="{{ asset('logo.png') }}" alt="MK Driving Academy Logo"
                        class="h-12 w-12 rounded-lg shadow-md"
                        onerror="this.onerror=null; this.src='{{ asset('images/logo.png') }}'">
                    <div>
                        <span class="text-xl font-bold text-gray-900 dark:text-white">
                            MK DRIVING ACADEMY
                        </span>
                    </div>
                </a>
            </div>

            <!-- Main Navigation Links -->
            <nav class="px-4 py-6 space-y-2">
                <!-- Leaderboard Link (Mobile) -->
                <a href="{{ route('leaderboard', ['locale' => app()->getLocale()]) }}"
                    class="group flex items-center px-4 py-3 text-base font-semibold rounded-lg transition-colors duration-150 border-transparent text-gray-900 hover:bg-gray-100 hover:text-gray-900 dark:text-white dark:hover:bg-gray-700 dark:hover:text-gray-100 hover:border-gray-300 dark:hover:border-gray-600"
                    @click="mobileMenuOpen = false">
                    <i class="fas fa-trophy w-5 h-5 mr-3 text-yellow-400"></i>
                    <span class="flex-1">{{ __('navigation.leaderboard') }}</span>
                </a>

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
                            : 'border-transparent text-gray-900 hover:bg-gray-100 hover:text-gray-900 dark:text-white dark:hover:bg-gray-700 dark:hover:text-gray-100 hover:border-gray-300 dark:hover:border-gray-600';
                    @endphp
                    <a href="{{ $routeName !== '#' ? route($routeName, $routeParams) : '#' }}{{ $link['fragment'] ? '#' . $link['fragment'] : '' }}"
                        class="group flex items-center px-4 py-3 text-base font-semibold rounded-lg transition-colors duration-150 {{ $activeClasses }}"
                        @if ($link['fragment']) x-data="{}" 
                           @click="$event.preventDefault(); $dispatch('close-mobile-menu');
                                  const target = document.querySelector('#{{ $link['fragment'] }}');
                                  if (target) {
                                      target.scrollIntoView({ behavior: 'smooth' });
                                      window.history.pushState(null, '', '{{ route($link['route'], ['locale' => app()->getLocale()]) }}#{{ $link['fragment'] }}');
                                  }" @endif
                        @click="mobileMenuOpen = false">
                        <span class="flex-1">{!! $link['text'] !!}</span>
                        @if ($isActive)
                            <svg class="h-4 w-4 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                    clip-rule="evenodd" />
                            </svg>
                        @endif
                    </a>
                @endforeach

                <!-- Admin Navigation (mobile only) -->
                @auth
                    @can('isAdmin')
                        @php
                            $isAdminMobileActive = request()->routeIs('admin.*');
                            $adminMobileClasses = $isAdminMobileActive
                                ? 'bg-orange-50 dark:bg-orange-900/30 border-orange-500 text-orange-700 dark:text-orange-300'
                                : 'border-transparent text-gray-900 hover:bg-gray-100 hover:text-gray-900 dark:text-white dark:hover:bg-gray-700 dark:hover:text-gray-100 hover:border-gray-300 dark:hover:border-gray-600';
                        @endphp
                        <a href="{{ route('admin.portal', ['locale' => app()->getLocale()]) }}"
                            class="group flex items-center px-4 py-3 text-base font-semibold rounded-lg transition-colors duration-150 {{ $adminMobileClasses }}"
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
                                    : 'text-gray-700 hover:bg-gray-200 hover:text-gray-900 dark:text-white dark:bg-gray-700 dark:hover:bg-gray-600 dark:hover:text-gray-100',
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
                            @if ($isCurrent)
                                <svg class="ml-1.5 h-4 w-4 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
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
                            <p class="text-xs text-gray-500 dark:text-gray-300 truncate">
                                {{ Auth::user()->email }}
                            </p>
                        </div>
                    </a>

                    <div class="space-y-2">
                        <!-- Theme Switcher -->
                        <button @click="window.navbarComponent.toggleTheme(); mobileMenuOpen = false"
                                class="w-full flex items-center justify-between px-4 py-3 text-sm font-medium text-gray-900 dark:text-gray-100 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-lg transition-colors duration-150">
                            <div class="flex items-center">
                                <div class="flex items-center justify-center w-8 h-8 rounded-full bg-purple-500/10 hover:bg-purple-500/20 transition-colors duration-200 mr-3 flex-shrink-0">
                                    <i class="fas fa-moon text-purple-500 text-sm dark:hidden"></i>
                                    <i class="fas fa-sun text-purple-500 text-sm hidden dark:block"></i>
                                </div>
                                <span>{{ __('navigation.theme') }}</span>
                            </div>
                            <div class="relative inline-flex h-6 w-11 items-center rounded-full bg-gray-200 dark:bg-purple-600 transition-colors duration-200">
                                <span class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform duration-200 translate-x-1 dark:translate-x-6"></span>
                            </div>
                        </button>
                        
                        <!-- Logout -->
                        <form method="POST" action="{{ route('logout', ['locale' => app()->getLocale()]) }}"
                            id="mobile-logout-form" x-data="{ submitting: false }"
                            @submit.prevent="
                            submitting = true;
                            const form = document.getElementById('mobile-logout-form');
                            form.submit();
                        ">
                            @csrf
                            <button type="submit" :disabled="submitting"
                                class="w-full flex items-center justify-center px-4 py-2 text-sm font-medium text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors duration-150 disabled:opacity-75 disabled:cursor-not-allowed">
                                <svg x-show="!submitting" class="mr-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg"
                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                </svg>
                                <svg x-show="submitting" class="animate-spin -ml-1 mr-2 h-4 w-4 text-red-500"
                                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10"
                                        stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                    </path>
                                </svg>
                                <span
                                    x-text="submitting ? '{{ __('Signing out...') }}' : '{{ __('navigation.logout') }}'"></span>
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
                            class="w-full flex items-center justify-center px-4 py-2 border border-white/30 rounded-lg shadow-sm text-sm font-medium text-white bg-white/10 hover:bg-white/20 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-150">
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
                // Fetching user stats...
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


                    if (response.ok) {
                        const stats = await response.json();
                        // Stats received and processed
                        this.userStats = {
                            averageScore: stats.averageScore,
                            leaderboardPosition: stats.leaderboardPosition,
                            streak: stats.streak,
                            xp: stats.xp
                        };
                        // UserStats updated
                    } else {
                        console.error('Failed to fetch stats:', response.statusText);
                    }
                } catch (error) {
                    console.error('Error fetching user stats:', error);
                }
            },
            toggleTheme() {
                // toggleTheme called
                // Toggle dark mode class on html element
                document.documentElement.classList.toggle('dark');
                
                // Save preference to localStorage
                const isDark = document.documentElement.classList.contains('dark');
                localStorage.setItem('theme', isDark ? 'dark' : 'light');
                
                // Theme toggled to:
                
                // Update meta theme-color for mobile browsers
                const metaThemeColor = document.querySelector('meta[name="theme-color"]');
                if (metaThemeColor) {
                    metaThemeColor.content = isDark ? '#1e3a8a' : '#1e40af'; // blue-800 : blue-600
                }
            },
            navigateTo(url) {
                window.location.href = url;
            },
            init() {
                // Initialize theme from localStorage
                const savedTheme = localStorage.getItem('theme');
                const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
                
                if (savedTheme === 'dark' || (!savedTheme && prefersDark)) {
                    document.documentElement.classList.add('dark');
                } else {
                    document.documentElement.classList.remove('dark');
                }
                
                // Set initial meta theme-color
                const isDark = document.documentElement.classList.contains('dark');
                const metaThemeColor = document.querySelector('meta[name="theme-color"]');
                if (metaThemeColor) {
                    metaThemeColor.content = isDark ? '#1e3a8a' : '#1e40af';
                }

                // Delay fetchUserStats to ensure Alpine is fully initialized
                this.$nextTick(() => {
                    setTimeout(() => {
                        this.fetchUserStats();
                    }, 100);
                });

                // Listen for stats updates from quiz completion
                this.$el.addEventListener('statsUpdated', (event) => {
                    this.userStats = {
                        ...this.userStats,
                        ...event.detail
                    };
                });
                
                // Expose component to window for global access
                window.navbarComponent = this;
            }
        }
    }
    
    // Also expose to window globally for immediate access
    document.addEventListener('alpine:initialized', () => {
        // Component will be available after Alpine initializes
    });
</script>
