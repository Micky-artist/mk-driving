@extends('layouts.app')

@push('styles')
<style>
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    .aspect-\[4\/3\] {
        aspect-ratio: 4/3;
    }
</style>
@endpush

@section('content')
<div class="bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Search Bar -->
            <div class="mb-8">
                <div class="flex items-center max-w-2xl mx-auto">
                    <div class="relative flex-1">
                        <input
                            type="text"
                            id="searchInput"
                            placeholder="{{ __('news.searchPlaceholder') }}"
                            class="w-full px-4 py-3 pl-12 pr-4 border border-gray-300 rounded-l-xl focus:outline-none focus:ring-2 focus:ring-[#023047] focus:border-transparent"
                        />
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                    </div>
                    <button id="searchButton" class="bg-[#023047] text-white px-6 py-3 rounded-r-xl hover:bg-[#023047]/90 transition-colors">
                        {{ __('news.searchButton') }}
                    </button>
                </div>
            </div>

            <!-- Category Filter -->
            <div class="flex flex-wrap justify-center gap-2 mb-8">
                @foreach($categories as $category)
                    <a href="?category={{ $category }}{{ request('search') ? '&search=' . request('search') : '' }}"
                       class="px-4 py-2 rounded-full text-sm font-medium transition-colors
                              {{ $activeCategory === $category 
                                 ? 'bg-[#023047] text-white' 
                                 : 'bg-white text-gray-700 hover:bg-gray-100' }}">
                        {{ __("news.categories.$category") }}
                    </a>
                @endforeach
            </div>

            <!-- Page Title -->
            <div class="text-center mb-12 flex flex-col justify-center items-center">
                <h1 class="text-4xl font-bold text-[#023047] mb-4">
                    {{ __('news.newsOnTime') }}
                </h1>
                <p class="w-full md:w-[70%] font-medium">
                    {{ __('news.newsDescription') }}
                </p>
            </div>

            <!-- Articles -->
            <div id="newsContainer">
                @if($articles->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($articles as $article)
                            <a href="{{ route('news.show', ['locale' => app()->getLocale(), 'slug' => $article->slug]) }}" class="block group">
                                <div class="bg-white rounded-xl shadow-md hover:shadow-xl transition-all duration-300 overflow-hidden border border-gray-100">
                                    <div class="relative aspect-[4/3] overflow-hidden">
                                        @php
                                            $hasImages = is_array($article->images) && count($article->images) > 0 && !empty($article->images[0]);
                                        @endphp
                                        @if($hasImages)
                                            <img 
                                                src="{{ $article->images[0] }}" 
                                                alt="{{ $article->title }}"
                                                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                                                onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';"
                                                loading="lazy"
                                            >
                                        @endif
                                        <div class="w-full h-full {{ $hasImages ? 'hidden' : 'flex' }} items-center justify-center bg-gradient-to-br from-blue-50 to-gray-100">
                                            <svg class="w-12 h-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                        </div>
                                        <div class="absolute top-3 left-3">
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
                                            <div class="{{ $color }} px-2 py-1 rounded-full text-xs font-medium inline-flex items-center">
                                                <span class="mr-1">{{ $icon }}</span>
                                                {{ __("news.categories.$category") }}
                                            </div>
                                        </div>
                                        @if(is_array($article->images) && count($article->images) > 1)
                                            <div class="absolute top-3 right-3">
                                                <div class="bg-black/50 text-white px-2 py-1 rounded-full text-xs font-medium">
                                                    +{{ count($article->images) - 1 }}
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="p-4">
                                        <h3 class="font-semibold text-gray-900 text-sm leading-snug mb-3 group-hover:text-[#023047] transition-colors line-clamp-2">
                                            {{ $article->localized_title }}
                                        </h3>
                                        <div class="flex items-center justify-between text-xs text-gray-500">
                                            <span>{{ $article->created_at->format('M j, Y') }}</span>
                                            @if($article->author)
                                                <span>{{ __('news.author') }} {{ $article->author->first_name }} {{ $article->author->last_name }}</span>
                                            @else
                                                <span>{{ __('news.unknown_author') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                    
                    <!-- Pagination -->
                    @if($articles->hasPages())
                        <div class="mt-8">
                            {{ $articles->appends(request()->query())->links() }}
                        </div>
                    @endif
                @else
                    <div class="text-center py-16">
                        <p class="text-gray-600">{{ __('blogs.noArticlesFound') }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

@push('scripts')
<script>
// Category icon mapping
const categoryIcons = {
    'advice': '💡',
    'police': '🚔',
    'trends': '📈',
    'vehicle': '🚗',
    'news': '📰'
};

// Category color mapping
const categoryColors = {
    'advice': 'bg-blue-100 text-blue-600',
    'police': 'bg-green-100 text-green-600',
    'trends': 'bg-purple-100 text-purple-600',
    'vehicle': 'bg-orange-100 text-orange-600',
    'news': 'bg-gray-100 text-gray-600'
};

document.addEventListener('DOMContentLoaded', function() {
    const newsContainer = document.getElementById('newsContainer');
    const searchInput = document.getElementById('searchInput');
    const searchButton = document.getElementById('searchButton');
    
    // Set initial search input value from URL
    const urlParams = new URLSearchParams(window.location.search);
    const searchQuery = urlParams.get('search') || '';
    searchInput.value = searchQuery;
    
    // Search functionality with debounce
    let searchTimeout;
    
    function handleSearch() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            const searchQuery = searchInput.value.trim();
            const url = new URL(window.location.href);
            
            if (searchQuery) {
                url.searchParams.set('search', searchQuery);
                // Reset to first page when searching
                url.searchParams.set('page', '1');
            } else {
                url.searchParams.delete('search');
            }
            
            // Update URL without page reload
            window.history.pushState({}, '', url.toString());
            
            // Load articles with search query and current category
            loadNews(searchQuery);
        }, 500);
    }
    
    // Load news articles with current filters
    function loadNews(searchQuery = '') {
        const url = new URL('/api/news', window.location.origin);
        const params = new URLSearchParams(window.location.search);
        
        // Add current pagination, search, and category filters
        if (searchQuery) {
            params.set('search', searchQuery);
        } else {
            params.delete('search');
        }
        
        // Preserve category filter
        const category = '{{ $activeCategory }}';
        if (category && category !== 'all') {
            params.set('category', category);
        } else {
            params.delete('category');
        }
        
        // Add pagination if not in URL
        if (!params.has('page')) {
            params.set('page', '1');
        }
        
        url.search = params.toString();
        
        // Show loading state
        const currentContent = newsContainer.innerHTML;
        newsContainer.innerHTML = `
            <div class="flex items-center justify-center py-16">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                <span class="ml-2">{{ __('blogs.Loading articles') }}...</span>
            </div>
        `;
        
        fetch(url, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.data && data.data.length > 0) {
                renderNews(data);
            } else {
                newsContainer.innerHTML = `
                    <div class="text-center py-16">
                        <p class="text-gray-600">{{ __('blogs.noArticlesFound') }}</p>
                    </div>
                `;
            }
        })
        .catch(error => {
            console.error('Error loading news:', error);
            newsContainer.innerHTML = `
                <div class="text-center py-16">
                    <p class="text-red-500">{{ __('Failed to load news. Please try again later.') }}</p>
                    <button onclick="window.location.reload()" class="mt-4 px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                        {{ __('Retry') }}
                    </button>
                </div>
            `;
        });
    }
    
    // Render news articles with pagination
    function renderNews(response) {
        const articles = response.data || [];
        const pagination = response.meta;
        
        if (articles.length === 0) {
            newsContainer.innerHTML = `
                <div class="text-center py-16">
                    <p class="text-gray-600">{{ __('blogs.noArticlesFound') }}</p>
                </div>
            `;
            return;
        }
        
        const articlesHtml = articles.map(article => {
            const category = article.category || 'news';
            const icon = categoryIcons[category] || '📰';
            const color = categoryColors[category] || 'bg-gray-100 text-gray-600';
            const imageCount = article.images ? article.images.length : 0;
            const imageUrl = imageCount > 0 ? article.images[0] : '';
            const hasMultipleImages = imageCount > 1;
            const authorName = article.author ? `${article.author.first_name} ${article.author.last_name}` : '';
            const publishDate = new Date(article.created_at).toLocaleDateString('en-US', {
                year: 'numeric',
                month: 'short',
                day: 'numeric'
            });
            
            return `
                <a href="/news/${article.slug}" class="block group">
                    <div class="bg-white rounded-xl shadow-md hover:shadow-xl transition-all duration-300 overflow-hidden border border-gray-100 h-full flex flex-col">
                        <div class="relative aspect-[4/3] overflow-hidden">
                            ${imageUrl ? `
                                <img 
                                    src="${imageUrl}" 
                                    alt="${article.title}"
                                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                                    onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';"
                                    loading="lazy"
                                >
                            ` : ''}
                            <div class="w-full h-full ${imageUrl ? 'hidden' : 'flex'} items-center justify-center bg-gradient-to-br from-blue-50 to-gray-100">
                                <svg class="w-12 h-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                            <div class="absolute top-3 left-3">
                                <div class="${color} px-2 py-1 rounded-full text-xs font-medium inline-flex items-center">
                                    <span class="mr-1">${icon}</span>
                                    {{ __("blogs.categories.${category}" || 'news') }}
                                </div>
                            </div>
                            ${hasMultipleImages ? `
                                <div class="absolute top-3 right-3">
                                    <div class="bg-black/50 text-white px-2 py-1 rounded-full text-xs font-medium">
                                        +${imageCount - 1}
                                    </div>
                                </div>
                            ` : ''}
                        </div>
                        <div class="p-4 flex-1 flex flex-col">
                            <h3 class="font-semibold text-gray-900 text-sm leading-snug mb-3 group-hover:text-[#023047] transition-colors line-clamp-2">
                                ${article.title}
                            </h3>
                            <div class="mt-auto">
                                <div class="flex items-center justify-between text-xs text-gray-500">
                                    <span>${publishDate}</span>
                                    <span>{{ __('blogs.author') }} ${authorName}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            `;
        }).join('');
        
        // Generate pagination links
        let paginationHtml = '';
        if (pagination && pagination.last_page > 1) {
            const currentPage = pagination.current_page;
            const lastPage = pagination.last_page;
            
            paginationHtml = `
                <div class="mt-8 flex items-center justify-center space-x-2">
                    ${currentPage > 1 ? `
                        <a href="?${updateQueryString('page', currentPage - 1)}" class="px-3 py-1 rounded-md border border-gray-300 hover:bg-gray-50">
                            &larr; {{ __('Previous') }}
                        </a>
                    ` : ''}
                    
                    ${Array.from({ length: Math.min(5, lastPage) }, (_, i) => {
                        let pageNum;
                        if (lastPage <= 5) {
                            pageNum = i + 1;
                        } else if (currentPage <= 3) {
                            pageNum = i + 1;
                        } else if (currentPage >= lastPage - 2) {
                            pageNum = lastPage - 4 + i;
                        } else {
                            pageNum = currentPage - 2 + i;
                        }
                        
                        return `
                            <a href="?${updateQueryString('page', pageNum)}" 
                               class="px-3 py-1 rounded-md ${currentPage === pageNum ? 'bg-[#023047] text-white' : 'border border-gray-300 hover:bg-gray-50'}">
                                ${pageNum}
                            </a>
                        `;
                    }).join('')}
                    
                    ${currentPage < lastPage ? `
                        <a href="?${updateQueryString('page', currentPage + 1)}" class="px-3 py-1 rounded-md border border-gray-300 hover:bg-gray-50">
                            {{ __('Next') }} &rarr;
                        </a>
                    ` : ''}
                </div>
            `;
        }
        
        newsContainer.innerHTML = `
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                ${articlesHtml}
            </div>
            ${paginationHtml}
        `;
    }
    
    // Helper function to update query string parameters
    function updateQueryString(key, value) {
        const params = new URLSearchParams(window.location.search);
        if (value) {
            params.set(key, value);
        } else {
            params.delete(key);
        }
        return params.toString();
    }
    
    // Event listeners
    searchInput.addEventListener('input', handleSearch);
    
    searchButton.addEventListener('click', () => {
        handleSearch();
    });
    
    searchInput.addEventListener('keypress', (e) => {
        if (e.key === 'Enter') {
            handleSearch();
        }
    });
    
    // Handle browser back/forward buttons
    window.addEventListener('popstate', () => {
        const searchQuery = new URLSearchParams(window.location.search).get('search') || '';
        searchInput.value = searchQuery;
        loadNews(searchQuery);
    });
    
    // Initial load (only if not already server-rendered)
    if (document.querySelector('#newsContainer').children.length === 0) {
        loadNews(searchQuery);
    }
});
</script>
@endpush
@endsection
