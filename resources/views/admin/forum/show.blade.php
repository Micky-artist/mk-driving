@extends('admin.layouts.app')

@section('title', 'Forum Question Details')

@section('content')
<div class="space-y-6">
    <!-- Breadcrumb -->
    <nav class="flex mb-6" aria-label="Breadcrumb">
        <ol class="flex items-center space-x-2 text-sm text-gray-500 dark:text-gray-400">
            <li>
                <a href="{{ route('admin.portal') }}" class="hover:text-gray-700 dark:hover:text-gray-300">Dashboard</a>
            </li>
            <li>
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                </svg>
            </li>
            <li>
                <a href="{{ route('admin.forum.index') }}" class="hover:text-gray-700 dark:hover:text-gray-300">Forum</a>
            </li>
            <li>
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                </svg>
            </li>
            <li class="text-gray-900 dark:text-white font-medium">Question Details</li>
        </ol>
    </nav>

    <!-- Question Details -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 mb-6">
        <div class="p-6">
            <!-- Question Header -->
            <div class="flex items-start justify-between mb-4">
                <div class="flex-1">
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">
                        @if(isset($question->title['en']) && isset($question->title['rw']))
                            <div>
                                <div class="text-sm text-gray-500 dark:text-gray-400 mb-1">English:</div>
                                {{ $question->title['en'] }}
                            </div>
                            <div class="text-sm text-gray-500 dark:text-gray-400 mb-1 mt-2">Kinyarwanda:</div>
                            {{ $question->title['rw'] }}
                        @else
                            {{ $question->title['en'] ?? $question->title['rw'] ?? 'Untitled' }}
                        @endif
                    </h1>
                    <div class="flex items-center gap-4 text-sm text-gray-500 dark:text-gray-400">
                        <span class="flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            {{ $question->user?->name ?? 'Deleted User' }}
                        </span>
                        <span class="flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            {{ $question->created_at->format('M j, Y g:i A') }}
                        </span>
                        <span class="flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                            {{ $question->views }} views
                        </span>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    @if(!$question->is_approved)
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300">
                            Pending
                        </span>
                    @endif
                    @if($question->is_news_discussion)
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300">
                            News Discussion
                        </span>
                    @endif
                </div>
            </div>

            <!-- Question Content -->
            <div class="prose prose-sm max-w-none dark:prose-invert mb-6">
                <div class="text-gray-700 dark:text-gray-300">
                    @if(isset($question->content['en']) && isset($question->content['rw']))
                        <div>
                            <div class="text-sm text-gray-500 dark:text-gray-400 mb-2 font-medium">English:</div>
                            <div class="mb-4">{!! $question->content['en'] !!}</div>
                        </div>
                        <div>
                            <div class="text-sm text-gray-500 dark:text-gray-400 mb-2 font-medium">Kinyarwanda:</div>
                            <div>{!! $question->content['rw'] !!}</div>
                        </div>
                    @else
                        {!! $question->content['en'] ?? $question->content['rw'] ?? '' !!}
                    @endif
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                <form action="{{ route('admin.forum.toggle-approval', $question) }}" method="POST" class="inline">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="inline-flex items-center px-4 py-2 text-sm font-medium rounded-md transition-colors duration-200
                        {{ $question->is_approved 
                            ? 'bg-yellow-100 text-yellow-800 hover:bg-yellow-200 dark:bg-yellow-900/30 dark:text-yellow-300 dark:hover:bg-yellow-900/50' 
                            : 'bg-green-100 text-green-800 hover:bg-green-200 dark:bg-green-900/30 dark:text-green-300 dark:hover:bg-green-900/50' }}">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        {{ $question->is_approved ? 'Unapprove' : 'Approve' }}
                    </button>
                </form>
                
                <form action="{{ route('admin.forum.destroy', $question) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this question?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-md hover:bg-red-700 transition-colors duration-200">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                        Delete
                    </button>
                </form>
                
                <a href="{{ route('admin.forum.index') }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 rounded-md hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors duration-200">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Back to Forum
                </a>
            </div>
        </div>
    </div>

    <!-- Answers Section -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
        <div class="p-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                Answers ({{ $question->answers->count() }})
            </h2>

            <!-- Answer Form -->
            <div class="mb-6 p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                <h3 class="text-sm font-medium text-gray-900 dark:text-white mb-3">Post an Answer</h3>
                <form action="{{ route('admin.forum.answers.store', $question) }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <textarea name="content" rows="4" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white" placeholder="Write your answer..." required></textarea>
                    </div>
                    <button type="submit" class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700 transition-colors duration-200">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                        </svg>
                        Post Answer
                    </button>
                </form>
            </div>

            <!-- Answers List -->
            @if($question->answers->count() > 0)
                <div class="space-y-4">
                    @foreach($question->answers as $answer)
                        <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4 {{ !$answer->is_approved ? 'bg-yellow-50 dark:bg-yellow-900/10' : '' }}">
                            <div class="flex items-start justify-between mb-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 bg-blue-500 text-white rounded-full flex items-center justify-center text-sm font-medium">
                                        {{ substr($answer->user?->name ?? 'D', 0, 1) }}
                                    </div>
                                    <div>
                                        <div class="font-medium text-gray-900 dark:text-white">{{ $answer->user?->name ?? 'Deleted User' }}</div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">{{ $answer->created_at->diffForHumans() }}</div>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2">
                                    @if(!$answer->is_approved)
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300">
                                            Pending
                                        </span>
                                    @endif
                                    <form action="{{ route('admin.forum.answers.destroy', $answer) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this answer?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </div>
                            <div class="prose prose-sm max-w-none dark:prose-invert">
                                <div class="text-gray-700 dark:text-gray-300">
                                    @if(isset($answer->content['en']) && isset($answer->content['rw']))
                                        <div>
                                            <div class="text-sm text-gray-500 dark:text-gray-400 mb-2 font-medium">English:</div>
                                            <div class="mb-4">{!! $answer->content['en'] !!}</div>
                                        </div>
                                        <div>
                                            <div class="text-sm text-gray-500 dark:text-gray-400 mb-2 font-medium">Kinyarwanda:</div>
                                            <div>{!! $answer->content['rw'] !!}</div>
                                        </div>
                                    @else
                                        {!! $answer->content['en'] ?? $answer->content['rw'] ?? '' !!}
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                    <svg class="w-12 h-12 mx-auto mb-4 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                    </svg>
                    <p>No answers yet. Be the first to answer!</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
