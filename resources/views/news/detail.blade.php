@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50">
    @include('partials.navbar')
    
    <div class="py-12 bg-white">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-8">
                <a href="{{ route('news.index') }}" class="inline-flex items-center text-blue-600 hover:text-blue-800 transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    {{ __('Back to News') }}
                </a>
            </div>

            <article class="prose max-w-none">
                <h1 class="text-4xl font-bold text-gray-900 mb-6">{{ $news['title'] }}</h1>
                
                <div class="flex items-center text-gray-600 text-sm mb-8">
                    <div class="flex items-center mr-6">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        <span>{{ $news['author']['first_name'] }} {{ $news['author']['last_name'] }}</span>
                    </div>
                    <div class="flex items-center mr-6">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        <span>{{ formatDate($news['created_at'], 'F d, Y') }}</span>
                    </div>
                    <div class="flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span>{{ $news['read_time'] }}</span>
                    </div>
                </div>

                @if(!empty($news['images']))
                    <div class="mb-8 rounded-lg overflow-hidden">
                        <img 
                            src="{{ asset('storage/' . $news['images'][0]) }}" 
                            alt="{{ $news['title'] }}" 
                            class="w-full h-auto object-cover"
                        >
                    </div>
                @endif

                <div class="prose-lg text-gray-700">
                    {!! $news['content'] !!}
                </div>

                @if(count($news['images']) > 1)
                    <div class="mt-12 grid grid-cols-2 md:grid-cols-3 gap-4">
                        @foreach(array_slice($news['images'], 1) as $image)
                            <div class="rounded-lg overflow-hidden">
                                <img 
                                    src="{{ asset('storage/' . $image) }}" 
                                    alt="{{ $news['title'] }} - Image {{ $loop->index + 2 }}" 
                                    class="w-full h-48 object-cover hover:opacity-90 transition-opacity"
                                >
                            </div>
                        @endforeach
                    </div>
                @endif
            </article>

            <div class="mt-16 pt-8 border-t border-gray-200">
                <h2 class="text-2xl font-bold text-gray-900 mb-6">{{ __('Related News') }}</h2>
                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach(\App\Models\News::where('id', '!=', $news['id'])
                        ->where('is_published', true)
                        ->orderBy('created_at', 'desc')
                        ->take(3)
                        ->get() as $related)
                        <div class="bg-white rounded-lg shadow overflow-hidden hover:shadow-md transition-shadow">
                            @if($related->images)
                                <a href="{{ route('news.show', $related->slug) }}" class="block">
                                    <img 
                                        src="{{ asset('storage/' . json_decode($related->images)[0]) }}" 
                                        alt="{{ $related->title }}" 
                                        class="w-full h-48 object-cover"
                                    >
                                </a>
                            @endif
                            <div class="p-6">
                                <div class="text-sm text-gray-500 mb-2">
                                    {{ formatDate($related->created_at, 'F d, Y') }} • {{ $related->read_time }}
                                </div>
                                <h3 class="text-xl font-semibold mb-3">
                                    <a href="{{ route('news.show', $related->slug) }}" class="text-gray-900 hover:text-blue-600 transition-colors">
                                        {{ $related->title }}
                                    </a>
                                </h3>
                                <a href="{{ route('news.show', $related->slug) }}" class="inline-flex items-center text-blue-600 hover:text-blue-800 font-medium">
                                    {{ __('Read More') }}
                                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    
    @include('partials.footer')
</div>
@endsection
