@props(['method' => 'GET', 'as' => 'a'])

@php
    $tag = $as === 'button' ? 'button' : 'a';
    $method = strtoupper($method);
    $isGet = $method === 'GET';
    $isPost = $method === 'POST';
    $isPut = $method === 'PUT';
    $isDelete = $method === 'DELETE';
    $isLink = $as === 'a';
    $isButton = $as === 'button';
    $classes = 'block w-full px-4 py-2 text-left text-sm leading-5 text-gray-700 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 transition duration-150 ease-in-out';
@endphp

<{{ $tag }} 
    {{ $attributes->merge(['class' => $classes]) }}
    @if($isLink) href="{{ $href }}" @endif
    @if($isButton) type="button" @endif
>
    @if(!$isGet)
        @method($method)
    @endif

    @if($isPost || $isPut || $isDelete)
        @csrf
    @endif

    {{ $slot }}
</{{ $tag }}>
