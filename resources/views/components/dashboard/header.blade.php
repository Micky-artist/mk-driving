@props(['title' => null])

<div class="flex items-center justify-between w-full">
    <!-- Mobile menu button -->
    <button 
        @click="$store.sidebar.openSidebar()"
        class="lg:hidden -ml-2 p-2 rounded-md text-gray-500 dark:text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-blue-500"
        aria-label="Open sidebar"
    >
        <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
        </svg>
    </button>
    
    <!-- User Stats Indicators (Mobile) -->
    @auth
    <div class="flex-1 flex justify-center items-center space-x-4 lg:hidden">
        <!-- Leaderboard Position -->
        <div class="text-center">
            <div class="text-orange-600 dark:text-orange-400 text-xs font-semibold">
                {{ __('dashboard.stats.rank') }}
            </div>
            <div class="text-orange-700 dark:text-orange-300 text-sm font-bold">
                #{{ Auth::user()->leaderboard_position ?? 1 }}
            </div>
        </div>
        
        <!-- Streak -->
        <div class="text-center">
            <div class="text-blue-600 dark:text-blue-400 text-xs font-semibold">
                {{ __('dashboard.stats.streak') }}
            </div>
            <div class="text-blue-700 dark:text-blue-300 text-sm font-bold">
                {{ Auth::user()->streak ?? 0 }}
            </div>
        </div>
        
        <!-- Points -->
        <div class="text-center">
            <div class="text-green-600 dark:text-green-400 text-xs font-semibold">
                {{ __('dashboard.stats.points') }}
            </div>
            <div class="text-green-700 dark:text-green-300 text-sm font-bold">
                {{ Auth::user()->points ?? 0 }}
            </div>
        </div>
    </div>
    @endauth
    
    <!-- Desktop title -->
    <div class="hidden lg:block">
        <h1 class="text-xl font-semibold text-gray-900 dark:text-white">
            <a href="{{ route('dashboard', ['locale' => app()->getLocale()]) }}" class="hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                {{ $title ?? __('navigation.dashboard') }}
            </a>
        </h1>
    </div>
    
    <!-- Right side actions -->
    <div class="flex items-center space-x-2">
        <!-- Language Switcher (Desktop only) -->
        <div class="hidden md:block">
            <x-language-switcher :current-locale="app()->getLocale()" />
        </div>

    @auth
        <!-- Profile dropdown for authenticated users -->
        <div x-data="{ open: false }" class="relative">
            <button @click="open = !open" type="button"
                class="flex items-center space-x-2 p-1 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors">
                <div class="relative">
                    <div
                        class="h-9 w-9 rounded-full bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white font-semibold text-sm shadow-sm">
                        {{ strtoupper(substr(Auth::user()->first_name, 0, 1)) }}{{ strtoupper(substr(Auth::user()->last_name, 0, 1)) }}
                    </div>
                </div>
                <svg class="hidden sm:block h-4 w-4 text-gray-400 dark:text-gray-500" :class="{ 'rotate-180': open }"
                    xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd"
                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                        clip-rule="evenodd" />
                </svg>
            </button>

            <!-- Dropdown menu -->
            <div x-show="open" @click.away="open = false" x-transition:enter="transition ease-out duration-100"
                x-transition:enter-start="transform opacity-0 scale-95"
                x-transition:enter-end="transform opacity-100 scale-100" x-transition:leave="transition ease-in duration-75"
                x-transition:leave-start="transform opacity-100 scale-100"
                x-transition:leave-end="transform opacity-0 scale-95"
                class="absolute right-0 mt-2 w-56 origin-top-right rounded-lg bg-white dark:bg-gray-800 shadow-lg ring-1 ring-black ring-opacity-5 dark:ring-gray-700 focus:outline-none z-50"
                x-cloak>
                <div class="p-2">
                    <!-- User info in dropdown -->
                    <div class="px-3 py-2 border-b border-gray-100 dark:border-gray-700 mb-1">
                        <p class="text-sm font-medium text-gray-900 dark:text-white truncate">
                            {{ Auth::user()->name }}
                        </p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 truncate">
                            {{ Auth::user()->email }}
                        </p>
                    </div>

                    @php
                        $currentRoute = request()->route();
                        $locale =
                            is_object($currentRoute) && method_exists($currentRoute, 'parameter')
                                ? $currentRoute->parameter('locale')
                                : app()->getLocale();
                    @endphp

                    <a href="{{ route('profile.show', ['locale' => $locale]) }}"
                        class="flex items-center px-3 py-2 text-sm text-gray-700 dark:text-gray-300 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
                        @click="open = false">
                        <svg class="w-4 h-4 mr-3 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        {{ __('dashboard.navigation.profile_settings') }}
                    </a>

                    <!-- Admin Badge -->
                    @if (Auth::user()->isAdmin())
                        <a href="{{ route('admin.portal', ['locale' => $locale]) }}" 
                           class="flex items-center px-3 py-2 text-sm text-orange-700 dark:text-orange-400 rounded-md hover:bg-orange-50 dark:hover:bg-orange-900/20 transition-colors"
                           @click="open = false">
                            <svg class="w-4 h-4 mr-3 text-orange-600 dark:text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            {{ __('navigation.admin') }}
                        </a>
                    @endif

                    <div class="border-t border-gray-100 dark:border-gray-700 my-1"></div>

                    <form method="POST" action="{{ route('logout', ['locale' => $locale]) }}">
                        @csrf
                        <button type="submit"
                            class="flex items-center w-full px-3 py-2 text-sm text-red-600 dark:text-red-400 rounded-md hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors"
                            @click="open = false">
                            <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            </svg>
                            {{ __('navigation.logout') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    @else
        <!-- Auth links for guests -->
        <div class="flex items-center space-x-4">
            <a href="{{ route('login', ['locale' => app()->getLocale()]) }}"
                class="text-sm font-medium text-gray-700 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 transition-colors duration-200">
                {{ __('auth.sign_in') }}
            </a>
            <a href="{{ route('register', ['locale' => app()->getLocale()]) }}"
                class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200 shadow-sm">
                {{ __('auth.sign_up') }}
            </a>
        </div>
    @endauth
</div>
