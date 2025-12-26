@push('styles')
<style>
    .carousel-container {
        scroll-behavior: smooth;
        -webkit-overflow-scrolling: touch;
    }
    
    .carousel-item {
        scroll-snap-align: start;
        flex: 0 0 auto;
    }
    
    .carousel-container {
        scroll-snap-type: x mandatory;
    }
    
    @media (min-width: 768px) {
        .carousel-item {
            min-width: 600px;
        }
    }
    
    @media (max-width: 767px) {
        .carousel-item {
            min-width: 85vw;
        }
    }
    
    .carousel-scrollbar::-webkit-scrollbar {
        height: 6px;
    }
    
    .carousel-scrollbar::-webkit-scrollbar-track {
        background: rgba(156, 163, 175, 0.1);
        border-radius: 3px;
    }
    
    .carousel-scrollbar::-webkit-scrollbar-thumb {
        background: rgba(59, 130, 246, 0.5);
        border-radius: 3px;
    }
    
    .carousel-scrollbar::-webkit-scrollbar-thumb:hover {
        background: rgba(59, 130, 246, 0.7);
    }
    
    .dark .carousel-scrollbar::-webkit-scrollbar-track {
        background: rgba(75, 85, 99, 0.3);
    }
    
    .dark .carousel-scrollbar::-webkit-scrollbar-thumb {
        background: rgba(59, 130, 246, 0.4);
    }
    
    .dark .carousel-scrollbar::-webkit-scrollbar-thumb:hover {
        background: rgba(59, 130, 246, 0.6);
    }
</style>
@endpush

<div class="relative py-4 mx-4 md:mx-8 bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-900 dark:to-gray-800 overflow-hidden">
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Section Header -->
        <div class="relative text-center mb-8 fade-in">
            <!-- Decorative elements -->
            <div class="absolute -top-4 -left-4 w-24 h-24 bg-emerald-100 dark:bg-emerald-900/30 rounded-full mix-blend-multiply filter blur-xl opacity-70 dark:opacity-30 animate-blob"></div>
            <div class="absolute -bottom-4 -right-4 w-24 h-24 bg-purple-100 dark:bg-purple-900/30 rounded-full mix-blend-multiply filter blur-xl opacity-70 dark:opacity-30 animate-blob animation-delay-2000"></div>
            
            <div class="relative">
                <span class="inline-block px-3 py-1 text-sm font-semibold text-blue-800 dark:text-blue-100 bg-blue-100 dark:bg-blue-900/70 rounded-full mb-4">
                    {{ __('forum.community') }}
                </span>
                <h2 class="text-3xl md:text-4xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-blue-600 to-orange-500 dark:from-blue-400 dark:to-orange-400 mb-3">
                    {{ __('forum.recent_questions') }}
                </h2>
                <div class="w-24 h-1.5 bg-gradient-to-r from-emerald-400 to-teal-400 mx-auto mb-4 rounded-full relative">
                    <div class="absolute -bottom-1 left-1/2 -translate-x-1/2 w-2 h-2 rounded-full bg-teal-500 dark:bg-teal-400"></div>
                </div>
                <p class="text-lg text-gray-600 dark:text-gray-300 max-w-3xl mx-auto fade-in delay-100 relative">
                    <span class="relative z-10">{{ __('forum.recent_questions_subtitle') }}</span>
                </p>
            </div>
        </div>

        @if(count($questions) > 0)
            <!-- Carousel Navigation Buttons -->
            <div class="flex justify-between items-center mb-6">
                <button id="prev-btn" class="p-2 rounded-full bg-white dark:bg-gray-800 shadow-lg border border-gray-200 dark:border-gray-700 hover:shadow-md transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed">
                    <svg class="w-5 h-5 text-gray-600 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                </button>
                <div class="text-sm text-gray-500 dark:text-gray-400">
                    <span id="current-index">1</span> / <span id="total-count">{{ count($questions) }}</span>
                </div>
                <button id="next-btn" class="p-2 rounded-full bg-white dark:bg-gray-800 shadow-lg border border-gray-200 dark:border-gray-700 hover:shadow-md transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed">
                    <svg class="w-5 h-5 text-gray-600 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </button>
            </div>

            <!-- Carousel Container -->
            <div class="relative">
                <div id="carousel" class="carousel-container carousel-scrollbar flex gap-6 overflow-x-auto pb-4" x-data="forumCarousel()">
                    @foreach($questions as $index => $question)
                        <div class="carousel-item" data-index="{{ $index }}">
                            <div class="bg-white dark:bg-gray-800/80 backdrop-blur-sm rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 border border-gray-100 dark:border-gray-700/50 overflow-hidden w-full">
                                <!-- Question Header -->
                                <div class="p-6 border-b border-gray-100 dark:border-gray-700/50">
                                    <!-- Topic Badge -->
                                    <div class="flex items-start justify-between mb-4">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gradient-to-r from-blue-100 to-indigo-100 text-blue-800 dark:from-blue-900/50 dark:to-indigo-900/50 dark:text-blue-200">
                                            {{ $question['topics'][0] ?? __('forum.general') }}
                                        </span>
                                        <div class="flex items-center space-x-2 text-xs text-gray-500 dark:text-gray-400">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            <span>{{ timeDiffForHumans($question['created_at']) }}</span>
                                        </div>
                                    </div>

                                    <!-- Question Title and User -->
                                    <div class="mb-4">
                                        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                                            <a href="{{ route('forum.show', ['locale' => app()->getLocale(), 'id' => $question['id']]) }}" class="focus:outline-none">
                                                {{ $question['title'] }}
                                            </a>
                                        </h3>
                                        <div class="flex items-center space-x-3">
                                            <div class="w-8 h-8 rounded-full bg-gradient-to-r from-blue-500 to-blue-600 flex items-center justify-center text-white text-xs font-bold">
                                                {{ substr($question['user']['firstName'] ?? '?', 0, 1) }}{{ substr($question['user']['lastName'] ?? '', 0, 1) }}
                                            </div>
                                            <div>
                                                <div class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                                    {{ $question['user']['firstName'] ?? 'Anonymous' }} {{ $question['user']['lastName'] ?? 'User' }}
                                                </div>
                                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                                    {{ __('forum.original_poster') }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Question Content -->
                                    <p class="text-gray-600 dark:text-gray-300 line-clamp-3">
                                        {{ Str::limit(strip_tags($question['content']), 300) }}
                                    </p>
                                </div>

                                <!-- Answers Section -->
                                @if(isset($question['answers']) && count($question['answers']) > 0)
                                    <div class="p-6 space-y-4 max-h-96 overflow-y-auto">
                                        <div class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                                            {{ count($question['answers']) }} {{ count($question['answers']) == 1 ? 'Reply' : 'Replies' }}
                                        </div>
                                        @foreach($question['answers'] as $answer)
                                            <div class="bg-gray-50 dark:bg-gray-800/50 rounded-xl p-4 border-l-4 border-blue-500">
                                                <!-- Answer Header -->
                                                <div class="flex items-start space-x-3 mb-3">
                                                    <div class="w-6 h-6 rounded-full bg-gradient-to-br from-green-500 to-green-600 flex items-center justify-center text-white text-xs font-bold flex-shrink-0">
                                                        {{ substr($answer['user']['firstName'] ?? '?', 0, 1) }}{{ substr($answer['user']['lastName'] ?? '', 0, 1) }}
                                                    </div>
                                                    <div class="flex-1 min-w-0">
                                                        <div class="flex items-center space-x-2 mb-2">
                                                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                                                {{ $answer['user']['firstName'] ?? 'Anonymous' }} {{ $answer['user']['lastName'] ?? 'User' }}
                                                            </span>
                                                            <span class="text-xs text-gray-500 dark:text-gray-400">
                                                                • {{ timeDiffForHumans($answer['created_at']) }}
                                                            </span>
                                                        </div>
                                                        <p class="text-sm text-gray-600 dark:text-gray-300 line-clamp-3">
                                                            {{ Str::limit(strip_tags($answer['content']), 200) }}
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="p-6 text-center">
                                        <div class="text-gray-500 dark:text-gray-400 text-sm">
                                            {{ __('forum.no_replies_yet') }}
                                        </div>
                                    </div>
                                @endif

                                <!-- Action Footer -->
                                <div class="p-6 border-t border-gray-100 dark:border-gray-700/50">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center space-x-4">
                                            <!-- Reply Count -->
                                            <div class="flex items-center space-x-1 text-gray-500 dark:text-gray-400">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                                </svg>
                                                <span class="text-sm">{{ isset($question['answers']) ? count($question['answers']) : 0 }} {{ isset($question['answers']) && count($question['answers']) == 1 ? 'Reply' : 'Replies' }}</span>
                                            </div>
                                        </div>
                                        
                                        <!-- View Full Discussion -->
                                        <a href="{{ route('forum.show', ['locale' => app()->getLocale(), 'id' => $question['id']]) }}" 
                                           class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition-colors">
                                            {{ __('forum.view_full_discussion') }}
                                            <svg class="ml-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                            </svg>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- View All Button -->
            <div class="mt-12 text-center">
                <div class="relative inline-flex group">
                    <div class="absolute -inset-0.5 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-full opacity-70 group-hover:opacity-100 blur transition duration-1000 group-hover:duration-300"></div>
                    <a href="{{ route('forum.index', app()->getLocale()) }}" 
                       class="relative flex items-center px-8 py-3 bg-gradient-to-r from-blue-600 to-indigo-700 text-white text-sm font-medium rounded-full hover:from-blue-700 hover:to-indigo-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200">
                        {{ __('forum.view_all_questions') }}
                        <svg class="ml-2 -mr-1 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                        </svg>
                    </a>
                </div>
            </div>
        @else
            <!-- Empty State -->
            <div class="text-center py-16 px-6 bg-white dark:bg-gray-800/50 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-700/50">
                <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400">
                    <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                    </svg>
                </div>
                <h3 class="mt-4 text-xl font-medium text-gray-900 dark:text-white">
                    {{ __('forum.no_questions_yet') }}
                </h3>
                <p class="mt-2 text-gray-600 dark:text-gray-300 max-w-md mx-auto">
                    {{ __('forum.be_the_first_to_ask') }}
                </p>
                <div class="mt-6">
                    <a href="{{ route('forum.create', app()->getLocale()) }}" 
                       class="inline-flex items-center px-6 py-2.5 bg-gradient-to-r from-blue-600 to-indigo-700 text-white text-sm font-medium rounded-full hover:from-blue-700 hover:to-indigo-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200">
                        <svg class="-ml-0.5 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                        {{ __('forum.ask_question') }}
                    </a>
                </div>
            </div>
        @endif
    </div>
    
    <!-- Decorative elements -->
    <div class="absolute top-0 right-0 -mr-32 -mt-8 opacity-30">
        <div class="w-64 h-64 rounded-full bg-gradient-to-r from-blue-200 to-indigo-300 dark:from-blue-800/30 dark:to-indigo-900/30 blur-3xl"></div>
    </div>
    <div class="absolute bottom-0 left-0 -ml-32 -mb-8 opacity-30">
        <div class="w-64 h-64 rounded-full bg-gradient-to-r from-indigo-200 to-purple-300 dark:from-indigo-800/30 dark:to-purple-900/30 blur-3xl"></div>
    </div>
</div>

@push('scripts')
<script>
function forumCarousel() {
    return {
        currentIndex: 0,
        totalItems: {{ count($questions) }},
        carousel: null,
        
        init() {
            this.carousel = document.getElementById('carousel');
            this.updateButtons();
            this.updateCounter();
            
            // Add scroll event listener
            this.carousel.addEventListener('scroll', () => {
                this.updateCurrentIndex();
            });
            
            // Navigation buttons
            document.getElementById('prev-btn').addEventListener('click', () => {
                this.prev();
            });
            
            document.getElementById('next-btn').addEventListener('click', () => {
                this.next();
            });
            
            // Touch/swipe support for mobile
            let startX = 0;
            let scrollLeft = 0;
            
            this.carousel.addEventListener('touchstart', (e) => {
                startX = e.touches[0].pageX - this.carousel.offsetLeft;
                scrollLeft = this.carousel.scrollLeft;
            });
            
            this.carousel.addEventListener('touchmove', (e) => {
                if (!startX) return;
                const x = e.touches[0].pageX - this.carousel.offsetLeft;
                const walk = (x - startX) * 2;
                this.carousel.scrollLeft = scrollLeft - walk;
            });
        },
        
        prev() {
            if (this.currentIndex > 0) {
                this.currentIndex--;
                this.scrollToItem(this.currentIndex);
            }
        },
        
        next() {
            if (this.currentIndex < this.totalItems - 1) {
                this.currentIndex++;
                this.scrollToItem(this.currentIndex);
            }
        },
        
        scrollToItem(index) {
            const items = this.carousel.querySelectorAll('.carousel-item');
            if (items[index]) {
                items[index].scrollIntoView({
                    behavior: 'smooth',
                    inline: 'start',
                    block: 'nearest'
                });
            }
        },
        
        updateCurrentIndex() {
            const items = this.carousel.querySelectorAll('.carousel-item');
            const carouselRect = this.carousel.getBoundingClientRect();
            
            items.forEach((item, index) => {
                const itemRect = item.getBoundingClientRect();
                const itemCenter = itemRect.left + itemRect.width / 2;
                const carouselCenter = carouselRect.left + carouselRect.width / 2;
                
                if (Math.abs(itemCenter - carouselCenter) < itemRect.width / 2) {
                    this.currentIndex = index;
                    this.updateButtons();
                    this.updateCounter();
                }
            });
        },
        
        updateButtons() {
            const prevBtn = document.getElementById('prev-btn');
            const nextBtn = document.getElementById('next-btn');
            
            prevBtn.disabled = this.currentIndex === 0;
            nextBtn.disabled = this.currentIndex === this.totalItems - 1;
        },
        
        updateCounter() {
            document.getElementById('current-index').textContent = this.currentIndex + 1;
        }
    }
}
</script>
@endpush
