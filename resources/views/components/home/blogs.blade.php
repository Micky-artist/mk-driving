<div class="py-4 sm:py-6 lg:py-8 px-2 sm:px-4 lg:px-8 max-w-7xl mx-auto w-full">
    <div class="text-center mb-10 px-4 fade-in">
        <h2 class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white mb-4">{{ __('home.blogs.title') }}</h2>
        <p class="text-gray-600 dark:text-gray-300 max-w-3xl mx-auto fade-in delay-100">
            {{ __('home.blogs.subtitle') }}
        </p>
    </div>

    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8 px-4 sm:px-6 lg:px-0">
        @forelse($blogs ?? [] as $blog)
            <div class="bg-white dark:bg-gray-800/50 rounded-xl overflow-hidden shadow-md hover:shadow-lg transition-all duration-300 h-full flex flex-col border border-gray-100 dark:border-gray-700/50 transform hover:-translate-y-1 fade-in" 
                 style="animation-delay: {{ $loop->index * 0.1 }}s;">
                <div class="h-48 bg-gray-100 dark:bg-gray-700 overflow-hidden relative group">
                    @if($blog['image'])
                        <img 
                            src="{{ $blog['image'] }}" 
                            alt="{{ $blog['title'] }}" 
                            class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105"
                            onerror="this.onerror=null; this.parentElement.innerHTML='<div class=\'w-full h-full flex items-center justify-center bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/30 dark:to-blue-800/30\'><div class=\'text-center p-4\'><svg xmlns=\'http://www.w3.org/2000/svg\' class=\'h-12 w-12 text-blue-400 dark:text-blue-300 mx-auto mb-2\' fill=\'none\' viewBox=\'0 0 24 24\' stroke=\'currentColor\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'1.5\' d=\'M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z\' /></svg><p class=\'text-sm text-blue-600 dark:text-blue-300\'>No image available</p></div></div>';"
                        >
                    @else
                        <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/30 dark:to-blue-800/30">
                            <div class="text-center p-4">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-blue-400 dark:text-blue-300 mx-auto mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <p class="text-sm text-blue-600 dark:text-blue-300">{{ __('home.blogs.no_image_available') }}</p>
                            </div>
                        </div>
                    @endif
                </div>
                <div class="p-6 flex-1 flex flex-col fade-in delay-100">
                    <div class="flex items-center text-sm text-gray-500 dark:text-gray-400 mb-2">
                        <span>{{ $blog['date'] }}</span>
                        <span class="mx-2">•</span>
                        <span>{{ $blog['read_time'] }}</span>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3">{{ $blog['title'] }}</h3>
                    <p class="text-gray-600 dark:text-gray-300 mb-4 flex-1">{{ $blog['excerpt'] }}</p>
                    <a 
                        href="{{ route('news.show', ['locale' => app()->getLocale(), 'news' => $blog['slug']]) }}" 
                        class="text-[#023047] dark:text-blue-400 font-medium hover:underline flex items-center group transition-all duration-300 hover:text-[#023047]/90 dark:hover:text-blue-300 fade-in delay-200"
                    >
                        {{ __('home.blogs.read_more') }}
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1 group-hover:translate-x-1 transition-transform duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                </div>
            </div>
        @empty
            <div class="col-span-full text-center py-10 fade-in">
                <p class="text-gray-600 dark:text-gray-400">{{ __('home.blogs.no_articles') }}</p>
            </div>
        @endforelse
    </div>

    @if(count($blogs ?? []) > 0)
        <div class="mt-10 text-center fade-in delay-200">
            <a href="{{ route('news.index', app()->getLocale()) }}" class="inline-flex items-center px-6 py-2 border border-transparent text-sm sm:text-base font-medium rounded-full text-white bg-[#023047] hover:bg-[#023047]/90 dark:bg-blue-600 dark:hover:bg-blue-700 transition-all duration-300 transform hover:scale-105">
                {{ __('home.blogs.view_all_articles') }}
            </a>
        </div>
    @endif
</div>
