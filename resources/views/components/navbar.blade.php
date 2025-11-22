<nav class="fixed w-full bg-[#023047] shadow-sm z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex items-center">
                <a href="{{ route('home', ['locale' => app()->getLocale()]) }}" class="flex items-center space-x-2">
                    <img 
                        src="{{ asset('logo.png') }}" 
                        alt="MK Driving School Logo" 
                        class="h-10 w-10 sm:h-12 sm:w-12 md:h-14 md:w-14 rounded-md"
                        onerror="this.onerror=null; this.src='{{ asset('images/logo.png') }}'"
                    >
                    <div class="hidden sm:block">
                        <span class="text-xl font-bold text-white">
                            MK Driving School
                        </span>
                        <div class="text-xs text-blue-200 -mt-1">{{ __('navigation.tagline') }}</div>
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

            <!-- Mobile menu button -->
            <div class="-mr-2 flex items-center md:hidden">
                <button 
                    @click="open = !open" 
                    class="inline-flex items-center justify-center p-2 rounded-md text-blue-200 hover:text-white hover:bg-blue-800/50 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white"
                    :aria-expanded="open"
                >
                    <span class="sr-only">{{ __('Open main menu') }}</span>
                    <svg class="h-6 w-6" x-show="!open" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                    <svg class="h-6 w-6" x-show="open" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="display: none;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile menu, show/hide based on menu state. -->
    <div 
        class="md:hidden" 
        x-show="open"
        x-transition:enter="transition ease-out duration-100 transform"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-75 transform"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        style="display: none;"
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
                        : 'border-transparent text-gray-600 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-800';
                    
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
            <div class="pt-2 pb-2 border-t border-gray-200">
                <div class="px-4 py-2">
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-2">{{ __('Change Language') }}</p>
                    <div class="flex space-x-2">
                        @foreach(config('app.available_locales') as $locale => $name)
                            <a 
                                href="{{ route('language.switch', $locale) }}" 
                                class="px-3 py-1 text-sm rounded-md {{ app()->getLocale() === $locale ? 'bg-[#023047] text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}"
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
</nav>

<!-- Add Alpine.js for mobile menu functionality -->
<script src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.8.2/dist/alpine.min.js" defer></script>
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('mobileMenu', () => ({
            open: false,
            toggle() {
                this.open = !this.open
            }
        }))
    })
</script>
