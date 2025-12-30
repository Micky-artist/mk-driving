@extends('layouts.app')

@push('styles')
<style>
.news-bg-animation {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    pointer-events: none;
    overflow: hidden;
    z-index: -1;
    background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 25%, #0f172a 50%, #1e3a8a 75%, #1e40af 100%);
    background-size: 400% 400%;
    animation: gradientShift 15s ease infinite;
}

@keyframes gradientShift {
    0% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
    100% { background-position: 0% 50%; }
}

.floating-shape {
    position: absolute;
    opacity: 0.1;
    animation: float 20s infinite ease-in-out;
}

.shape-1 {
    width: 80px;
    height: 80px;
    background: linear-gradient(45deg, #f97316, #fb923c);
    border-radius: 30% 70% 70% 30% / 30% 30% 70% 70%;
    top: 10%;
    left: 10%;
    animation-delay: 0s;
    animation-duration: 25s;
}

.shape-2 {
    width: 120px;
    height: 120px;
    background: linear-gradient(135deg, #3b82f6, #60a5fa);
    border-radius: 63% 37% 54% 46% / 55% 48% 52% 45%;
    top: 60%;
    right: 10%;
    animation-delay: 5s;
    animation-duration: 30s;
}

.shape-3 {
    width: 60px;
    height: 60px;
    background: linear-gradient(90deg, #f97316, #fbbf24);
    clip-path: polygon(50% 0%, 100% 50%, 50% 100%, 0% 50%);
    bottom: 20%;
    left: 20%;
    animation-delay: 10s;
    animation-duration: 20s;
}

.shape-4 {
    width: 100px;
    height: 100px;
    background: linear-gradient(180deg, #1e40af, #3b82f6);
    border-radius: 40% 60% 60% 40% / 60% 30% 70% 40%;
    top: 30%;
    right: 30%;
    animation-delay: 15s;
    animation-duration: 35s;
}

.shape-5 {
    width: 90px;
    height: 90px;
    background: linear-gradient(270deg, #fb923c, #f97316);
    clip-path: polygon(25% 0%, 75% 0%, 100% 50%, 75% 100%, 25% 100%, 0% 50%);
    bottom: 10%;
    right: 20%;
    animation-delay: 20s;
    animation-duration: 28s;
}

@keyframes float {
    0%, 100% {
        transform: translateY(0px) rotate(0deg) scale(1);
    }
    25% {
        transform: translateY(-30px) rotate(90deg) scale(1.1);
    }
    50% {
        transform: translateY(20px) rotate(180deg) scale(0.9);
    }
    75% {
        transform: translateY(-15px) rotate(270deg) scale(1.05);
    }
}

.particle {
    position: absolute;
    width: 4px;
    height: 4px;
    background: rgba(251, 146, 60, 0.6);
    border-radius: 50%;
    animation: particleFloat 15s infinite linear;
}

.particle-1 { top: 20%; left: 5%; animation-delay: 0s; }
.particle-2 { top: 50%; left: 10%; animation-delay: 2s; }
.particle-3 { top: 80%; left: 15%; animation-delay: 4s; }
.particle-4 { top: 30%; left: 80%; animation-delay: 6s; }
.particle-5 { top: 60%; left: 85%; animation-delay: 8s; }
.particle-6 { top: 10%; left: 50%; animation-delay: 10s; }
.particle-7 { top: 90%; left: 60%; animation-delay: 12s; }

@keyframes particleFloat {
    0% {
        transform: translateY(0px) translateX(0px);
        opacity: 0;
    }
    10% {
        opacity: 1;
    }
    90% {
        opacity: 1;
    }
    100% {
        transform: translateY(-100vh) translateX(50px);
        opacity: 0;
    }
}

/* Dark mode adjustments */
.dark .news-bg-animation {
    background: linear-gradient(135deg, #0f172a 0%, #1e293b 25%, #020817 50%, #0f172a 75%, #1e293b 100%);
}

.dark .floating-shape {
    opacity: 0.15;
}

.dark .particle {
    background: rgba(251, 146, 60, 0.4);
}
</style>
@endpush

@section('content')
    <!-- Animated Background -->
    <div class="news-bg-animation">
        <div class="floating-shape shape-1"></div>
        <div class="floating-shape shape-2"></div>
        <div class="floating-shape shape-3"></div>
        <div class="floating-shape shape-4"></div>
        <div class="floating-shape shape-5"></div>
        <div class="particle particle-1"></div>
        <div class="particle particle-2"></div>
        <div class="particle particle-3"></div>
        <div class="particle particle-4"></div>
        <div class="particle particle-5"></div>
        <div class="particle particle-6"></div>
        <div class="particle particle-7"></div>
    </div>

    <div class="bg-gray-50/90 dark:bg-gray-900/90 transition-colors duration-300">
        <!-- Main Content Container -->
        <div class="max-w-7xl mx-auto px-3 sm:px-4 lg:px-16">
            <div class="py-8">
                <!-- Header Section -->
                <div class="text-center mb-12">
                    <h1 class="text-4xl font-bold text-gray-900 dark:text-white mb-4">
                        {{ __('news.title') }}
                    </h1>
                    <p class="text-xl text-gray-600 dark:text-gray-300 max-w-3xl mx-auto">
                        {{ __('news.description') }}
                    </p>
                </div>

                <!-- News Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    @forelse($news as $newsItem)
                        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden hover:shadow-lg transition-all duration-300">
                            <!-- Featured Image -->
                            @if($newsItem->featured_image)
                                <div class="aspect-w-16 aspect-h-9">
                                    <img src="{{ asset('storage/' . $newsItem->featured_image) }}" 
                                         alt="{{ $newsItem->localized_title }}"
                                         class="w-full h-48 object-cover">
                                </div>
                            @else
                                <div class="w-full h-48 bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center">
                                    <svg class="w-16 h-16 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                                    </svg>
                                </div>
                            @endif

                            <!-- Content -->
                            <div class="p-6">
                                <!-- Category Badge -->
                                <div class="flex items-center justify-between mb-3">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400">
                                        {{ __('news.article') }}
                                    </span>
                                    <span class="text-sm text-gray-500 dark:text-gray-400">
                                        {{ $newsItem->published_at->format('M j, Y') }}
                                    </span>
                                </div>

                                <!-- Title -->
                                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-3 line-clamp-2">
                                    <a href="{{ route('news.show', ['locale' => app()->getLocale(), 'news' => $newsItem->slug]) }}" 
                                       class="hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                                        {{ $newsItem->localized_title }}
                                    </a>
                                </h3>

                                <!-- Excerpt -->
                                <p class="text-gray-600 dark:text-gray-300 text-sm mb-4 line-clamp-3">
                                    {{ Str::limit(strip_tags($newsItem->excerpt), 150) }}
                                </p>

                                <!-- Meta Info -->
                                <div class="flex items-center justify-between text-sm text-gray-500 dark:text-gray-400">
                                    <div class="flex items-center space-x-2">
                                        <div class="w-6 h-6 rounded-full bg-gradient-to-br from-orange-500 to-orange-600 flex items-center justify-center text-white text-xs font-bold">
                                            {{ substr($newsItem->user->firstName ?? 'A', 0, 1) }}{{ substr($newsItem->user->lastName ?? 'dmin', 0, 1) }}
                                        </div>
                                        <span>{{ $newsItem->user->firstName ?? 'Admin' }} {{ $newsItem->user->lastName ?? '' }}</span>
                                    </div>
                                    <div class="flex items-center space-x-4">
                                        <span class="flex items-center space-x-1">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                            <span>{{ $newsItem->views ?? 0 }}</span>
                                        </span>
                                        <span class="flex items-center space-x-1">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                            </svg>
                                            <span>{{ $newsItem->comments_count ?? 0 }}</span>
                                        </span>
                                    </div>
                                </div>

                                <!-- Read More Button -->
                                <div class="mt-6">
                                    <a href="{{ route('news.show', ['locale' => app()->getLocale(), 'news' => $newsItem->slug]) }}" 
                                       class="inline-flex items-center text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 font-medium text-sm transition-colors">
                                        {{ __('news.read_more') }}
                                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                        </svg>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full text-center py-12">
                            <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-blue-100 dark:bg-blue-900/30 mb-4">
                                <svg class="h-8 w-8 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                                </svg>
                            </div>
                            <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">
                                {{ __('news.no_articles_title') }}
                            </h3>
                            <p class="text-gray-500 dark:text-gray-300 max-w-md mx-auto">
                                {{ __('news.no_articles') }}
                            </p>
                        </div>
                    @endforelse
                </div>

                <!-- Pagination -->
                @if($news->hasPages())
                    <div class="mt-12">
                        {{ $news->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
