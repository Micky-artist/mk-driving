@props(['currentLocale' => current_locale()])

@php
    $availableLocales = available_locales();
@endphp

@if(count($availableLocales) > 1)
    @php
        $routeName = request()->route() ? request()->route()->getName() : 'home';
        $baseRouteParams = request()->route() ? request()->route()->parameters() : [];

        $locales = array_keys($availableLocales);
        $targetLocale = count($locales) === 2
            ? ($currentLocale === $locales[0] ? $locales[1] : $locales[0])
            : ($locales[0] ?? $currentLocale);

        $routeParams = $baseRouteParams;
        $routeParams['locale'] = $targetLocale;
        $url = route($routeName, $routeParams, false);
        $url = '/' . ltrim($url, '/');

        $flag = $currentLocale === 'rw' ? '🇷🇼' : '🇺🇸';
    @endphp

    <a
        href="{{ $url }}"
        class="text-blue-200 hover:text-white text-xl transition-colors duration-200"
        hreflang="{{ $targetLocale }}"
        aria-label="Change language"
        title="Change language"
    >
        {{ $flag }}
    </a>
@endif
