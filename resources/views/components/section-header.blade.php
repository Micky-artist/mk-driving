@props(['title' => '', 'badge' => null, 'href' => null])

<div class="text-center mb-6">
    @if($href)
        <a href="{{ $href }}" class="inline-block group hover:scale-105 transition-transform duration-200">
            <h2 class="text-3xl sm:text-4xl font-semibold text-gray-900 dark:text-white mb-2 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors duration-200">
                {{ $title }}
            </h2>
        </a>
    @else
        <h2 class="text-3xl sm:text-4xl font-semibold text-gray-900 dark:text-white mb-2">
            {{ $title }}
        </h2>
    @endif
    <div class="w-16 h-0.5 bg-gradient-to-r from-blue-500 to-blue-600 mx-auto rounded-full"></div>
    @if($badge)
        <div class="mt-3">
            <span class="text-sm text-gray-600 dark:text-gray-400 font-medium">{{ $badge }}</span>
        </div>
    @endif
</div>
