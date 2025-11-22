@extends('dashboard.layouts.app')

@section('title', $article->title)

@push('styles')
<style>
    .article-content {
        line-height: 1.8;
        color: #334155;
    }
    .article-content p {
        margin-bottom: 1.5rem;
    }
    .article-content img {
        max-width: 100%;
        height: auto;
        border-radius: 0.5rem;
        margin: 1.5rem 0;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    }
    .article-meta {
        display: flex;
        align-items: center;
        gap: 1.5rem;
        color: #64748b;
        font-size: 0.9375rem;
        margin: 1.5rem 0 2rem;
    }
    .article-meta span {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    .article-header {
        margin-bottom: 2.5rem;
    }
    .article-tags {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
        margin: 2rem 0 1.5rem;
    }
    .article-tag {
        background-color: #f1f5f9;
        color: #0f172a;
        padding: 0.375rem 0.75rem;
        border-radius: 9999px;
        font-size: 0.8125rem;
        font-weight: 500;
        transition: all 0.2s;
    }
    
    /* Related Articles */
    .related-article {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .related-article:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    }
    .related-article img {
        height: 160px;
        object-fit: cover;
        transition: transform 0.3s ease;
    }
    .related-article:hover img {
        transform: scale(1.05);
    }
    }
    .article-tag:hover {
        background-color: #e2e8f0;
    }
    .article-image {
        border-radius: 0.75rem;
        overflow: hidden;
        margin: 2rem 0;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    }
</style>
@endpush

@section('content')
<div class="min-h-screen bg-slate-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="max-w-4xl mx-auto">
            <article class="bg-white rounded-xl shadow-sm p-6 md:p-8">
            <a href="{{ route('dashboard.news.index') }}" class="inline-flex items-center text-sm text-blue-600 hover:text-blue-800 mb-6 transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                {{ __('Back to News') }}
            </a>
            
            <header class="mb-8">
                <h1 class="text-3xl md:text-4xl font-bold text-slate-800 leading-tight mb-6">
                    {{ $article->title }}
                </h1>
                
                <div class="article-meta">
                    <span class="text-slate-600">
                        <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        {{ $article->author->name ?? 'Admin' }}
                    </span>
                    <span class="text-slate-600">
                        <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        {{ $article->created_at->format('F j, Y') }}
                    </span>
                    <span class="text-slate-600">
                        <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        {{ $readTime }} min read
                    </span>
                </div>
                
                @if($article->category)
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                    {{ ucfirst($article->category) }}
                </span>
                @endif
            </header>
            
            @if(!empty($article->images) && is_array($article->images) && count($article->images) > 0)
                <div class="article-image">
                    <img 
                        src="{{ asset('storage/' . $article->images[0]) }}" 
                        alt="{{ $article->title }}" 
                        class="w-full h-auto max-h-[32rem] object-cover"
                        loading="lazy"
                    >
                </div>
            @endif
            
            <div class="prose max-w-none prose-slate prose-lg article-content">
                {!! $article->content !!}
            </div>
            
            @if(!empty($article->tags) && is_array($article->tags) && count($article->tags) > 0)
            <div class="article-tags">
                @foreach($article->tags as $tag)
                    <a href="{{ route('dashboard.news.index', ['tag' => $tag]) }}" class="article-tag">
                        #{{ $tag }}
                    </a>
                @endforeach
            </div>
            @endif
            
            <div class="mt-12 pt-6 border-t border-slate-200">
                <a href="{{ route('dashboard.news.index') }}" class="inline-flex items-center text-blue-600 hover:text-blue-800 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    {{ __('Back to all news') }}
                </a>
            </div>
            </article>

            <!-- Related Articles Section -->
            @if($relatedArticles->isNotEmpty())
                <section class="mt-12">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-2xl font-bold text-slate-800">Related Articles</h2>
                        <a href="{{ route('dashboard.news.index') }}?category={{ $article->category }}" class="text-sm font-medium text-blue-600 hover:text-blue-800 transition-colors">
                            View all in {{ ucfirst($article->category) }}
                            <span aria-hidden="true">→</span>
                        </a>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($relatedArticles as $related)
                            <a href="{{ route('dashboard.news.show', $related->slug) }}" class="block group">
                                <div class="bg-white rounded-lg overflow-hidden shadow-sm related-article h-full flex flex-col">
                                    <div class="overflow-hidden">
                                        @if(!empty($related->images) && is_array($related->images) && count($related->images) > 0)
                                            <img 
                                                src="{{ asset('storage/' . $related->images[0]) }}" 
                                                alt="{{ $related->title }}" 
                                                class="w-full h-48 object-cover"
                                            >
                                        @else
                                            <div class="w-full h-48 bg-gray-100 flex items-center justify-center">
                                                <svg class="h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                                                </svg>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="p-4 flex-1 flex flex-col">
                                        <div class="flex-1">
                                            <h3 class="text-lg font-semibold text-slate-800 group-hover:text-blue-600 transition-colors mb-2 line-clamp-2">
                                                {{ $related->title }}
                                            </h3>
                                            <p class="text-sm text-slate-500 line-clamp-2 mb-3">
                                                {{ Str::limit(strip_tags($related->content), 120) }}
                                            </p>
                                        </div>
                                        <div class="mt-3 flex items-center text-xs text-slate-500">
                                            <span>{{ $related->created_at->format('M d, Y') }}</span>
                                            <span class="mx-2">•</span>
                                            <span>{{ $readTime }} min read</span>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </section>
            @endif
        </div>
    </div>
</div>
@endsection
