@extends('layouts.app')

@section('title', __('News'))

@push('styles')
<style>
    .news-card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .news-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    }
    .news-image {
        height: 200px;
        object-fit: cover;
    }
    .read-time {
        display: flex;
        align-items: center;
        gap: 0.25rem;
    }
    .publish-date {
        display: flex;
        align-items: center;
        gap: 0.25rem;
    }
    .badge {
        font-size: 0.75rem;
        font-weight: 500;
        padding: 0.25rem 0.5rem;
        border-radius: 0.25rem;
    }
</style>
@endpush

@section('content')
    <!-- Include unified navbar for dashboard -->
    <x-unified-navbar :showUserStats="true" />
    
    <div class=""><div class="py-6 px-4 sm:px-6 lg:px-8">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">{{ __('Latest News') }}</h1>
        <p class="mt-1 text-sm text-gray-500">
            {{ __('Stay updated with the latest news and announcements.') }}
        </p>
    </div>

    <!-- Category Filter and Search -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <!-- Category Filter -->
        <div class="flex flex-wrap gap-2">
            @foreach($categories as $category)
                <a 
                    href="?category={{ $category }}@if(request('search'))&search={{ request('search') }}@endif"
                    class="px-3 py-1 rounded-full text-sm font-medium transition-colors
                        {{ $activeCategory === $category 
                            ? 'bg-blue-600 text-white' 
                            : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}"
                >
                    {{ ucfirst($category) }}
                </a>
            @endforeach
        </div>

        <!-- Search Form -->
        <form method="GET" class="w-full sm:w-auto">
            <div class="relative">
                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Search news..."
                    class="w-full sm:w-64 pl-10 pr-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                >
                <div class="absolute left-3 top-1/2 transform -translate-y-1/2 text-slate-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                @if(request('search') || request('category') !== 'all')
                    <a 
                        href="{{ route('dashboard.news.index') }}" 
                        class="absolute right-3 top-1/2 transform -translate-y-1/2 text-slate-400 hover:text-slate-600"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </a>
                @endif
            </div>
            @if(request('category') !== 'all')
                <input type="hidden" name="category" value="{{ request('category') }}">
            @endif
        </form>
    </div>

    @if(session('success'))
        <div class="rounded-md bg-green-50 p-4 mb-6">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                </div>
            </div>
        </div>
    @endif

    @if($news->isEmpty())
        <div class="text-center py-12">
            <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-12 w-12 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <h3 class="mt-2 text-sm font-medium text-slate-900">No news found</h3>
            <p class="mt-1 text-sm text-slate-500">
                @if(request('search'))
                    Try adjusting your search or filter to find what you're looking for.
                @else
                    There are no news articles in this category yet.
                @endif
            </p>
            <div class="mt-6">
                <a 
                    href="{{ route('dashboard.news.index') }}" 
                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="-ml-1 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    Reset filters
                </a>
            </div>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($news as $item)
            <div class="bg-white rounded-lg shadow overflow-hidden news-card">
                <div class="relative h-48 overflow-hidden">
                    @if(!empty($item->images) && is_array($item->images) && count($item->images) > 0)
                        <img 
                            src="{{ asset('storage/' . $item->images[0]) }}" 
                            alt="{{ $item->title }}" 
                            class="w-full h-full object-cover"
                        >
                    @else
                        <div class="w-full h-full bg-gray-100 flex items-center justify-center">
                            <svg class="h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                    @endif
                    <div class="absolute top-2 right-2">
                        <span class="bg-blue-100 text-blue-800 text-xs font-semibold px-2.5 py-0.5 rounded">
                            {{ $item->category ?? 'General' }}
                        </span>
                    </div>
                </div>
                <div class="p-4">
                    <div class="flex justify-between items-center mb-2">
                        <div class="text-xs text-gray-500 flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            {{ $item->author->name ?? 'Admin' }}
                        </div>
                        <div class="text-xs text-gray-500 flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            {{ $item->created_at->format('M d, Y') }}
                        </div>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">
                        <a href="{{ route('news.show', $item->slug) }}" class="hover:text-blue-600 transition-colors">
                            {{ $item->title }}
                        </a>
                    </h3>
                    <p class="text-sm text-gray-600 mb-4 line-clamp-2">
                        {{ Str::limit(strip_tags($item->content), 100) }}
                    </p>
                    <div class="flex justify-between items-center">
                        <span class="text-xs text-gray-500 flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            {{ $item->read_time ?? '3' }} min read
                        </span>
                        <a href="{{ route('news.show', $item->slug) }}" class="text-sm font-medium text-blue-600 hover:text-blue-700">
                            {{ __('Read more') }} →
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">{{ __('No news yet') }}</h3>
                <p class="mt-1 text-sm text-gray-500">{{ __('Get started by creating a new news article.') }}</p>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="mt-8">
        {{ $news->withQueryString()->links() }}
    </div>
</div>
@endsection
