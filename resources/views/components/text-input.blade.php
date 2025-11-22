@props(['id' => null, 'name' => null, 'type' => 'text', 'value' => '', 'error' => null, 'placeholder' => null, 'required' => false, 'autocomplete' => null, 'class' => ''])

@php
    $id = $id ?? $name;
    $classes = 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm ' . $class;
    if ($error) {
        $classes .= ' border-red-500';
    }
@endphp

<input
    @if($id) id="{{ $id }}" @endif
    @if($name) name="{{ $name }}" @endif
    type="{{ $type }}"
    @if($value) value="{{ old($name, $value) }}" @elseif(old($name)) value="{{ old($name) }}" @endif
    @if($placeholder) placeholder="{{ $placeholder }}" @endif
    @if($required) required @endif
    @if($autocomplete) autocomplete="{{ $autocomplete }}" @endif
    {{ $attributes->merge(['class' => $classes]) }}
>

@if($error)
    <p class="mt-1 text-sm text-red-600">{{ $error }}</p>
@endif
