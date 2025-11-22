@props([
    'active' => ''
])

@php
$currentLocale = request()->route('locale') ?? app()->getLocale();

// Dashboard-related navigation items (shown at the top)
$dashboardNav = [
    [
        'icon' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6',
        'key' => 'home',
        'route' => 'dashboard',
        'is_external' => false
    ],
    [
        'icon' => 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253',
        'key' => 'quizzes',
        'route' => 'dashboard.quizzes.index',
        'is_external' => false
    ]
];

// External navigation items (shown at the bottom with external icon)
$externalNav = [
    [
        'icon' => 'M3 10h18M7 15h1m4 0h1m-1-5h1m4 0h1m-9 5h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v6a2 2 0 002 2z',
        'key' => 'subscriptions',
        'route' => 'plans',
        'is_external' => true
    ],
    [
        'icon' => 'M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z',
        'key' => 'news',
        'route' => 'news',
        'is_external' => true
    ],
    [
        'icon' => 'M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z',
        'key' => 'forum',
        'route' => 'forum.index',
        'is_external' => true
    ],
    [
        'icon' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z',
        'key' => 'profile',
        'route' => 'profile.show',
        'is_external' => true
    ]
];

// Combine both navigation arrays with divider in between
$navigation = array_merge(
    $dashboardNav,
    [['is_divider' => true]],
    $externalNav
);
@endphp

<div class="h-full flex flex-col">
    <!-- Logo -->
    <div class="flex items-center justify-center h-16 px-6 border-b border-gray-200">
        <a href="{{ url('/' . $currentLocale) }}" class="flex items-center space-x-3">
            <img 
                src="{{ asset('logo.png') }}" 
                alt="MK Scholars Logo" 
                class="h-14 w-14 object-contain"
            >
            <span class="text-xl font-bold text-gray-900">
                MK DRIVING
            </span>
        </a>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 px-4 py-6 space-y-1 overflow-y-auto">
        @foreach($navigation as $item)
            @php
                // Skip processing for divider items
                if (isset($item['is_divider'])) {
                    continue;
                }

                $params = ['locale' => $currentLocale];
                $routeExists = Route::has($item['route']);
                $isActive = request()->routeIs($item['route']);
                $href = $routeExists ? route($item['route'], $params) : '#';
                
                // Special case for dashboard
                if ($item['key'] === 'home' && request()->routeIs('home')) {
                    $isActive = true;
                }
            @endphp
                
            <a 
                href="{{ $href }}" 
                class="group flex items-center justify-between px-4 py-3 text-sm font-medium rounded-lg transition-colors duration-150 {{ $isActive ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-100 hover:text-gray-900' }}"
                @if(!$routeExists) title="Coming soon" @endif
                @if($item['is_external'] ?? false) target="_blank" @endif
            >
                <div class="flex items-center">
                    <svg 
                        class="w-5 h-5 mr-3 flex-shrink-0 {{ $isActive ? 'text-blue-700' : 'text-gray-400' }}" 
                        fill="none" 
                        stroke="currentColor" 
                        viewBox="0 0 24 24"
                    >
                        <path 
                            stroke-linecap="round" 
                            stroke-linejoin="round" 
                            stroke-width="2" 
                            d="{{ $item['icon'] }}"
                        />
                    </svg>
                    <span>@lang('dashboard.navigation.' . $item['key'])</span>
                </div>
                @if($item['is_external'] ?? false)
                    <svg class="w-4 h-4 text-gray-400 group-hover:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                    </svg>
                @endif
            </a>
        @endforeach
    </nav>

    @auth
        <!-- User section & Logout -->
        <div class="border-t border-gray-200 p-4">
            <!-- User info -->
            <div class="flex items-center mb-4 px-4 py-2">
                <div class="h-10 w-10 rounded-full bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white font-semibold text-sm flex-shrink-0">
                    {{ strtoupper(substr(Auth::user()->first_name, 0, 1)) }}{{ strtoupper(substr(Auth::user()->last_name, 0, 1)) }}
                </div>
                <div class="ml-3 overflow-hidden">
                    <p class="text-sm font-medium text-gray-900 truncate">
                        {{ Auth::user()->first_name }} {{ Auth::user()->last_name }}
                    </p>
                    <p class="text-xs text-gray-500 truncate">
                        {{ Auth::user()->email }}
                    </p>
                </div>
            </div>

            <!-- Logout button -->
            <form method="POST" action="{{ route('logout', ['locale' => $currentLocale]) }}" class="w-full">
                @csrf
                <button 
                    type="submit"
                    class="flex items-center w-full px-4 py-3 text-sm font-medium text-red-600 rounded-lg hover:bg-red-50 transition-colors duration-150"
                >
                    <svg 
                        class="w-5 h-5 mr-3 flex-shrink-0" 
                        fill="none" 
                        stroke="currentColor" 
                        viewBox="0 0 24 24"
                    >
                        <path 
                            stroke-linecap="round" 
                            stroke-linejoin="round" 
                            stroke-width="2" 
                            d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"
                        />
                    </svg>
                    <span>@lang('dashboard.navigation.logout')</span>
                </button>
            </form>
        </div>
    @else
        <!-- Guest user section -->
        <div class="border-t border-gray-200 p-4 mt-auto">
            <div class="space-y-3">
                <a href="{{ route('register', ['locale' => app()->getLocale()]) }}" class="w-full flex items-center justify-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    {{ __('Sign up') }}
                </a>
                <div class="text-center">
                    <p class="text-xs text-gray-500">{{ __('Already have an account?') }}</p>
                    <a href="{{ route('login', ['locale' => app()->getLocale()]) }}" class="text-sm font-medium text-blue-600 hover:text-blue-500">
                        {{ __('Sign in') }}
                    </a>
                </div>
            </div>
        </div>
    @endauth
</div>