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
        <div class="max-w-4xl mx-auto px-3 sm:px-4 lg:px-16">
            <div class="py-8">
                <!-- Back Button -->
                <div class="mb-6">
                    <a href="{{ route('news.index', ['locale' => app()->getLocale()]) }}" 
                       class="inline-flex items-center text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white transition-colors">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                        {{ __('news.back_to_news') }}
                    </a>
                </div>

                <!-- Article Header -->
                <article class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <!-- Featured Image -->
                    @if($news->featured_image)
                        <div class="w-full h-64 md:h-96">
                            <img src="{{ asset('storage/' . $news->featured_image) }}" 
                                 alt="{{ $news->localized_title }}"
                                 class="w-full h-full object-cover">
                        </div>
                    @else
                        <div class="w-full h-64 md:h-96 bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center">
                            <svg class="w-24 h-24 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                            </svg>
                        </div>
                    @endif

                    <!-- Article Content -->
                    <div class="p-6 md:p-8">
                        <!-- Article Meta -->
                        <div class="flex items-center justify-between mb-6">
                            <div class="flex items-center space-x-4">
                                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-orange-500 to-orange-600 flex items-center justify-center text-white font-bold">
                                    {{ substr($news->user->firstName ?? 'A', 0, 1) }}{{ substr($news->user->lastName ?? 'dmin', 0, 1) }}
                                </div>
                                <div>
                                    <div class="font-medium text-gray-900 dark:text-white">
                                        {{ $news->user->firstName ?? 'Admin' }} {{ $news->user->lastName ?? '' }}
                                    </div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400">
                                        {{ $news->published_at->format('F j, Y') }}
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center space-x-4 text-sm text-gray-500 dark:text-gray-400">
                                <span class="flex items-center space-x-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    <span>{{ $news->views ?? 0 }} {{ __('news.views') }}</span>
                                </span>
                                <span class="flex items-center space-x-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                    </svg>
                                    <span>{{ $news->comments_count ?? 0 }} {{ __('news.comments') }}</span>
                                </span>
                            </div>
                        </div>

                        <!-- Article Title -->
                        <h1 class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-white mb-6">
                            {{ $news->localized_title }}
                        </h1>

                        <!-- Article Excerpt -->
                        @if($news->excerpt)
                            <div class="text-lg text-gray-600 dark:text-gray-300 mb-8 italic border-l-4 border-blue-500 pl-4">
                                {{ $news->excerpt }}
                            </div>
                        @endif

                        <!-- Article Body -->
                        <div class="prose prose-lg dark:prose-invert max-w-none">
                            {!! $news->localized_content !!}
                        </div>

                        <!-- Article Footer -->
                        <div class="mt-12 pt-8 border-t border-gray-200 dark:border-gray-600">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-4">
                                    <button class="flex items-center space-x-2 text-gray-600 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                        </svg>
                                        <span>{{ __('news.like') }}</span>
                                    </button>
                                    <button class="flex items-center space-x-2 text-gray-600 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m9.032 4.026a9.001 9.001 0 01-7.432 0m9.032-4.026A9.001 9.001 0 0112 3c-4.474 0-8.268 2.943-9.543 7a9.001 9.001 0 009.543 7 9.001 9.001 0 009.543-7z" />
                                        </svg>
                                        <span>{{ __('news.share') }}</span>
                                    </button>
                                </div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">
                                    {{ __('news.last_updated') }}: {{ $news->updated_at->format('M j, Y \a\t g:i A') }}
                                </div>
                            </div>
                        </div>
                    </div>
                </article>

                <!-- Related Articles -->
                @if(isset($relatedNews) && $relatedNews->count() > 0)
                    <div class="mt-12">
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">
                            {{ __('news.related_articles') }}
                        </h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            @foreach($relatedNews->take(2) as $relatedItem)
                                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 hover:shadow-md transition-all duration-300">
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">
                                        <a href="{{ route('news.show', ['locale' => app()->getLocale(), 'news' => $relatedItem->slug]) }}" 
                                           class="hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                                            {{ $relatedItem->localized_title }}
                                        </a>
                                    </h3>
                                    <p class="text-gray-600 dark:text-gray-300 text-sm mb-3 line-clamp-2">
                                        {{ Str::limit(strip_tags($relatedItem->excerpt), 100) }}
                                    </p>
                                    <span class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ $relatedItem->published_at->format('M j, Y') }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
