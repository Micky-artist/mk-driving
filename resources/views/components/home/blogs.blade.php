<div class="py-16 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto w-full">
    <div class="text-center mb-10 px-4">
        <h2 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-4">{{ __('home.blogs.title') }}</h2>
        <p class="text-gray-600 max-w-3xl mx-auto">
            {{ __('home.blogs.subtitle') }}
        </p>
    </div>

    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8 px-4 sm:px-6 lg:px-0">
        @forelse($blogs ?? [] as $blog)
            <div class="bg-white rounded-xl overflow-hidden shadow-md hover:shadow-lg transition-shadow h-full flex flex-col">
                <div class="h-48 bg-gray-100 overflow-hidden relative">
                    @if($blog['image'])
                        <img 
                            src="{{ $blog['image'] }}" 
                            alt="{{ $blog['title'] }}" 
                            class="w-full h-full object-cover"
                            onerror="this.onerror=null; this.parentElement.innerHTML='<div class=\'w-full h-full flex items-center justify-center bg-gradient-to-br from-blue-50 to-blue-100\'><div class=\'text-center p-4\'><svg xmlns=\'http://www.w3.org/2000/svg\' class=\'h-12 w-12 text-blue-400 mx-auto mb-2\' fill=\'none\' viewBox=\'0 0 24 24\' stroke=\'currentColor\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'1.5\' d=\'M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z\' /></svg><p class=\'text-sm text-blue-600\'>No image available</p></div></div>';"
                        >
                    @else
                        <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-blue-50 to-blue-100">
                            <div class="text-center p-4">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-blue-400 mx-auto mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <p class="text-sm text-blue-600">{{ __('home.blogs.no_image_available') }}</p>
                            </div>
                        </div>
                    @endif
                </div>
                <div class="p-6">
                    <div class="flex items-center text-sm text-gray-500 mb-2">
                        <span>{{ $blog['date'] }}</span>
                        <span class="mx-2">•</span>
                        <span>{{ $blog['read_time'] }}</span>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">{{ $blog['title'] }}</h3>
                    <p class="text-gray-600 mb-4">{{ $blog['excerpt'] }}</p>
                    <a 
                        href="{{ route('news.show', ['locale' => app()->getLocale(), 'slug' => $blog['slug']]) }}" 
                        class="text-[#023047] font-medium hover:underline flex items-center"
                    >
                        {{ __('home.blogs.read_more') }}
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                </div>
            </div>
        @empty
            <div class="col-span-3 text-center py-10">
                <p class="text-gray-500">{{ __('home.blogs.no_articles') }}</p>
            </div>
        @endforelse
    </div>

    @if(count($blogs ?? []) > 0)
        <div class="mt-10 text-center px-4">
            <a 
                href="{{ route('news.index', ['locale' => app()->getLocale()]) }}" 
                class="inline-block bg-[#023047] hover:bg-[#023047]/90 text-white font-medium py-2 px-6 rounded-full transition-colors"
            >
                {{ __('home.blogs.view_all') }}
            </a>
        </div>
    @endif
</div>
