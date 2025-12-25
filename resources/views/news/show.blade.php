@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50">
    @include('components.navbar')
    
    <div class="pt-32 pb-16">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Back button -->
            <div class="mb-6">
                <a href="{{ route('news.index', ['locale' => app()->getLocale()]) }}" class="inline-flex items-center text-[#023047] hover:text-[#023047]/80 transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    {{ __('news.viewAll') }}
                </a>
            </div>
            
            <!-- Article -->
            <article class="bg-white rounded-xl shadow-md overflow-hidden">
                <!-- Article Header -->
                <div class="relative">
                    @if($article->images && count($article->images) > 0)
                        <img 
                            src="{{ $article->images[0] }}" 
                            alt="{{ $article->localized_title }}"
                            class="w-full h-96 object-cover"
                            onerror="this.onerror=null; this.src='/images/placeholder-news.jpg';"
                        >
                    @else
                        <div class="w-full h-96 bg-gradient-to-br from-blue-50 to-gray-100 flex items-center justify-center">
                            <svg class="w-20 h-20 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path>
                            </svg>
                        </div>
                    @endif
                    
                    <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/70 to-transparent p-6">
                        <div class="max-w-3xl mx-auto">
                            <div class="flex flex-wrap gap-2 mb-3">
                                @php
                                    $category = $article->category ?? 'news';
                                    $color = [
                                        'advice' => 'bg-blue-100 text-blue-600',
                                        'police' => 'bg-green-100 text-green-600',
                                        'trends' => 'bg-purple-100 text-purple-600',
                                        'vehicle' => 'bg-orange-100 text-orange-600',
                                        'news' => 'bg-gray-100 text-gray-600',
                                    ][$category] ?? 'bg-gray-100 text-gray-600';
                                    
                                    $icon = [
                                        'advice' => '💡',
                                        'police' => '🚔',
                                        'trends' => '📈',
                                        'vehicle' => '🚗',
                                    ][$category] ?? '📰';
                                @endphp
                                <span class="{{ $color }} px-3 py-1 rounded-full text-xs font-medium inline-flex items-center">
                                    <span class="mr-1">{{ $icon }}</span>
                                    {{ __("blogs.categories.$category") }}
                                </span>
                            </div>
                            <h1 class="text-3xl md:text-4xl font-bold text-white mb-4">{{ $article->localized_title }}</h1>
                            <div class="flex items-center text-sm text-white/90">
                                <span>{{ $article->created_at->format('F j, Y') }}</span>
                                <span class="mx-2">•</span>
                                <span>{{ $article->author->first_name }} {{ $article->author->last_name }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Article Content -->
                <div class="p-6 md:p-8">
                    <div class="prose max-w-none">
                        {!! $article->localized_content !!}
                    </div>
                    
                    @if($article->images && count($article->images) > 1)
                        <div class="mt-12">
                            <h3 class="text-xl font-semibold text-gray-900 mb-4">{{ __('news.gallery') }}</h3>
                            <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                                @foreach(array_slice($article->images, 1) as $image)
                                    <div class="aspect-square overflow-hidden rounded-lg">
                                        <img 
                                            src="{{ $image }}" 
                                            alt="{{ $article->title }} - {{ $loop->iteration }}"
                                            class="w-full h-full object-cover hover:scale-105 transition-transform duration-300"
                                            onerror="this.style.display='none';"
                                        >
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
                
                <!-- Article Footer -->
                <div class="border-t border-gray-100 p-6 bg-gray-50">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="mr-3">
                                <span class="text-sm text-gray-500">{{ __('news.share') }}:</span>
                            </div>
                            <div class="flex space-x-2">
                                <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}" 
                                   target="_blank" 
                                   class="text-gray-400 hover:text-blue-600 transition-colors"
                                   aria-label="Share on Facebook">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path fill-rule="evenodd" d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z" clip-rule="evenodd" />
                                    </svg>
                                </a>
                                <a href="https://twitter.com/intent/tweet?url={{ urlencode(url()->current()) }}&text={{ urlencode($article->title) }}" 
                                   target="_blank" 
                                   class="text-gray-400 hover:text-blue-400 transition-colors"
                                   aria-label="Share on Twitter">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path d="M8.29 20.251c7.547 0 11.675-6.253 11.675-11.675 0-.178 0-.355-.012-.53A8.348 8.348 0 0022 5.92a8.19 8.19 0 01-2.357.646 4.118 4.118 0 001.804-2.27 8.224 8.224 0 01-2.605.996 4.107 4.107 0 00-6.993 3.743 11.65 11.65 0 01-8.457-4.287 4.106 4.106 0 001.27 5.477A4.072 4.072 0 012.8 9.713v.052a4.105 4.105 0 003.292 4.022 4.095 4.095 0 01-1.853.07 4.108 4.108 0 003.834 2.85A8.233 8.233 0 012 18.407a11.616 11.616 0 006.29 1.84" />
                                    </svg>
                                </a>
                                <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ urlencode(url()->current()) }}" 
                                   target="_blank" 
                                   class="text-gray-400 hover:text-blue-700 transition-colors"
                                   aria-label="Share on LinkedIn">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z" />
                                    </svg>
                                </a>
                            </div>
                        </div>
                        <div>
                            <span class="text-sm text-gray-500">
                                {{ __('news.lastUpdated') }}: {{ timeDiffForHumans($article->updated_at) }}
                            </span>
                        </div>
                    </div>
                </div>
            </article>
            
            <!-- Related Articles -->
            @if($relatedArticles->count() > 0)
                <div class="mt-16">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">{{ __('news.relatedPosts') }}</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($relatedArticles as $related)
                            <a href="{{ route('news.show', ['locale' => app()->getLocale(), 'slug' => $related->slug]) }}" class="block group">
                                <div class="bg-white rounded-xl shadow-md hover:shadow-xl transition-all duration-300 overflow-hidden border border-gray-100 h-full flex flex-col">
                                    <div class="relative aspect-[4/3] overflow-hidden">
                                        @if($related->images && count($related->images) > 0)
                                            <img 
                                                src="{{ $related->images[0] }}" 
                                                alt="{{ $related->title }}"
                                                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                                                onerror="this.onerror=null; this.src='/images/placeholder-news.jpg';"
                                            >
                                        @else
                                            <div class="w-full h-full bg-gradient-to-br from-blue-50 to-gray-100 flex items-center justify-center">
                                                <svg class="w-12 h-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                </svg>
                                            </div>
                                        @endif
                                        <div class="absolute top-3 left-3">
                                            @php
                                                $category = $related->category ?? 'news';
                                                $color = [
                                                    'advice' => 'bg-blue-100 text-blue-600',
                                                    'police' => 'bg-green-100 text-green-600',
                                                    'trends' => 'bg-purple-100 text-purple-600',
                                                    'vehicle' => 'bg-orange-100 text-orange-600',
                                                    'news' => 'bg-gray-100 text-gray-600',
                                                ][$category] ?? 'bg-gray-100 text-gray-600';
                                                
                                                $icon = [
                                                    'advice' => '💡',
                                                    'police' => '🚔',
                                                    'trends' => '📈',
                                                    'vehicle' => '🚗',
                                                ][$category] ?? '📰';
                                            @endphp
                                            <div class="{{ $color }} px-2 py-1 rounded-full text-xs font-medium inline-flex items-center">
                                                <span class="mr-1">{{ $icon }}</span>
                                                {{ __("blogs.categories.$category") }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="p-4 flex-1 flex flex-col">
                                        <h3 class="font-semibold text-gray-900 text-sm leading-snug mb-3 group-hover:text-[#023047] transition-colors line-clamp-2">
                                            {{ $related->localized_title }}
                                        </h3>
                                        <div class="mt-auto">
                                            <div class="flex items-center justify-between text-xs text-gray-500">
                                                <span>{{ $related->created_at->format('M j, Y') }}</span>
                                                <span>{{ __('news.author') }} {{ $related->author->first_name }} {{ $related->author->last_name }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
    
    @include('components.footer')
</div>
@endsection
