@extends('admin.layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Forum Management</h1>
            <p class="text-gray-600">Review and manage forum questions and answers</p>
        </div>
    </div>

    @if (session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert">
            <p>{{ session('success') }}</p>
        </div>
    @endif

    @if (session('error'))
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
            <p>{{ session('error') }}</p>
        </div>
    @endif

    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        @if($questions->count() > 0)
            <div class="divide-y divide-gray-200">
                @foreach($questions as $question)
                    <div class="p-6 hover:bg-gray-50 transition-colors duration-150">
                        <div class="flex justify-between items-start">
                            <div class="flex-1">
                                <div class="flex items-center space-x-2">
                                    <h3 class="text-lg font-medium text-gray-900">
                                        {{ $question->title['en'] ?? 'No title' }}
                                    </h3>
                                    
                                    @if($question->is_approved)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            Approved
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            Pending
                                        </span>
                                    @endif
                                </div>
                                
                                <p class="mt-1 text-sm text-gray-600">
                                    {{ $question->content['en'] ?? 'No content' }}
                                </p>
                                
                                <div class="mt-2 flex items-center text-sm text-gray-500">
                                    <span>Asked by {{ $question->user->name ?? 'Unknown' }}</span>
                                    <span class="mx-2">•</span>
                                    <span>{{ $question->created_at->diffForHumans() }}</span>
                                    <span class="mx-2">•</span>
                                    <span>{{ $question->answers_count ?? 0 }} answers</span>
                                    <span class="mx-2">•</span>
                                    <span>{{ $question->views }} views</span>
                                </div>
                                
                                @if($question->topics && count($question->topics) > 0)
                                    <div class="mt-2 flex flex-wrap gap-1">
                                        @foreach($question->topics as $topic)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                {{ $topic }}
                                            </span>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                            
                            <div class="ml-4 flex-shrink-0 flex space-x-2">
                                <form action="{{ route('admin.forum.toggle-approval', $question) }}" method="POST" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="text-sm font-medium {{ $question->is_approved ? 'text-yellow-600 hover:text-yellow-900' : 'text-green-600 hover:text-green-900' }}">
                                        {{ $question->is_approved ? 'Unapprove' : 'Approve' }}
                                    </button>
                                </form>
                                
                                <form action="{{ route('admin.forum.destroy', $question) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this question and all its answers?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-sm font-medium text-red-600 hover:text-red-900 ml-2">
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </div>
                        
                        <!-- Answers Section -->
                        @if($question->answers->count() > 0)
                            <div class="mt-4 pl-4 border-l-2 border-gray-200">
                                <h4 class="text-sm font-medium text-gray-700 mb-2">Answers ({{ $question->answers->count() }})</h4>
                                <div class="space-y-4">
                                    @foreach($question->answers as $answer)
                                        <div class="bg-gray-50 p-3 rounded-md">
                                            <div class="flex justify-between items-start">
                                                <div>
                                                    <p class="text-sm text-gray-700">
                                                        {{ $answer->content['en'] ?? 'No content' }}
                                                    </p>
                                                    <div class="mt-1 text-xs text-gray-500">
                                                        Answered by {{ $answer->user->name ?? 'Unknown' }}
                                                        <span class="mx-1">•</span>
                                                        {{ $answer->created_at->diffForHumans() }}
                                                    </div>
                                                </div>
                                                <form action="{{ route('admin.forum.answers.destroy', $answer) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this answer?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-xs text-red-600 hover:text-red-900">
                                                        Delete
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                        
                        <!-- Add Answer Form -->
                        <div class="mt-4 pt-4 border-t border-gray-200">
                            <form action="{{ route('admin.forum.answers.store', $question) }}" method="POST">
                                @csrf
                                <div>
                                    <label for="answer-{{ $question->id }}" class="block text-sm font-medium text-gray-700 mb-1">
                                        Post an answer
                                    </label>
                                    <div class="mt-1">
                                        <textarea id="answer-{{ $question->id }}" name="content" rows="2" 
                                            class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border border-gray-300 rounded-md"
                                            placeholder="Write your answer here..." required></textarea>
                                    </div>
                                </div>
                                <div class="mt-2">
                                    <button type="submit" class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                        Post Answer
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
            
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $questions->links() }}
            </div>
        @else
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No questions yet</h3>
                <p class="mt-1 text-sm text-gray-500">There are no forum questions to display at the moment.</p>
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
    // Confirmation for delete actions
    document.addEventListener('DOMContentLoaded', function() {
        const deleteForms = document.querySelectorAll('form[onsubmit*="confirm"]');
        
        deleteForms.forEach(form => {
            form.addEventListener('submit', function(e) {
                if (!confirm('Are you sure you want to delete this item?')) {
                    e.preventDefault();
                }
            });
        });
    });
</script>
@endpush
@endsection
