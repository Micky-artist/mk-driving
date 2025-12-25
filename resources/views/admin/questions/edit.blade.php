@extends('admin.layouts.app')

@section('title', 'Edit Question')

@push('styles')
<style>
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    .fade-in {
        animation: fadeIn 0.6s ease-out forwards;
    }
    
    .fade-in-delay-1 { animation-delay: 0.1s; }
    .fade-in-delay-2 { animation-delay: 0.2s; }
    
    .form-section {
        transition: all 0.3s ease;
    }
    
    .form-section:hover {
        transform: translateY(-2px);
    }
</style>
@endpush

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">
                Edit Question
            </h1>
            <p class="text-gray-600 dark:text-gray-400">
                Modify question details, answers, and quiz assignment.
            </p>
        </div>
        <div class="flex items-center gap-3">
            <form action="{{ route('admin.questions.index') }}" method="GET">
                <button type="submit" 
                        class="inline-flex items-center px-6 py-3 bg-gray-600 hover:bg-gray-700 text-white font-medium rounded-lg shadow-sm hover:shadow-md transition-all duration-200">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Back to Questions
                </button>
            </form>
        </div>
    </div>

    <!-- Success/Error Alerts -->
    @if (session('success'))
        <div class="mb-6 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4 fade-in fade-in-delay-1">
            <div class="flex items-center">
                <svg class="w-5 h-5 text-green-600 dark:text-green-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <p class="text-green-800 dark:text-green-200 font-medium">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    @if (session('error'))
        <div class="mb-6 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4 fade-in fade-in-delay-1">
            <div class="flex items-center">
                <svg class="w-5 h-5 text-red-600 dark:text-red-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <p class="text-red-800 dark:text-red-200 font-medium">{{ session('error') }}</p>
            </div>
        </div>
    @endif

    <!-- Edit Form -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 fade-in fade-in-delay-1">
        <form action="{{ route('admin.questions.update', $question) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')
            
            <!-- Question Details Section -->
            <div class="form-section bg-gray-50 dark:bg-gray-700/50 rounded-lg p-6 mb-6">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Question Details
                </h2>
                
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Question Text (English) -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"></path>
                            </svg>
                            Question Text (English)
                        </label>
                        <textarea name="question_text[en]" 
                                  rows="4" 
                                  class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white transition-all duration-200"
                                  placeholder="Enter the question text in English...">{{ is_array($question->text) ? ($question->text['en'] ?? '') : $question->text }}</textarea>
                    </div>
                    
                    <!-- Question Text (Kinyarwanda) -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"></path>
                            </svg>
                            Question Text (Kinyarwanda)
                        </label>
                        <textarea name="question_text[rw]" 
                                  rows="4" 
                                  class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white transition-all duration-200"
                                  placeholder="Enter the question text in Kinyarwanda...">{{ is_array($question->text) ? ($question->text['rw'] ?? '') : '' }}</textarea>
                    </div>
                </div>
                    
                    <!-- Question Type -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Question Type
                        </label>
                        <select name="question_type" 
                                class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white transition-all duration-200">
                            <option value="multiple_choice" {{ $question->type == 'multiple_choice' ? 'selected' : '' }}>Multiple Choice</option>
                            <option value="true_false" {{ $question->type == 'true_false' ? 'selected' : '' }}>True/False</option>
                            <option value="short_answer" {{ $question->type == 'short_answer' ? 'selected' : '' }}>Short Answer</option>
                        </select>
                    </div>
                </div>
                
                <!-- Points -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Points
                        </label>
                        <input type="number" 
                               name="points" 
                               value="{{ $question->points }}" 
                               min="1" 
                               class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white transition-all duration-200"
                               placeholder="Enter points">
                    </div>
                    
                    <!-- Quiz Assignment -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Assign to Quiz
                        </label>
                        <select name="quiz_id" 
                                class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white transition-all duration-200">
                            <option value="">No Quiz Assigned</option>
                            @foreach ($quizzes as $id => $title)
                                <option value="{{ $id }}" {{ $question->quiz_id == $id ? 'selected' : '' }}>{{ $title }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                
                <!-- Explanation (Bilingual) -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Explanation (English)
                        </label>
                        <textarea name="explanation[en]" 
                                  rows="3" 
                                  class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white transition-all duration-200"
                                  placeholder="Enter explanation in English...">{{ is_array($question->explanation) ? ($question->explanation['en'] ?? '') : $question->explanation }}</textarea>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Explanation (Kinyarwanda)
                        </label>
                        <textarea name="explanation[rw]" 
                                  rows="3" 
                                  class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white transition-all duration-200"
                                  placeholder="Enter explanation in Kinyarwanda...">{{ is_array($question->explanation) ? ($question->explanation['rw'] ?? '') : '' }}</textarea>
                    </div>
                </div>
            </div>
            
            <!-- Answer Options Section -->
            <div class="form-section bg-gray-50 dark:bg-gray-700/50 rounded-lg p-6">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                    Answer Options
                </h2>
                
                <div id="answer-options" class="space-y-4">
                    @foreach ($question->options as $index => $option)
                        <div class="flex items-start gap-4 p-4 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-600">
                            <input type="hidden" name="options[{{ $index }}][id]" value="{{ $option->id }}">
                            
                            <div class="flex items-center mt-6">
                                <input type="radio" 
                                       name="correct_option_id" 
                                       value="{{ $option->id }}" 
                                       {{ $option->is_correct ? 'checked' : '' }}
                                       class="w-4 h-4 text-blue-600 focus:ring-blue-500 border-gray-300">
                                <label class="ml-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Correct Answer
                                </label>
                            </div>
                            
                            <div class="flex-1">
                                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-3">
                                    <div>
                                        <div class="flex items-center gap-2 mb-2">
                                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Option {{ chr(65 + $index) }} (EN):</span>
                                        </div>
                                        <input type="text" 
                                               name="options[{{ $index }}][option_text][en]" 
                                               value="{{ is_array($option->option_text) ? ($option->option_text['en'] ?? '') : $option->option_text }}"
                                               class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white transition-all duration-200"
                                               placeholder="Enter answer option in English...">
                                    </div>
                                    
                                    <div>
                                        <div class="flex items-center gap-2 mb-2">
                                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Option {{ chr(65 + $index) }} (RW):</span>
                                        </div>
                                        <input type="text" 
                                               name="options[{{ $index }}][option_text][rw]" 
                                               value="{{ is_array($option->option_text) ? ($option->option_text['rw'] ?? '') : '' }}"
                                               class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white transition-all duration-200"
                                               placeholder="Enter answer option in Kinyarwanda...">
                                    </div>
                                </div>
                                
                                <div class="flex items-center gap-4">
                                    <div class="flex items-center">
                                        <label class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                                            <input type="checkbox" 
                                                   name="options[{{ $index }}][is_active]" 
                                                   value="1" 
                                                   {{ $option->is_active ?? 'checked' }}
                                                   class="w-4 h-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                            <span class="ml-2">Active</span>
                                        </label>
                                    </div>
                                    
                                    <div class="flex items-center">
                                        <label class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                                            <span class="mr-2">Order:</span>
                                            <input type="number" 
                                                   name="options[{{ $index }}][order]" 
                                                   value="{{ $option->order ?? $index + 1 }}"
                                                   min="1"
                                                   class="w-16 px-2 py-1 border border-gray-300 dark:border-gray-600 rounded text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                <!-- Add New Answer Button -->
                <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-600 flex justify-end">
                    <button type="button" 
                            onclick="addNewAnswer()" 
                            class="inline-flex items-center justify-center px-6 py-4 bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transform hover:scale-[1.02] transition-all duration-200 border border-green-500/20">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        <span class="flex items-center">
                            Add New Answer Option
                        </span>
                    </button>
                </div>
            </div>
            
            <!-- Form Actions -->
            <div class="flex justify-end pt-6">
                <div class="flex flex-col sm:flex-row gap-4">
                    <button type="submit" 
                            class="inline-flex items-center px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg shadow-sm hover:shadow-md transition-all duration-200">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Update Question
                    </button>
                    
                    <form action="{{ route('admin.questions.index') }}" method="GET">
                        <button type="submit" 
                                class="px-6 py-3 bg-gray-200 hover:bg-gray-300 text-gray-800 dark:bg-gray-700 dark:text-gray-300 font-medium rounded-lg transition-all duration-200">
                            Cancel
                        </button>
                    </form>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
function addNewAnswer() {
    const container = document.getElementById('answer-options');
    const optionCount = container.children.length;
    const newIndex = optionCount;
    
    const newAnswerHtml = `
        <div class="flex items-start gap-4 p-4 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-600">
            <input type="hidden" name="options[${newIndex}][id]" value="">
            
            <div class="flex items-center mt-6">
                <input type="radio" 
                       name="correct_option_id" 
                       value="" 
                       class="w-4 h-4 text-blue-600 focus:ring-blue-500 border-gray-300">
                <label class="ml-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                    Correct Answer
                </label>
            </div>
            
            <div class="flex-1">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-3">
                    <div>
                        <div class="flex items-center gap-2 mb-2">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Option ${chr(65 + newIndex)} (EN):</span>
                        </div>
                        <input type="text" 
                               name="options[${newIndex}][option_text][en]" 
                               class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white transition-all duration-200"
                               placeholder="Enter answer option in English...">
                    </div>
                    
                    <div>
                        <div class="flex items-center gap-2 mb-2">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Option ${chr(65 + newIndex)} (RW):</span>
                        </div>
                        <input type="text" 
                               name="options[${newIndex}][option_text][rw]" 
                               class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white transition-all duration-200"
                               placeholder="Enter answer option in Kinyarwanda...">
                    </div>
                </div>
                
                <div class="flex items-center gap-4">
                    <div class="flex items-center">
                        <label class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                            <input type="checkbox" 
                                   name="options[${newIndex}][is_active]" 
                                   value="1" 
                                   checked
                                   class="w-4 h-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                            <span class="ml-2">Active</span>
                        </label>
                    </div>
                    
                    <div class="flex items-center">
                        <label class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                            <span class="mr-2">Order:</span>
                            <input type="number" 
                                   name="options[${newIndex}][order]" 
                                   value="${newIndex + 1}"
                                   min="1"
                                   class="w-16 px-2 py-1 border border-gray-300 dark:border-gray-600 rounded text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                        </label>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    container.insertAdjacentHTML('beforeend', newAnswerHtml);
}
</script>
@endsection
