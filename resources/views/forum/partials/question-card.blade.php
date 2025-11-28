<div class="bg-white dark:bg-gray-800/90 backdrop-blur-sm rounded-2xl shadow-md overflow-hidden hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 border border-gray-100 dark:border-gray-700/50 fade-in group" data-animate>
    <div class="p-6 relative">
        <!-- Decorative accent -->
        <div class="absolute top-0 left-0 w-1 h-full bg-gradient-to-b from-blue-500 to-blue-600 dark:from-blue-400 dark:to-blue-500 rounded-r"></div>
        <div class="flex items-start justify-between pl-4">
            <div class="flex-1">
                <!-- Badges -->
                <div class="flex flex-wrap gap-2 mb-3">
                    @if(isset($question['is_pinned']) && $question['is_pinned'])
                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium bg-yellow-100 dark:bg-yellow-900/40 text-yellow-800 dark:text-yellow-200 border border-yellow-200 dark:border-yellow-800/50">
                            <svg class="w-3.5 h-3.5 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
                            </svg>
                            {{ __('forum.pinned') }}
                        </span>
                    @endif
                </div>
                <a href="{{ route('forum.show', ['locale' => app()->getLocale(), 'id' => $question['id']]) }}" class="block">
                    <h3 class="text-xl font-semibold text-gray-800 dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors duration-200">
                        @if(is_array($question['title']))
                            {{ $question['title'][app()->getLocale()] ?? $question['title']['en'] ?? __('forum.question.title_placeholder') }}
                        @else
                            {{ $question['title'] ?? __('forum.question.title_placeholder') }}
                        @endif
                    </h3>
                </a>
                <p class="mt-3 text-gray-600 dark:text-gray-300 leading-relaxed">
                    @if(is_array($question['content']))
                        {{ Str::limit(strip_tags($question['content'][app()->getLocale()] ?? $question['content']['en'] ?? ''), 150) }}
                    @else
                        {{ Str::limit(strip_tags($question['content'] ?? ''), 150) }}
                    @endif
                </p>
            </div>
            <div class="ml-4 flex-shrink-0 flex flex-col items-end">
                @if(isset($question['answers_count']) && $question['answers_count'] > 0)
                    <span class="inline-flex items-center px-3 py-1.5 rounded-xl text-sm font-medium bg-green-50 dark:bg-green-900/30 text-green-700 dark:text-green-300 border border-green-100 dark:border-green-800/50 transition-all duration-200 shadow-sm">
                        <svg class="w-4 h-4 mr-1.5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                        <span class="font-medium">{{ $question['answers_count'] }}</span>
                        <span class="ml-1">{{ trans_choice('forum.answers', $question['answers_count']) }}</span>
                    </span>
                    @if(isset($question['views']))
                        <div class="mt-2 text-xs text-gray-500 dark:text-gray-400 flex items-center">
                            <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            {{ number_format($question['views']) }} {{ __('forum.views') }}
                        </div>
                    @endif
                @else
                    <span class="inline-flex items-center px-3 py-1.5 rounded-xl text-sm font-medium bg-yellow-50 dark:bg-yellow-900/20 text-yellow-700 dark:text-yellow-300 border border-yellow-100 dark:border-yellow-800/30 transition-all duration-200 shadow-sm">
                        <svg class="w-4 h-4 mr-1.5 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd" />
                        </svg>
                        {{ __('forum.unanswered') }}
                    </span>
                @endif
            </div>
        </div>

        <div class="mt-5 pt-4 border-t border-gray-100 dark:border-gray-700 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div class="flex items-center space-x-3">
                <div class="flex-shrink-0">
                    @php
                        $authorName = ($question['author']['firstName'] ?? '') . ' ' . ($question['author']['lastName'] ?? '');
                        $authorName = trim($authorName) !== '' ? $authorName : __('forum.anonymous');
                        $initials = '';
                        if (!empty(trim($authorName))) {
                            $nameParts = explode(' ', $authorName);
                            $initials = strtoupper(substr($nameParts[0], 0, 1) . (isset($nameParts[1]) ? substr($nameParts[1], 0, 1) : ''));
                        }
                    @endphp
                    <div class="relative group">
                        @if(isset($question['author']['profile_photo_url']))
                            <img class="h-10 w-10 rounded-xl ring-2 ring-white dark:ring-gray-800 transition-all duration-300 group-hover:scale-110 shadow-sm" 
                                 src="{{ $question['author']['profile_photo_url'] }}" 
                                 alt="{{ $authorName }}"
                                 title="{{ $authorName }}"
                                 loading="lazy">
                        @else
                            <div class="h-10 w-10 rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white font-medium text-sm shadow-sm group-hover:scale-110 transition-transform duration-300">
                                {{ $initials }}
                            </div>
                        @endif
                        <span class="absolute -bottom-1 -right-1 block h-3 w-3 rounded-full bg-green-500 ring-2 ring-white dark:ring-gray-800"></span>
                    </div>
                </div>
                <div>
                    <p class="font-medium text-gray-800 dark:text-gray-100">{{ $authorName }}</p>
                    <div class="flex items-center text-sm text-gray-500 dark:text-gray-400">
                        <time datetime="{{ $question['createdAt'] }}" title="{{ $question['createdAt'] }}" class="flex items-center hover:text-gray-700 dark:hover:text-gray-300 transition-colors duration-200">
                            <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            {{ \Carbon\Carbon::parse($question['createdAt'])->diffForHumans() }}
                        </time>
                        @if(isset($question['updatedAt']) && $question['updatedAt'] != $question['createdAt'])
                            <span class="mx-2 text-gray-300 dark:text-gray-600">•</span>
                            <span class="text-xs text-gray-400 dark:text-gray-500" title="{{ __('forum.edited') }} {{ \Carbon\Carbon::parse($question['updatedAt'])->diffForHumans() }}">
                                {{ __('forum.edited') }}
                            </span>
                        @endif
                    </div>
                </div>
            </div>
            @if(isset($question['tags']) && count($question['tags']) > 0)
                <div class="flex flex-wrap gap-2 justify-start sm:justify-end mt-3 sm:mt-0">
                    @foreach(array_slice($question['tags'], 0, 3) as $tag)
                        @php
                            $tagName = is_array($tag) ? ($tag['name'] ?? '') : $tag;
                            $tagSlug = Str::slug($tagName);
                            // Generate a consistent color based on tag name
                            $colors = ['blue', 'indigo', 'purple', 'pink', 'rose', 'teal', 'emerald'];
                            $colorIndex = abs(crc32($tagName)) % count($colors);
                            $color = $colors[$colorIndex];
                        @endphp
                        <a href="{{ route('forum.tag', ['locale' => app()->getLocale(), 'tag' => $tagSlug]) }}" 
                           class="inline-flex items-center px-3 py-1 rounded-lg text-xs font-medium transition-all duration-200
                                  bg-{{ $color }}-50 dark:bg-{{ $color }}-900/30 text-{{ $color }}-700 dark:text-{{ $color }}-300 
                                  hover:bg-{{ $color }}-100 dark:hover:bg-{{ $color }}-900/40 border border-{{ $color }}-100 dark:border-{{ $color }}-800/30">
                            <span class="truncate max-w-[120px] sm:max-w-[150px]">{{ $tagName }}</span>
                        </a>
                    @endforeach
                    @if(count($question['tags']) > 3)
                        <span class="inline-flex items-center px-3 py-1 rounded-lg text-xs font-medium bg-gray-50 dark:bg-gray-700/50 text-gray-500 dark:text-gray-400 border border-gray-100 dark:border-gray-600/50">
                            +{{ count($question['tags']) - 3 }}
                        </span>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>
