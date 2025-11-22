@props([
    'logo' => null,
])

<div {{ $attributes->merge(['class' => 'w-full max-w-md mx-auto shadow-lg border-0 mt-10 bg-white rounded-lg overflow-hidden']) }}>
    <div class="px-8 py-8">
        {{ $logo }}
        {{ $slot }}
    </div>
</div>
