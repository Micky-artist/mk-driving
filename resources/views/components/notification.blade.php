@props(['type' => 'info', 'message' => ''])

@php
    $colors = [
        'success' => 'bg-green-100 border-green-500 text-green-900',
        'error' => 'bg-red-100 border-red-500 text-red-900',
        'warning' => 'bg-yellow-100 border-yellow-500 text-yellow-900',
        'info' => 'bg-blue-100 border-blue-500 text-blue-900',
    ][$type] ?? 'bg-gray-100 border-gray-500 text-gray-900';
    
    $icon = [
        'success' => 'check-circle',
        'error' => 'x-circle',
        'warning' => 'exclamation-triangle',
        'info' => 'information-circle',
    ][$type] ?? 'information-circle';
@endphp

<div x-data="{ show: true }" 
     x-show="show"
     x-init="setTimeout(() => show = false, 5000)"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0 transform translate-x-8"
     x-transition:enter-end="opacity-100 transform translate-x-0"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100 transform translate-x-0"
     x-transition:leave-end="opacity-0 transform translate-x-8"
     class="fixed right-4 bottom-4 w-80 p-4 rounded-lg shadow-lg border-l-4 {{ $colors }} z-50">
    <div class="flex items-start">
        <div class="flex-shrink-0">
            @if($type === 'success')
                <svg class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
            @elseif($type === 'error')
                <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            @elseif($type === 'warning')
                <svg class="h-6 w-6 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            @else
                <svg class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            @endif
        </div>
        <div class="ml-3 w-0 flex-1 pt-0.5">
            <p class="text-sm font-medium">
                {{ $message }}
            </p>
        </div>
        <div class="ml-4 flex-shrink-0 flex">
            <button @click="show = false" class="inline-flex text-gray-500 hover:text-gray-700 focus:outline-none">
                <span class="sr-only">Close</span>
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    </div>
</div>
