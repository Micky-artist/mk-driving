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
    .fade-in {
        opacity: 0;
        transform: translateY(20px);
        transition: opacity 0.6s ease-out, transform 0.6s ease-out;
    }
    .fade-in.visible {
        opacity: 1;
        transform: translateY(0);
    }
    .scrollbar-hide::-webkit-scrollbar {
        display: none;
    }
    .scrollbar-hide {
        -ms-overflow-style: none;
        scrollbar-width: none;
    }
</style>
@endpush

@section('content')
<div class="min-h-screen bg-gradient-to-b from-gray-50 to-gray-100 dark:from-gray-900 dark:to-gray-800 transition-colors duration-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <!-- Search Bar with Animation -->
        <div class="mb-12 transform transition-all duration-500 hover:scale-[1.01]">
            <div class="flex flex-col md:flex-row items-center justify-between gap-4">
                <div class="relative w-full md:w-1/2">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <input
                        type="text"
                        id="searchInput"
                        placeholder="{{ __('news.searchPlaceholder') }}"
                        class="w-full pl-10 pr-4 py-3 rounded-xl border-2 border-transparent bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-300 shadow-lg"
                    />
                </div>
                <button id="searchButton" 
                        class="w-full md:w-auto px-6 py-3 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white rounded-xl font-medium transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl">
                    {{ __('news.searchButton') }}
                    <span class="ml-2">🔍</span>
                </button>
            </div>
        </div>

        <!-- Enhanced Category Filter with Smooth Scrolling -->
        <div class="mb-12 relative group">
            <div class="relative">
                <!-- Gradient overlays that fade in on hover -->
                <div class="absolute inset-y-0 left-0 w-12 bg-gradient-to-r from-gray-50 to-transparent dark:from-gray-900 z-10 pointer-events-none opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                <div class="absolute inset-y-0 right-0 w-12 bg-gradient-to-l from-gray-50 to-transparent dark:from-gray-900 z-10 pointer-events-none opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                
                <div class="overflow-x-auto pb-6 scrollbar-hide snap-x snap-mandatory scroll-smooth">
                    <div class="flex space-x-2.5 px-1">
                        <!-- All Categories Button -->
                        <a href="?{{ request('search') ? 'search=' . request('search') : '' }}"
                           class="snap-center px-4 py-2 rounded-xl text-sm font-medium whitespace-nowrap transition-all duration-300 flex-shrink-0 shadow-sm
                                  {{ !$activeCategory 
                                     ? 'bg-gradient-to-r from-blue-600 to-blue-700 text-white shadow-lg scale-[1.02] ring-2 ring-blue-400/30' 
                                     : 'bg-white/90 dark:bg-gray-800/90 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700/90 shadow-md hover:shadow-lg' }}">
                            {{ __('news.categories.all') }}
                            @if(!$activeCategory)
                                <span class="ml-1.5 inline-flex">✨</span>
                            @endif
                        </a>
                        
                        <!-- Category Items -->
                        @foreach($categories as $category)
                            <a href="?category={{ $category }}{{ request('search') ? '&search=' . request('search') : '' }}"
                               class="snap-center px-4 py-2 rounded-xl text-sm font-medium whitespace-nowrap transition-all duration-300 flex-shrink-0 shadow-sm
                                      {{ $activeCategory === $category 
                                         ? 'bg-gradient-to-r from-blue-600 to-blue-700 text-white shadow-lg scale-[1.02] ring-2 ring-blue-400/30' 
                                         : 'bg-white/90 dark:bg-gray-800/90 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700/90 shadow-md hover:shadow-lg' }}">
                                <span class="relative">
                                    {{ __("news.categories.$category") }}
                                    @if($activeCategory === $category)
                                        <span class="absolute -top-2 -right-3 text-yellow-400 text-xs">•</span>
                                    @endif
                                </span>
                            </a>
                        @endforeach
                    </div>
                </div>
                
                <!-- Scroll indicators (visible on mobile) -->
                <div class="md:hidden absolute bottom-0 left-0 right-0 flex justify-center space-x-1.5 pt-2">
                    @foreach($categories as $index => $category)
                        <span class="block w-1.5 h-1.5 rounded-full bg-gray-300 dark:bg-gray-600 transition-all duration-300 {{ $activeCategory === $category ? 'w-6 bg-blue-500' : '' }}"></span>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Page Title with Animation -->
        <div class="text-center mb-16 fade-in">
            <h1 class="text-4xl md:text-5xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-blue-600 to-cyan-500 mb-4">
                {{ __('news.newsOnTime') }}
            </h1>
            <p class="text-lg text-gray-600 dark:text-gray-300 max-w-3xl mx-auto">
                {{ __('news.newsDescription') }}
            </p>
        </div>

        <!-- Articles Grid -->
        <div id="newsContainer">
            @if($articles->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    @foreach($articles as $article)
                        <a href="{{ route('news.detail', ['locale' => app()->getLocale(), 'slug' => $article->slug]) }}" 
                           class="group block transform transition-all duration-500 hover:-translate-y-2 fade-in" data-animate>
                            <div class="h-full bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm rounded-2xl overflow-hidden border border-gray-200 dark:border-gray-700 shadow-lg hover:shadow-2xl transition-all duration-300">
                                <!-- Image Container -->
                                <div class="relative aspect-[4/3] overflow-hidden">
                                    @php
                                        $hasImages = is_array($article->images) && count($article->images) > 0 && !empty($article->images[0]);
                                    @endphp
                                    @if($hasImages)
                                        <img 
                                            src="{{ $article->images[0] }}" 
                                            alt="{{ $article->title }}"
                                            class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110"
                                            onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';"
                                            loading="lazy"
                                        >
                                    @endif
                                    <div class="w-full h-full {{ $hasImages ? 'hidden' : 'flex' }} items-center justify-center bg-gradient-to-br from-blue-50 to-gray-100 dark:from-gray-700 dark:to-gray-800">
                                        <svg class="w-16 h-16 text-gray-400 dark:text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                    </div>
                                    
                                    <!-- Category Badge -->
                                    @php
                                        $category = $article->category ?? 'news';
                                        $colorClasses = [
                                            'advice' => 'bg-blue-100 text-blue-600 dark:bg-blue-900/50 dark:text-blue-300',
                                            'police' => 'bg-green-100 text-green-600 dark:bg-green-900/50 dark:text-green-300',
                                            'trends' => 'bg-purple-100 text-purple-600 dark:bg-purple-900/50 dark:text-purple-300',
                                            'vehicle' => 'bg-orange-100 text-orange-600 dark:bg-orange-900/50 dark:text-orange-300',
                                            'news' => 'bg-gray-100 text-gray-600 dark:bg-gray-700/50 dark:text-gray-300',
                                        ][$category] ?? 'bg-gray-100 text-gray-600 dark:bg-gray-700/50 dark:text-gray-300';
                                        
                                        $icons = [
                                            'advice' => '💡',
                                            'police' => '🚔',
                                            'trends' => '📈',
                                            'vehicle' => '🚗',
                                        ][$category] ?? '📰';
                                    @endphp
                                    <div class="absolute top-3 left-3 transform transition-transform duration-300 group-hover:scale-110">
                                        <div class="{{ $colorClasses }} px-3 py-1 rounded-full text-xs font-medium inline-flex items-center backdrop-blur-sm">
                                            <span class="mr-1.5 text-sm">{{ $icons }}</span>
                                            {{ __("news.categories.$category") }}
                                        </div>
                                    </div>

                                    @if(is_array($article->images) && count($article->images) > 1)
                                        <div class="absolute top-3 right-3 bg-black/60 text-white px-2.5 py-1 rounded-full text-xs font-medium backdrop-blur-sm">
                                            +{{ count($article->images) - 1 }} {{ __('news.more_photos') }}
                                        </div>
                                    @endif
                                </div>

                                <!-- Article Content -->
                                <div class="p-5">
                                    <h3 class="font-bold text-gray-900 dark:text-white text-lg leading-tight mb-3 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors duration-300 line-clamp-2">
                                        {{ $article->localized_title }}
                                    </h3>
                                    <p class="text-gray-600 dark:text-gray-300 text-sm mb-4 line-clamp-3">
                                        {{ $article->localized_excerpt ?? \Illuminate\Support\Str::limit(strip_tags($article->localized_content), 120) }}
                                    </p>
                                    <div class="flex items-center justify-between text-sm text-gray-500 dark:text-gray-400">
                                        <span class="flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                            {{ $article->created_at->translatedFormat('M j, Y') }}
                                        </span>
                                        @if($article->author)
                                            <span class="flex items-center">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                                </svg>
                                                {{ $article->author->first_name }} {{ $article->author->last_name }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>

                <!-- Pagination -->
                @if($articles->hasPages())
                    <div class="mt-16 fade-in">
                        {{ $articles->links('pagination::tailwind') }}
                    </div>
                @endif
            @else
                <div class="text-center py-20 fade-in">
                    <div class="max-w-md mx-auto">
                        <div class="w-24 h-24 mx-auto mb-6 text-gray-400">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-medium text-gray-900 dark:text-white mb-2">{{ __('news.no_articles_found') }}</h3>
                        <p class="text-gray-500 dark:text-gray-400">{{ __('news.try_different_search') }}</p>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Intersection Observer for fade-in animations
    document.addEventListener('DOMContentLoaded', function() {
        const animateElements = document.querySelectorAll('[data-animate]');
        
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                    // Unobserve after animation
                    observer.unobserve(entry.target);
                }
            });
        }, {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        });

        animateElements.forEach(element => {
            observer.observe(element);
            // Add staggered delay based on element position
            element.style.transitionDelay = `${Math.random() * 0.2}s`;
        });

        // Add scroll event for category filter shadow
        const categoryContainer = document.querySelector('.overflow-x-auto');
        if (categoryContainer) {
            const updateScrollShadow = () => {
                const { scrollLeft, scrollWidth, clientWidth } = categoryContainer;
                const leftGradient = document.querySelector('.left-gradient');
                const rightGradient = document.querySelector('.right-gradient');
                
                if (leftGradient) leftGradient.style.opacity = scrollLeft > 10 ? '1' : '0';
                if (rightGradient) rightGradient.style.opacity = scrollLeft < scrollWidth - clientWidth - 10 ? '1' : '0';
            };
            
            categoryContainer.addEventListener('scroll', updateScrollShadow);
            // Initial check
            updateScrollShadow();
        }
    });
// Add smooth scrolling to category links
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        document.querySelector(this.getAttribute('href')).scrollIntoView({
            behavior: 'smooth'
        });
    });
});

// Add fade-in animation to elements when they come into view
const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.classList.add('fade-in-visible');
            observer.unobserve(entry.target);
        }
    });
}, {
    threshold: 0.1
});

document.querySelectorAll('.fade-in').forEach((el) => {
    observer.observe(el);
});
</script>
@endpush