@props(['currentLocale' => current_locale()])

@php
    $availableLocales = available_locales();
@endphp

@if(count($availableLocales) > 1)
<div x-data="{ open: false }" class="relative">
    <button 
        @click="open = !open" 
        class="flex items-center space-x-1 bg-slate-800 text-white border-slate-800 hover:bg-slate-700 rounded-full px-3 py-1.5 text-sm h-9 transition-colors"
        aria-haspopup="true"
        :aria-expanded="open"
    >
        <!-- Globe Icon -->
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        
        <!-- Language Code -->
        <span class="hidden sm:inline">{{ strtoupper($currentLocale) }}</span>
        
        <!-- Chevron Down -->
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
        </svg>
    </button>

    <!-- Dropdown Menu -->
    <div 
        x-show="open" 
        @click.away="open = false"
        x-transition:enter="transition ease-out duration-100"
        x-transition:enter-start="transform opacity-0 scale-95"
        x-transition:enter-end="transform opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-75"
        x-transition:leave-start="transform opacity-100 scale-100"
        x-transition:leave-end="transform opacity-0 scale-95"
        class="absolute right-0 mt-2 w-40 bg-white rounded-md shadow-lg py-1 z-50 ring-1 ring-black ring-opacity-5"
        style="display: none;"
        role="menu"
        aria-orientation="vertical"
        aria-labelledby="language-menu"
    >
        @foreach($availableLocales as $locale => $name)
            @php
                $routeName = request()->route() ? request()->route()->getName() : 'home';
                $routeParams = request()->route() ? request()->route()->parameters() : [];
                
                // Add or update the locale parameter
                $routeParams['locale'] = $locale;
                
                // Generate the URL for the route with the new locale
                $url = route($routeName, $routeParams, false);
                
                // Ensure we don't have double slashes
                $url = '/' . ltrim($url, '/');
            @endphp
            <a 
                href="{{ $url }}" 
                class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 {{ $currentLocale === $locale ? 'bg-gray-50 font-medium' : '' }}"
                role="menuitem"
                hreflang="{{ $locale }}"
                @if($currentLocale === $locale) aria-current="true" @endif
            >
                <span class="w-8 text-gray-500">{{ strtoupper($locale) }}</span>
                <span>{{ $name }}</span>
            </a>
        @endforeach
    </div>
</div>
@endif
