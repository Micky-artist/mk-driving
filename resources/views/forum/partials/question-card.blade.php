<div class="bg-white dark:bg-gray-800 rounded-xl shadow-md overflow-hidden hover:shadow-lg transition-all duration-300 transform hover:-translate-y-0.5 fade-in" data-animate>
    <div class="p-6">
        <div class="flex items-start justify-between">
            <div class="flex-1">
                <a href="{{ route('forum.show', ['locale' => app()->getLocale(), 'id' => $question['id']]) }}" class="block group">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors duration-200">
                        @if(is_array($question['title']))
                            {{ $question['title'][app()->getLocale()] ?? $question['title']['en'] ?? __('forum.question.title_placeholder') }}
                        @else
                            {{ $question['title'] ?? __('forum.question.title_placeholder') }}
                        @endif
                    </h3>
                </a>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">
                    @if(is_array($question['content']))
                        {{ Str::limit(strip_tags($question['content'][app()->getLocale()] ?? $question['content']['en'] ?? ''), 150) }}
                    @else
                        {{ Str::limit(strip_tags($question['content'] ?? ''), 150) }}
                    @endif
                </p>
            </div>
            <div class="ml-4 flex-shrink-0">
                @if(isset($question['answers_count']) && $question['answers_count'] > 0)
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900/50 text-green-800 dark:text-green-200 border border-green-200 dark:border-green-800 transition-all duration-200">
                        <svg class="w-3.5 h-3.5 mr-1.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                        {{ trans_choice('forum.answers', $question['answers_count'], ['count' => $question['answers_count']]) }}
                    </span>
                @else
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-200 border border-yellow-200 dark:border-yellow-800/50 transition-all duration-200">
                        <svg class="w-3.5 h-3.5 mr-1.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd" />
                        </svg>
                        {{ __('forum.unanswered') }}
                    </span>
                @endif
            </div>
        </div>

        <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-700 flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="flex-shrink-0 group relative">
                    @php
                        $authorName = ($question['author']['firstName'] ?? '') . ' ' . ($question['author']['lastName'] ?? '');
                        $authorName = trim($authorName) !== '' ? $authorName : __('forum.anonymous');
                    @endphp
                    <div class="relative group">
                        <img class="h-9 w-9 rounded-full ring-2 ring-white dark:ring-gray-700 transition-transform duration-300 group-hover:scale-110" 
                             src="{{ $question['author']['profile_photo_url'] ?? 'https://ui-avatars.com/api/?name='.urlencode($authorName).'&background=3b82f6&color=fff' }}" 
                             alt="{{ $authorName }}"
                             title="{{ $authorName }}"
                             loading="lazy">
                        <span class="absolute bottom-0 right-0 block h-2.5 w-2.5 rounded-full bg-green-500 ring-2 ring-white dark:ring-gray-800"></span>
                    </div>
                </div>
                <div class="text-sm">
                    <p class="font-medium text-gray-900 dark:text-gray-100">{{ $authorName }}</p>
                    <div class="text-gray-500 dark:text-gray-400">
                        <time datetime="{{ $question['createdAt'] }}" title="{{ $question['createdAt'] }}" class="hover:text-gray-700 dark:hover:text-gray-300 transition-colors duration-200">
                            {{ \Carbon\Carbon::parse($question['createdAt'])->diffForHumans() }}
                        </time>
                    </div>
                </div>
            </div>
            @if(isset($question['tags']) && count($question['tags']) > 0)
                <div class="flex flex-wrap gap-2 justify-end">
                    @foreach(array_slice($question['tags'], 0, 3) as $tag)
                        @php
                            $tagName = is_array($tag) ? ($tag['name'] ?? '') : $tag;
                            $tagSlug = Str::slug($tagName);
                        @endphp
                        <a href="{{ route('forum.tag', ['locale' => app()->getLocale(), 'tag' => $tagSlug]) }}" 
                           class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 hover:bg-gray-200 transition-colors duration-200">
                            {{ $tagName }}
                        </a>
                    @endforeach
                    @if(count($question['tags']) > 3)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-500">
                            +{{ count($question['tags']) - 3 }}
                        </span>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>
