<nav class="bg-white shadow-sm sticky top-0 z-10">
    <div class="px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
            <!-- Mobile menu button - Only show on dashboard pages -->
            @if(request()->is('dashboard*'))
            <div class="flex items-center lg:hidden">
                <button 
                    @click="mobileMenuOpen = true" 
                    class="inline-flex items-center justify-center p-2 rounded-md text-gray-500 hover:text-gray-600 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-blue-500"
                    aria-expanded="false"
                >
                    <span class="sr-only">Open main menu</span>
                    <svg 
                        class="block h-6 w-6" 
                        xmlns="http://www.w3.org/2000/svg" 
                        fill="none" 
                        viewBox="0 0 24 24" 
                        stroke="currentColor" 
                        aria-hidden="true"
                    >
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
            </div>
            @else
            <div class="flex-shrink-0 flex items-center">
                <a href="{{ url('/') }}">
                    <img class="h-8 w-auto" src="{{ asset('logo.png') }}" alt="Logo">
                </a>
            </div>
            @endif

            <div class="flex-1 flex items-center justify-end">
                @if(request()->is('dashboard*'))
                <div class="hidden lg:block ml-4">
                    <h1 class="text-lg font-medium text-gray-900">
                        @yield('title', config('app.name'))
                    </h1>
                </div>
                @endif
                
                <div class="flex items-center space-x-4">
                    <!-- Language Switcher -->
                    <div class="px-2">
                        <x-language-switcher :currentLocale="app()->getLocale()" />
                    </div>
                    
                    @auth
                        <!-- User dropdown -->
                        <div class="ml-4 flex items-center md:ml-6" x-data="{ open: false }">
                            <button 
                                @click="open = !open" 
                                class="max-w-xs bg-white flex items-center text-sm rounded-full focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500" 
                                id="user-menu" 
                                aria-expanded="false" 
                                aria-haspopup="true"
                            >
                                <span class="sr-only">Open user menu</span>
                                <div class="h-8 w-8 rounded-full bg-blue-500 flex items-center justify-center text-white font-medium">
                                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                </div>
                                <span class="ml-2 text-sm font-medium text-gray-700 hidden md:inline">
                                    {{ Auth::user()->name }}
                                </span>
                            </button>
                            
                            <!-- Dropdown menu -->
                            <div 
                                x-show="open" 
                                @click.away="open = false"
                                x-transition:enter="transition ease-out duration-100"
                                x-transition:enter-start="transform opacity-0 scale-95"
                                x-transition:enter-end="transform opacity-100 scale-100"
                                x-transition:leave="transition ease-in duration-75"
                                x-transition:leave-start="transform opacity-100 scale-100"
                                x-transition:leave-end="transform opacity-0 scale-95"
                                class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg py-1 bg-white ring-1 ring-black ring-opacity-5 focus:outline-none z-50"
                                role="menu" 
                                aria-orientation="vertical" 
                                aria-labelledby="user-menu"
                                style="display: none;"
                            >
                                @php
                                    $currentLocale = request()->route('locale') ?? app()->getLocale();
                                @endphp
                                <a 
                                    href="{{ route('profile', ['locale' => $currentLocale]) }}" 
                                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" 
                                    role="menuitem"
                                >
                                    {{ __('Your Profile') }}
                                </a>
                                <form method="POST" action="{{ route('logout', ['locale' => $currentLocale]) }}">
                                    @csrf
                                    <button 
                                        type="submit"
                                        class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                                        role="menuitem"
                                    >
                                        {{ __('Sign out') }}
                                    </button>
                                </form>
                            </div>
                        </div>
                    @else
                        <div class="hidden md:flex items-center space-x-2">
                            <a 
                                href="{{ route('login', app()->getLocale()) }}" 
                                class="px-3 py-2 text-sm font-medium text-gray-700 hover:text-gray-900"
                            >
                                {{ __('navigation.login') }}
                            </a>
                            @if (Route::has('register'))
                                <a 
                                    href="{{ route('register', app()->getLocale()) }}" 
                                    class="px-3 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700"
                                >
                                    {{ __('navigation.register') }}
                                </a>
                            @endif
                        </div>
                    @endauth
                </div>
            </div>
        </div>
    </div>
</nav>
