@extends('layouts.app')

@section('content')
    <!-- Include unified navbar for dashboard -->
    <x-unified-navbar :showUserStats="true" />
    
    <div class="pt-16"><div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Back to questions -->
    <div class="mb-6">
        <a href="{{ route('dashboard.forum.index') }}" class="inline-flex items-center text-sm font-medium text-blue-600 hover:text-blue-800">
            <svg class="mr-1 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
            </svg>
            Back to questions
        </a>
    </div>

    <div class="w-full">
        <!-- Main Content -->
            <!-- Question -->
            <div class="bg-white shadow overflow-hidden sm:rounded-lg mb-6">
                <div class="px-4 py-5 sm:px-6 flex items-start justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">{{ $question->title }}</h1>
                        <div class="mt-1 flex items-center text-sm text-gray-500">
                            <span>Asked {{ timeDiffForHumans($question->created_at) }} by {{ $question->user->name }}</span>
                            <span class="mx-2">•</span>
                            <span>{{ $question->views }} {{ Str::plural('view', $question->views) }}</span>
                        </div>
                    </div>
                    <div class="flex items-center space-x-2">
                        <div class="text-center">
                            <button onclick="vote('question', {{ $question->id }}, 'up')" 
                                    class="text-gray-400 hover:text-blue-500 focus:outline-none">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                                </svg>
                            </button>
                            <div id="question-{{ $question->id }}-votes" class="text-sm font-medium text-gray-900">
                                {{ $question->votes ?? 0 }}
                            </div>
                            <button onclick="vote('question', {{ $question->id }}, 'down')" 
                                    class="text-gray-400 hover:text-red-500 focus:outline-none">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="border-t border-gray-200 px-4 py-5 sm:px-6">
                    <div class="prose max-w-none">
                        {!! $question->content !!}
                    </div>
                    <div class="mt-4 flex flex-wrap gap-2">
                        @foreach($question->topics as $topic)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                {{ $topic }}
                            </span>
                        @endforeach
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-4 sm:px-6 flex justify-between items-center">
                    <div class="flex space-x-4">
                        <button type="button" class="inline-flex items-center text-sm text-gray-500 hover:text-gray-700">
                            <svg class="mr-1 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M2 5a2 2 0 012-2h7a2 2 0 012 2v4a2 2 0 01-2 2H9l-3 3v-3H4a2 2 0 01-2-2V5z" />
                                <path d="M15 7v2a4 4 0 01-4 4H9.828l-1.766 1.767c.28.149.599.233.938.233h2l3 3v-3h2a2 2 0 002-2V9a2 2 0 00-2-2h-1z" />
                            </svg>
                            {{ $question->answers_count }} {{ Str::plural('answer', $question->answers_count) }}
                        </button>
                    </div>
                    @if(auth()->id() === $question->user_id)
                        <div class="flex space-x-2">
                            <a href="#" class="text-sm text-blue-600 hover:text-blue-800">Edit</a>
                            <span class="text-gray-300">•</span>
                            <a href="#" class="text-sm text-red-600 hover:text-red-800">Delete</a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Answers -->
            <div class="mb-8">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-medium text-gray-900">{{ $question->answers_count }} {{ Str::plural('Answer', $question->answers_count) }}</h2>
                    <div class="flex items-center">
                        <span class="text-sm text-gray-500 mr-2">Sort by:</span>
                        <select class="text-sm border-gray-300 rounded-md focus:border-blue-500 focus:ring-blue-500">
                            <option>Highest Score</option>
                            <option>Newest</option>
                            <option>Oldest</option>
                        </select>
                    </div>
                </div>

                @if($question->answers_count > 0)
                    <div class="space-y-6">
                        @foreach($question->answers as $answer)
                            <div id="answer-{{ $answer->id }}" class="bg-white shadow overflow-hidden sm:rounded-lg border-l-4 {{ $question->best_answer_id == $answer->id ? 'border-blue-500' : 'border-transparent' }}">
                                <div class="p-4 sm:p-6">
                                    <div class="flex items-start">
                                        <!-- Voting -->
                                        <div class="flex flex-col items-center mr-4">
                                            <button onclick="vote('answer', {{ $answer->id }}, 'up')" 
                                                    class="text-gray-400 hover:text-blue-500 focus:outline-none">
                                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                                                </svg>
                                            </button>
                                            <div id="answer-{{ $answer->id }}-votes" class="my-1 text-sm font-medium text-gray-900">
                                                {{ $answer->votes ?? 0 }}
                                            </div>
                                            <button onclick="vote('answer', {{ $answer->id }}, 'down')" 
                                                    class="text-gray-400 hover:text-red-500 focus:outline-none">
                                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                                </svg>
                                            </button>
                                            @if(auth()->id() === $question->user_id && !$question->best_answer_id)
                                                <button onclick="markAsBestAnswer({{ $answer->id }})" 
                                                        class="mt-2 text-gray-400 hover:text-green-500 focus:outline-none" 
                                                        title="Mark as best answer">
                                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                    </svg>
                                                </button>
                                            @elseif($question->best_answer_id == $answer->id)
                                                <div class="mt-2 text-green-500" title="Best answer">
                                                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                                    </svg>
                                                </div>
                                            @endif
                                        </div>
                                        
                                        <!-- Answer content -->
                                        <div class="flex-1 min-w-0">
                                            <div class="prose max-w-none">
                                                {!! $answer->content !!}
                                            </div>
                                            <div class="mt-4 flex items-center justify-between">
                                                <div class="flex items-center text-sm text-gray-500">
                                                    <div class="flex items-center">
                                                        <img class="h-8 w-8 rounded-full" src="https://ui-avatars.com/api/?name={{ urlencode($answer->user->name) }}&color=7F9CF5&background=EBF4FF" alt="">
                                                        <div class="ml-2">
                                                            <div class="font-medium text-gray-900">{{ $answer->user->name }}</div>
                                                            <div>Answered {{ timeDiffForHumans($answer->created_at) }}</div>
                                                        </div>
                                                    </div>
                                                </div>
                                                @if(auth()->id() === $answer->user_id)
                                                    <div class="flex space-x-2">
                                                        <a href="#" class="text-sm text-blue-600 hover:text-blue-800">Edit</a>
                                                        <span class="text-gray-300">•</span>
                                                        <a href="#" class="text-sm text-red-600 hover:text-red-800">Delete</a>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                        <div class="px-4 py-5 sm:p-6 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M8 10h.01M12 14h.01M16 18h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No answers yet</h3>
                            <p class="mt-1 text-sm text-gray-500">Be the first to answer this question!</p>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Your Answer -->
            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Your Answer</h3>
                </div>
                <form action="{{ route('dashboard.forum.answers.store', $question) }}" method="POST" class="p-6">
                    @csrf
                    <div class="mb-4">
                        <label for="content" class="sr-only">Your Answer</label>
                        <textarea id="content" name="content" rows="8" class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border border-gray-300 rounded-md" placeholder="Write your answer here..." required></textarea>
                        @error('content')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="flex justify-end">
                        <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Post Your Answer
                        </button>
                    </div>
                </form>
            </div>
        </div>
</div>
</div>

@push('scripts')
<script>
    // Handle voting
    async function vote(type, id, voteType) {
        try {
            const response = await fetch(`/dashboard/forum/${type}/${id}/vote`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ vote: voteType })
            });

            const data = await response.json();
            
            if (data.success) {
                // Update the vote count display
                const voteCountElement = document.getElementById(`${type}-${id}-votes`);
                if (voteCountElement) {
                    voteCountElement.textContent = data.votes;
                }
                
                // Optional: Update button appearance based on vote
                console.log(`Vote ${voteType} successful`);
            }
        } catch (error) {
            console.error('Error voting:', error);
        }
    }

    // Handle marking as best answer
    async function markAsBestAnswer(answerId) {
        if (!confirm('Are you sure you want to mark this as the best answer?')) {
            return;
        }

        try {
            const response = await fetch(`/dashboard/forum/{{ $question->id }}/best-answer/${answerId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            if (response.ok) {
                // Reload the page to show the updated best answer
                window.location.reload();
            }
        } catch (error) {
            console.error('Error marking as best answer:', error);
        }
    }

    // Initialize any rich text editor if needed
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize editor here if needed
    });
</script>
@endpush

@endsection
