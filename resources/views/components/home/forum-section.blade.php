@push('styles')
<style>
    @keyframes blob {
        0% {
            transform: translate(0px, 0px) scale(1);
        }
        33% {
            transform: translate(30px, -20px) scale(1.1);
        }
        66% {
            transform: translate(-20px, 20px) scale(0.9);
        }
        100% {
            transform: translate(0px, 0px) scale(1);
        }
    }
    .animate-blob {
        animation: blob 7s infinite;
    }
    .animation-delay-2000 {
        animation-delay: 2s;
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
            <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3 max-w-7xl mx-auto">
                @foreach($questions as $index => $question)
                    <div 
                        class="group relative bg-white dark:bg-gray-800/80 backdrop-blur-sm rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1 border border-gray-100 dark:border-gray-700/50 overflow-hidden h-full flex flex-col fade-in"
                        style="animation-delay: {{ $index * 0.1 }}s;"
                    >
                        <!-- Topic Badge -->
                        <div class="absolute top-4 right-4 z-10">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gradient-to-r from-blue-100 to-indigo-100 text-blue-800 dark:from-blue-900/50 dark:to-indigo-900/50 dark:text-blue-200">
                                {{ $question['topics'][0] ?? __('forum.general') }}
                            </span>
                        </div>

                        <div class="p-6 pb-4 flex-1 flex flex-col">
                            <!-- Question Title -->
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">
                                <a href="{{ route('forum.show', ['locale' => app()->getLocale(), 'question' => $question['id']]) }}" class="focus:outline-none">
                                    <span class="absolute inset-0" aria-hidden="true"></span>
                                    {{ $question['title'] }}
                                </a>
                            </h3>

                            <!-- Question Excerpt -->
                            <p class="text-gray-600 dark:text-gray-300 mb-4 line-clamp-3">
                                {{ Str::limit(strip_tags($question['content']), 120) }}
                            </p>

                            <!-- Top Answer Preview -->
                            @if($question['top_answer'])
                                <div class="mt-auto">
                                    <div class="relative bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-gray-800/50 dark:to-gray-800/30 rounded-xl p-4 border-l-4 border-blue-500 mb-4 overflow-hidden">
                                        <div class="absolute inset-0 bg-gradient-to-br from-blue-100/30 to-transparent opacity-30 dark:opacity-10"></div>
                                        <div class="relative">
                                            <div class="flex items-start">
                                                <div class="flex-shrink-0">
                                                    <div class="flex items-center justify-center h-8 w-8 rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900/50 dark:text-blue-200 text-sm font-medium">
                                                        ✓
                                                    </div>
                                                </div>
                                                <div class="ml-3">
                                                    <p class="text-sm text-gray-700 dark:text-gray-300 line-clamp-2">
                                                        {{ Str::limit(strip_tags($question['top_answer']['content']), 120) }}
                                                    </p>
                                                    <div class="mt-2 text-xs text-blue-600 dark:text-blue-400 font-medium">
                                                        — {{ $question['top_answer']['user']->name }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <!-- Meta Information -->
                            <div class="mt-auto pt-4 border-t border-gray-100 dark:border-gray-700/50">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-3">
                                        <div class="flex-shrink-0">
                                            <div class="h-8 w-8 rounded-full bg-gradient-to-r from-blue-500 to-indigo-600 flex items-center justify-center text-white text-xs font-medium">
                                                {{ substr($question['user']->name, 0, 1) }}
                                            </div>
                                        </div>
                                        <div class="text-sm text-gray-600 dark:text-gray-300">
                                            {{ $question['user']->name }}
                                        </div>
                                    </div>
                                    <div class="flex items-center space-x-4">
                                        <span class="inline-flex items-center text-sm text-gray-500 dark:text-gray-400">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                                            </svg>
                                            {{ $question['answers_count'] }}
                                        </span>
                                        <span class="text-xs text-gray-400">
                                            {{ $question['created_at']->diffForHumans() }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
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
