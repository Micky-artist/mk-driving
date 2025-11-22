@extends('dashboard.layouts.app')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-6">
        <a href="{{ route('dashboard.forum.index') }}" class="inline-flex items-center text-sm font-medium text-blue-600 hover:text-blue-800">
            <svg class="mr-1 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
            </svg>
            Back to questions
        </a>
    </div>

    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
            <h1 class="text-2xl font-bold text-gray-900">Ask a Question</h1>
            <p class="mt-1 text-sm text-gray-500">Be clear and concise to get the best answers from the community.</p>
        </div>
        
        <form action="{{ route('dashboard.forum.store') }}" method="POST" class="p-6">
            @csrf
            
            <!-- Title -->
            <div class="mb-6">
                <label for="title" class="block text-sm font-medium text-gray-700">Title</label>
                <p class="mt-1 text-sm text-gray-500">Be specific and imagine you're asking a question to another person.</p>
                <div class="mt-2">
                    <input type="text" name="title" id="title" 
                           class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md @error('title') border-red-300 text-red-900 placeholder-red-300 focus:outline-none focus:ring-red-500 focus:border-red-500 @enderror"
                           value="{{ old('title') }}" 
                           placeholder="e.g. How do I prepare for the driving theory test?"
                           required>
                    @error('title')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Content -->
            <div class="mb-6">
                <label for="content" class="block text-sm font-medium text-gray-700">Details</label>
                <p class="mt-1 text-sm text-gray-500">Include all the information someone would need to answer your question.</p>
                <div class="mt-2">
                    <textarea id="content" name="content" rows="8" 
                              class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border border-gray-300 rounded-md @error('content') border-red-300 text-red-900 placeholder-red-300 focus:outline-none focus:ring-red-500 focus:border-red-500 @enderror" 
                              placeholder="Provide more details about your question here..."
                              required>{{ old('content') }}</textarea>
                    @error('content')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Topics -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Topics</label>
                <p class="text-sm text-gray-500 mb-3">Add up to 3 topics that best describe your question.</p>
                
                <div id="topics-container" class="space-y-2">
                    <!-- Topics will be added here by JavaScript -->
                </div>
                
                <div class="mt-2 relative">
                    <input type="text" id="topic-input" 
                           class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md"
                           placeholder="Type a topic and press Enter"
                           maxlength="50">
                    <p class="mt-1 text-xs text-gray-500">Press Enter to add a topic</p>
                    @error('topics')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Hidden input to store the selected topics -->
                <div id="topics-data">
                    @if(old('topics'))
                        @foreach((array)old('topics') as $topic)
                            <input type="hidden" name="topics[]" value="{{ $topic }}">
                        @endforeach
                    @endif
                </div>
            </div>

            <!-- Preview (optional) -->
            <div class="mb-6 border-t border-gray-200 pt-6">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-medium text-gray-900">Preview</h3>
                    <button type="button" id="toggle-preview" class="text-sm text-blue-600 hover:text-blue-800">
                        Show preview
                    </button>
                </div>
                <div id="preview-content" class="mt-2 hidden p-4 bg-gray-50 rounded-md">
                    <h3 id="preview-title" class="text-lg font-medium text-gray-900"></h3>
                    <div id="preview-body" class="mt-2 prose max-w-none"></div>
                    <div id="preview-topics" class="mt-3 flex flex-wrap gap-2"></div>
                </div>
            </div>

            <!-- Submit -->
            <div class="pt-5">
                <div class="flex justify-end">
                    <a href="{{ route('dashboard.forum.index') }}" class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Cancel
                    </a>
                    <button type="submit" class="ml-3 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Post Your Question
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const topicInput = document.getElementById('topic-input');
        const topicsContainer = document.getElementById('topics-container');
        const topicsData = document.getElementById('topics-data');
        const previewButton = document.getElementById('toggle-preview');
        const previewContent = document.getElementById('preview-content');
        const previewTitle = document.getElementById('preview-title');
        const previewBody = document.getElementById('preview-body');
        const previewTopics = document.getElementById('preview-topics');
        
        // Available topics for suggestions
        const availableTopics = [
            'driving-test', 'theory-test', 'learner-permit', 'road-rules', 
            'traffic-signs', 'parallel-parking', 'highway-driving', 'night-driving',
            'winter-driving', 'defensive-driving', 'fuel-efficiency', 'car-maintenance'
        ];
        
        // Initialize topics from existing hidden inputs
        const existingTopics = Array.from(document.querySelectorAll('input[name="topics[]"]')).map(input => input.value);
        existingTopics.forEach(topic => addTopic(topic));
        
        // Handle topic input
        topicInput.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                const value = topicInput.value.trim();
                
                if (value && !existingTopics.includes(value)) {
                    addTopic(value);
                    topicInput.value = '';
                }
            }
        });
        
        // Add a topic
        function addTopic(topic) {
            if (existingTopics.length >= 3) {
                alert('You can only add up to 3 topics');
                return;
            }
            
            if (topic && !existingTopics.includes(topic)) {
                existingTopics.push(topic);
                
                // Add to topics container
                const topicElement = document.createElement('div');
                topicElement.className = 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 mr-2 mb-2';
                topicElement.innerHTML = `
                    ${topic}
                    <button type="button" class="ml-1.5 inline-flex text-blue-400 hover:text-blue-600 focus:outline-none" data-topic="${topic}">
                        <span class="sr-only">Remove topic</span>
                        <svg class="h-3.5 w-3.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </button>
                `;
                
                // Add to hidden inputs
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'topics[]';
                input.value = topic;
                topicsData.appendChild(input);
                
                // Add to container
                topicsContainer.appendChild(topicElement);
                
                // Add remove event
                topicElement.querySelector('button').addEventListener('click', function() {
                    const topicToRemove = this.getAttribute('data-topic');
                    const index = existingTopics.indexOf(topicToRemove);
                    if (index > -1) {
                        existingTopics.splice(index, 1);
                    }
                    topicsContainer.removeChild(topicElement);
                    
                    // Remove from hidden inputs
                    const inputs = document.querySelectorAll('input[name="topics[]"]');
                    inputs.forEach(input => {
                        if (input.value === topicToRemove) {
                            topicsData.removeChild(input);
                        }
                    });
                    
                    updatePreview();
                });
                
                updatePreview();
            }
        }
        
        // Toggle preview
        let previewVisible = false;
        previewButton.addEventListener('click', function() {
            previewVisible = !previewVisible;
            
            if (previewVisible) {
                updatePreview();
                previewContent.classList.remove('hidden');
                previewButton.textContent = 'Hide preview';
            } else {
                previewContent.classList.add('hidden');
                previewButton.textContent = 'Show preview';
            }
        });
        
        // Update preview content
        function updatePreview() {
            const title = document.getElementById('title')?.value || 'Your question title will appear here';
            const content = document.getElementById('content')?.value || 'Your question content will appear here';
            
            previewTitle.textContent = title;
            previewBody.innerHTML = marked.parse(content);
            
            // Update topics in preview
            previewTopics.innerHTML = '';
            existingTopics.forEach(topic => {
                const tag = document.createElement('span');
                tag.className = 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800';
                tag.textContent = topic;
                previewTopics.appendChild(tag);
            });
        }
        
        // Update preview when content changes
        document.getElementById('title')?.addEventListener('input', updatePreview);
        document.getElementById('content')?.addEventListener('input', updatePreview);
    });
</script>
@endpush

@endsection
