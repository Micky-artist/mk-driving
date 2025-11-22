<div class="bg-white rounded-lg shadow overflow-hidden hover:shadow-md transition-shadow duration-200">
    <div class="p-6">
        <div class="flex items-start justify-between">
            <div class="flex-1">
                <a href="{{ route('forum.show', ['locale' => app()->getLocale(), 'id' => $question['id']]) }}" class="block">
                    <h3 class="text-lg font-medium text-gray-900 hover:text-blue-600 transition-colors duration-200">
                        @if(is_array($question['title']))
                            {{ $question['title'][app()->getLocale()] ?? $question['title']['en'] ?? __('forum.question.title_placeholder') }}
                        @else
                            {{ $question['title'] ?? __('forum.question.title_placeholder') }}
                        @endif
                    </h3>
                </a>
                <p class="mt-1 text-sm text-gray-500">
                    @if(is_array($question['content']))
                        {{ Str::limit(strip_tags($question['content'][app()->getLocale()] ?? $question['content']['en'] ?? ''), 150) }}
                    @else
                        {{ Str::limit(strip_tags($question['content'] ?? ''), 150) }}
                    @endif
                </p>
            </div>
            <div class="ml-4 flex-shrink-0">
                @if(isset($question['answers_count']) && $question['answers_count'] > 0)
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                        {{ trans_choice('forum.answers', $question['answers_count'], ['count' => $question['answers_count']]) }}
                    </span>
                @else
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                        {{ __('forum.unanswered') }}
                    </span>
                @endif
            </div>
        </div>

        <div class="mt-4 flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="flex-shrink-0">
                    @php
                        $authorName = ($question['author']['firstName'] ?? '') . ' ' . ($question['author']['lastName'] ?? '');
                        $authorName = trim($authorName) !== '' ? $authorName : __('forum.anonymous');
                    @endphp
                    <img class="h-8 w-8 rounded-full" 
                         src="{{ $question['author']['profile_photo_url'] ?? 'https://ui-avatars.com/api/?name='.urlencode($authorName) }}" 
                         alt="{{ $authorName }}"
                         title="{{ $authorName }}">
                </div>
                <div class="text-sm">
                    <p class="text-gray-900">{{ $authorName }}</p>
                    <div class="text-gray-500">
                        <time datetime="{{ $question['createdAt'] }}" title="{{ $question['createdAt'] }}">
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
