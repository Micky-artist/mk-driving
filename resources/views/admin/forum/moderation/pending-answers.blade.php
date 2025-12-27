@extends('admin.layouts.app')

@section('title', 'Pending Answers')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Page Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Pending Answers</h1>
                    <p class="mt-2 text-gray-600 dark:text-gray-400">Review and approve user answers awaiting moderation</p>
                </div>
                <div class="flex space-x-3">
                    <button type="button" 
                            onclick="window.location.href='{{ route('admin.forum.moderation.index') }}'"
                            class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white font-medium rounded-lg transition-colors duration-200">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Back to Moderation
                    </button>
                    <button type="button" 
                            onclick="window.location.href='{{ route('admin.forum.index') }}'"
                            class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors duration-200">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4;4a();a1 
                             
                        }). 
</svg>
</button>
erce 
           component 
class 
extends 
React 
PureComponentistic 
Component 
Side 
{ 
; 
render() 
 
{; 
 attest 
const . 
 . 
.; 
} 
__; 
};”; 
} 
 ""; 

</button> 
</div> 
</div> 
</div>

<!-- Pending Answers List -->
<div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
    <div class="p-6">
        @if($pendingAnswers->count() > 0)
            <div class="space-y-6">
                @foreach($pendingAnswers as $answer)
                    <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-6 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-200">
                        <!-- Question Context -->
                        <div class="mb-4">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <h4 class="text-lg font-medium text-gray-900 dark:text-white mb-2">
                                        Question: {{ $answer->question->title['en'] ?? $answer->question->title['rw'] ?? 'Untitled' }}
                                    </h4>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                        Asked by {{ $answer->question->user->name ?? 'Unknown User' }} · 
                                        {{ $answer->question->created_at->diffForHumans() }}
                                    </p>
                                </div>
                                <a href="{{ route('admin.forum.show', $answer->question) }}" 
                                   class="inline-flex items-center px-3 py-1 text-sm bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-md hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors duration-200">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                    View Question
                                </a>
                            </div>
                        </div>

                        <!-- Answer Content -->
                        <div class="mb-4">
                            <div class="bg-gray-50 dark:bg-gray-700/30 rounded-lg p-4">
                                <p class="text-gray-700 dark:text-gray-300 whitespace-pre-wrap">
                                    @if(isset($answer->content['en']) && isset($answer->content['rw']))
                                        <div>
                                            <div class="text-sm text-gray-500 dark:text-gray-400 mb-2 font-medium">English:</div>
                                            <div class="mb-4">{{ $answer->content['en'] }}</div>
                                        </div>
                                        <div>
                                            <div class="text-sm text-gray-500 dark:text-gray-400 mb-2 font-medium">Kinyarwanda:</div>
                                            <div>{{ $answer->content['rw'] }}</div>
                                        </div>
                                    @else
                                        {{ $answer->content['en'] ?? $answer->content['rw'] ?? '' }}
                                    @endif
                                </p>
                            </div>
                        </div>

                        <!-- Answer Author & Actions -->
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900/20 rounded-full flex items-center justify-center">
                                        <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                    </div>
                                    <div class="ml-2">
                                        <p class="text-sm font-medium text-gray-900 dark:text-white">
                                            {{ $answer->user->name ?? 'Unknown User' }}
                                        </p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">
                                            {{ $answer->user->email ?? 'No email' }} · 
                                            {{ $answer->created_at->diffForHumans() }}
                                        </p>
                                    </div>
                                </div>
                                @if($answer->user)
                                <a href="{{ route('admin.users.show', $answer->user->id) }}" 
                                   class="inline-flex items-center px-2 py-1 text-xs bg-purple-100 dark:bg-purple-900/20 text-purple-700 dark:text-purple-300 rounded-md hover:bg-purple-200 dark:hover:bg-purple-800 transition-colors duration-200">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                    Manage User
                                </a>
                                @endif
                            </div>

                            <div class="flex items-center space-x-2">
                                <form action="{{ route('admin.forum.moderation.approve-answer', $answer) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" 
                                            class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition-colors duration-200">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        Approve
                                    </button>
                                </form>
                                <form action="{{ route('admin.forum.moderation.reject-answer', $answer) }}" method="POST" class="inline"
                                      onsubmit="return confirm('Are you sure you want to reject and delete this answer?')">
                                    @csrf
                                    <button type="submit" 
                                            class="inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg transition-colors duration-200">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                        Reject
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            @if($pendingAnswers->hasPages())
                <div class="mt-6">
                    {{ $pendingAnswers->links() }}
                </div>
            @endif
        @else
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No pending answers</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">All answers have been reviewed and approved.</p>
                <div class="mt-6">
                    <button type="button" 
                            onclick="window.location.href='{{ route('admin.forum.moderation.index') }}'"
                            class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg transition-colors duration-200">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Back to Moderation
                    </button>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
