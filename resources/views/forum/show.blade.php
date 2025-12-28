@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="mb-6">
            <a href="{{ route('forum.index') }}" class="text-blue-600 hover:text-blue-800 flex items-center">
                <svg class="h-5 w-5 mr-1" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                </svg>
                {{ __('Back to Forum') }}
            </a>
        </div>

        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <!-- Question Header -->
            <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                <div class="flex items-start space-x-4">
                    <!-- Voting Column -->
                    <div class="flex flex-col items-center space-y-2 flex-shrink-0">
                        <button
                            id="question-up-{{ $question->id }}"
                            onclick="vote('question', {{ $question->id }}, 'up')"
                            class="w-8 h-8 rounded hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center justify-center transition-colors">
                            <svg class="w-5 h-5 text-gray-400 hover:text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M3.293 9.707a1 1 0 010-1.414l6-6a1 1 0 011.414 0l6 6a1 1 0 01-1.414 1.414L11 5.414V17a1 1 0 11-2 0V5.414L4.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                            </svg>
                        </button>
                        <span id="question-score-{{ $question->id }}" class="text-sm font-medium text-gray-600 dark:text-gray-300">{{ $question->votes ?? 0 }}</span>
                        <button
                            id="question-down-{{ $question->id }}"
                            onclick="vote('question', {{ $question->id }}, 'down')"
                            class="w-8 h-8 rounded hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center justify-center transition-colors">
                            <svg class="w-5 h-5 text-gray-400 hover:text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 10.293a1 1 0 010 1.414l-6 6a1 1 0 01-1.414 0l-6-6a1 1 0 111.414-1.414L9 14.586V3a1 1 0 012 0v11.586l4.293-4.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>
                    
                    <!-- Content Column -->
                    <div class="flex-1 min-w-0">
                        <h1 class="text-2xl font-bold text-gray-900">
                            @if(is_array($question->title))
                                {{ $question->title[app()->getLocale()] ?? $question->title['en'] ?? 'No title' }}
                            @else
                                {{ $question->title }}
                            @endif
                        </h1>
                        <div class="mt-1 flex flex-col sm:flex-row sm:flex-wrap sm:mt-0 sm:space-x-6">
                            <div class="mt-2 flex items-center text-sm text-gray-500">
                                <svg class="flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                                </svg>
                                {{ $question->user->name ?? 'Anonymous' }}
                            </div>
                            <div class="mt-2 flex items-center text-sm text-gray-500">
                                <svg class="flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd" />
                                </svg>
                                <time datetime="{{ $question->created_at->toDateTimeString() }}" title="{{ $question->created_at->format('F j, Y, g:i a') }}">
                                    @php
                                        $diff = $question->created_at->diffInSeconds(now());
                                        if ($diff < 60) {
                                            echo trans_choice('time.second_ago', $diff, ['count' => $diff]);
                                        } elseif ($diff < 3600) {
                                            $minutes = floor($diff / 60);
                                            echo trans_choice('time.minute_ago', $minutes, ['count' => $minutes]);
                                        } elseif ($diff < 86400) {
                                            $hours = floor($diff / 3600);
                                            echo trans_choice('time.hour_ago', $hours, ['count' => $hours]);
                                        } else {
                                            $days = floor($diff / 86400);
                                            echo trans_choice('time.day_ago', $days, ['count' => $days]);
                                        }
                                    @endphp
                                </time>
                            </div>
                            @if(isset($question->tags) && $question->tags->isNotEmpty())
                            <div class="mt-2 flex items-center text-sm text-gray-500">
                                <svg class="flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M17.707 9.293a1 1 0 010 1.414l-7 7a1 1 0 01-1.414 0l-7-7A.997.997 0 012 10V5a3 3 0 013-3h5c.256 0 .512.098.707.293l7 7zM5 6a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
                                </svg>
                                @foreach($question->tags as $tag)
                                    <span class="mr-1">{{ $tag->name }}</span>
                                @endforeach
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Question Content -->
            <div class="px-4 py-5 sm:p-6">
                <div class="prose max-w-none">
                    @if(is_array($question->content))
                        {!! $question->content[app()->getLocale()] ?? $question->content['en'] ?? 'No content available' !!}
                    @else
                        {!! $question->content !!}
                    @endif
                </div>
            </div>

            <!-- Answers Section -->
            <div class="border-t border-gray-200 px-4 py-5 sm:px-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">
                    {{ $question->answers_count ?? 0 }} {{ Str::plural('Answer', $question->answers_count ?? 0) }}
                </h3>

                @if(isset($question->answers) && $question->answers->count() > 0)
                    @foreach($question->answers as $answer)
                        <div class="border-b border-gray-200 py-6">
                            <div class="flex items-start space-x-4">
                                <!-- Answer Voting -->
                                <div class="flex flex-col items-center space-y-2 flex-shrink-0">
                                    <button
                                        id="answer-up-{{ $answer->id }}"
                                        onclick="vote('answer', {{ $answer->id }}, 'up')"
                                        class="w-8 h-8 rounded hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center justify-center transition-colors">
                                        <svg class="w-5 h-5 text-gray-400 hover:text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M3.293 9.707a1 1 0 010-1.414l6-6a1 1 0 011.414 0l6 6a1 1 0 01-1.414 1.414L11 5.414V17a1 1 0 11-2 0V5.414L4.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                    <span id="answer-score-{{ $answer->id }}" class="text-sm font-medium text-gray-600 dark:text-gray-300">{{ $answer->votes ?? 0 }}</span>
                                    <button
                                        id="answer-down-{{ $answer->id }}"
                                        onclick="vote('answer', {{ $answer->id }}, 'down')"
                                        class="w-8 h-8 rounded hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center justify-center transition-colors">
                                        <svg class="w-5 h-5 text-gray-400 hover:text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 10.293a1 1 0 010 1.414l-6 6a1 1 0 01-1.414 0l-6-6a1 1 0 111.414-1.414L9 14.586V3a1 1 0 012 0v11.586l4.293-4.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                </div>
                                
                                <!-- Answer Content -->
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center space-x-3 mb-2">
                                        <div class="flex-shrink-0 mr-3">
                                            <img class="h-10 w-10 rounded-full" 
                                                 src="{{ $answer->user->profile_photo_url ?? 'https://ui-avatars.com/api/?name='.urlencode($answer->user->name) }}" 
                                                 alt="{{ $answer->user->name }}">
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-900">
                                                {{ $answer->user->name }}
                                                @if(isset($answer->user->is_admin) && $answer->user->is_admin)
                                                    <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                        Admin
                                                    </span>
                                                @endif
                                            </p>
                                            <p class="text-sm text-gray-500">
                                                <time datetime="{{ $answer->created_at->toDateTimeString() }}">
                                                    {{ timeDiffForHumans($answer->created_at) }}
                                                </time>
                                            </p>
                                        </div>
                                    </div>
                                    <div class="prose max-w-none">
                                        @if(is_array($answer->content))
                                            {!! $answer->content[app()->getLocale()] ?? $answer->content['en'] ?? 'No content available' !!}
                                        @else
                                            {!! $answer->content !!}
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <p class="text-gray-500">{{ __('No answers yet. Be the first to answer!') }}</p>
                @endif
            </div>

            <!-- Answer Form -->
            @auth
                <div class="bg-gray-50 px-4 py-5 sm:px-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('Your Answer') }}</h3>
                    <form action="{{ route('forum.answers.store', $question) }}" method="POST">
                        @csrf
                        <div class="mt-1">
                            <textarea id="content" name="content" rows="5" 
                                class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md"
                                placeholder="{{ __('Type your answer here...') }}"></textarea>
                            @error('content')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="mt-4">
                            <button type="submit" 
                                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                {{ __('Post Answer') }}
                            </button>
                        </div>
                    </form>
                </div>
            @else
                <div class="bg-blue-50 p-4 rounded-md">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h.01a1 1 0 100-2H10V9z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-blue-700">
                                {{ __('Please') }} <a href="{{ route('login') }}" class="font-medium text-blue-700 underline hover:text-blue-600">{{ __('sign in') }}</a> {{ __('to answer this question.') }}
                            </p>
                        </div>
                    </div>
                </div>
            @endauth
        </div>
    </div>
</div>

@push('scripts')
<script>
// Voting functionality with optimistic updates
async function vote(type, id, direction) {
    console.log('Vote function called with:', { type, id, direction });
    
    const upButton = document.getElementById(`${type}-up-${id}`);
    const downButton = document.getElementById(`${type}-down-${id}`);
    const scoreElement = document.getElementById(`${type}-score-${id}`);
    
    console.log('Elements found:', { upButton: !!upButton, downButton: !!downButton, scoreElement: !!scoreElement });
    
    if (!upButton || !downButton || !scoreElement) return;
    
    // Store original state for rollback
    const originalScore = parseInt(scoreElement.textContent);
    const originalUpClasses = upButton.className;
    const originalDownClasses = downButton.className;
    const originalUpSvg = upButton.querySelector('svg').className;
    const originalDownSvg = downButton.querySelector('svg').className;
    
    // Optimistic UI update - apply changes immediately
    let scoreChange = 0;
    let wasUpvoted = upButton.classList.contains('text-green-600');
    let wasDownvoted = downButton.classList.contains('text-red-600');
    
    if (direction === 'up') {
        if (wasUpvoted) {
            // Removing upvote
            scoreChange = -1;
            upButton.classList.remove('text-green-600', 'dark:text-green-400');
            upButton.querySelector('svg').classList.remove('text-green-600');
            upButton.querySelector('svg').classList.add('text-gray-400');
        } else {
            // Adding upvote (or changing from downvote)
            scoreChange = wasDownvoted ? 2 : 1;
            upButton.classList.add('text-green-600', 'dark:text-green-400');
            upButton.querySelector('svg').classList.remove('text-gray-400');
            upButton.querySelector('svg').classList.add('text-green-600');
            
            // Remove downvote if it existed
            if (wasDownvoted) {
                downButton.classList.remove('text-red-600', 'dark:text-red-400');
                downButton.querySelector('svg').classList.remove('text-red-600');
                downButton.querySelector('svg').classList.add('text-gray-400');
            }
        }
    } else {
        if (wasDownvoted) {
            // Removing downvote
            scoreChange = 1;
            downButton.classList.remove('text-red-600', 'dark:text-red-400');
            downButton.querySelector('svg').classList.remove('text-red-600');
            downButton.querySelector('svg').classList.add('text-gray-400');
        } else {
            // Adding downvote (or changing from upvote)
            scoreChange = wasUpvoted ? -2 : -1;
            downButton.classList.add('text-red-600', 'dark:text-red-400');
            downButton.querySelector('svg').classList.remove('text-gray-400');
            downButton.querySelector('svg').classList.add('text-red-600');
            
            // Remove upvote if it existed
            if (wasUpvoted) {
                upButton.classList.remove('text-green-600', 'dark:text-green-400');
                upButton.querySelector('svg').classList.remove('text-green-600');
                upButton.querySelector('svg').classList.add('text-gray-400');
            }
        }
    }
    
    // Update score immediately
    scoreElement.textContent = originalScore + scoreChange;
    
    // Add subtle animation
    scoreElement.style.transform = 'scale(1.2)';
    setTimeout(() => {
        scoreElement.style.transform = 'scale(1)';
    }, 200);
    
    try {
        const url = `/{{ app()->getLocale() }}/forum/${type}/${id}/vote`;
        console.log('Making request to:', url);
        console.log('Request payload:', { vote: direction });
        
        const response = await fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
                'Accept': 'application/json'
            },
            credentials: 'same-origin',
            body: JSON.stringify({ vote: direction })
        });

        console.log('Response status:', response.status);
        console.log('Response headers:', response.headers);

        const data = await response.json();
        console.log('Response data:', data);
        
        if (data.success) {
            // Update with server-confirmed score
            scoreElement.textContent = data.votes;
            
            // Update button states based on server response
            upButton.classList.remove('text-green-600', 'dark:text-green-400', 'text-blue-600', 'dark:text-blue-400');
            downButton.classList.remove('text-red-600', 'dark:text-red-400');
            upButton.querySelector('svg').classList.remove('text-green-600', 'text-blue-600');
            downButton.querySelector('svg').classList.remove('text-red-600');
            upButton.querySelector('svg').classList.add('text-gray-400');
            downButton.querySelector('svg').classList.add('text-gray-400');
            
            if (data.action === 'removed') {
                // Vote was removed - no active state
            } else if (data.user_vote === 'up') {
                upButton.classList.add('text-green-600', 'dark:text-green-400');
                upButton.querySelector('svg').classList.remove('text-gray-400');
                upButton.querySelector('svg').classList.add('text-green-600');
            } else if (data.user_vote === 'down') {
                downButton.classList.add('text-red-600', 'dark:text-red-400');
                downButton.querySelector('svg').classList.remove('text-gray-400');
                downButton.querySelector('svg').classList.add('text-red-600');
            }
        } else {
            throw new Error(data.message || 'Voting failed');
        }
    } catch (error) {
        console.error('Error voting:', error);
        
        // Rollback optimistic updates
        scoreElement.textContent = originalScore;
        upButton.className = originalUpClasses;
        downButton.className = originalDownClasses;
        upButton.querySelector('svg').className = originalUpSvg;
        downButton.querySelector('svg').className = originalDownSvg;
        
        // Show error message
        showErrorMessage('Failed to vote. Please try again.');
    }
}

// Show error message
function showErrorMessage(message) {
    const toast = document.createElement('div');
    toast.className =
        'fixed top-4 right-4 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg z-50 transition-opacity duration-300';
    toast.textContent = message;
    document.body.appendChild(toast);

    setTimeout(() => {
        toast.style.opacity = '0';
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}
</script>
@endpush
@endsection
