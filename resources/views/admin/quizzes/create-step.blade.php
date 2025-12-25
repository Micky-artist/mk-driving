@extends('admin.layouts.app')

@section('title', 'Create Quiz - Question {{ $step }}')

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
    .fade-in-delay-3 { animation-delay: 0.3s; }
    
    .question-card {
        transition: all 0.3s ease;
    }
    
    .option-input {
        transition: all 0.2s ease;
    }
    
    .option-input:focus {
        transform: scale(1.02);
    }
    
    .image-preview {
        max-width: 200px;
        max-height: 150px;
        object-fit: cover;
        border-radius: 8px;
    }
    
    .progress-step {
        transition: all 0.3s ease;
    }
    
    .progress-step.active {
        background: linear-gradient(135deg, #1e40af 0%, #1e3a8a 100%);
        color: white;
        transform: scale(1.1);
    }
    
    .progress-step.completed {
        background: linear-gradient(135deg, #059669 0%, #047857 100%);
        color: white;
    }
</style>
@endpush

@section('content')
<div class="space-y-6">
    <!-- Header with Progress -->
    <div class="mb-8">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">
                    Create Road Theory Test - Question {{ $step }}/20
                </h1>
                <p class="text-gray-600 dark:text-gray-400">
                    Complete each question step-by-step. Your progress is automatically saved.
                </p>
            </div>
            
            <!-- Auto-save indicator -->
            <div id="auto-save-indicator" class="px-4 py-2 bg-gray-100 dark:bg-gray-700 rounded-lg border border-gray-200 dark:border-gray-600">
                <span class="text-sm text-gray-500 dark:text-gray-400">
                    💾 Auto-save enabled
                </span>
            </div>
        </div>

        <!-- Progress Bar -->
        <div class="mb-6">
            <div class="flex items-center justify-between mb-2">
                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Progress</span>
                <span class="text-sm text-gray-500 dark:text-gray-400">{{ $step }}/20 Questions</span>
            </div>
            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-3">
                <div class="bg-gradient-to-r from-blue-600 to-blue-700 h-3 rounded-full transition-all duration-500 ease-out" 
                     style="width: {{ ($step / 20) * 100 }}%"></div>
            </div>
            
            <!-- Step indicators -->
            <div class="flex justify-between mt-4">
                @for ($i = 1; $i <= 20; $i++)
                    <div class="progress-step {{ $i == $step ? 'active' : ($i < $step ? 'completed' : '') }} 
                                w-8 h-8 rounded-full flex items-center justify-center text-xs font-semibold
                                {{ $i < $step ? 'bg-green-500 text-white' : ($i == $step ? 'bg-blue-600 text-white' : 'bg-gray-300 dark:bg-gray-600 text-gray-600 dark:text-gray-400') }}">
                        @if ($i < $step)
                            ✓
                        @else
                            {{ $i }}
                        @endif
                    </div>
                @endfor
            </div>
        </div>
    </div>

    <!-- Error Alert -->
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

    
    <!-- Question Form -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 fade-in fade-in-delay-2">
        <form action="{{ route('admin.quizzes.create.question.store', ['step' => $step]) }}" method="POST" enctype="multipart/form-data" class="p-6">
            @csrf
            
            <!-- Question Details -->
            <div class="mb-8">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-6 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Question {{ $step }}
                </h2>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Question Text - English -->
                    <div class="lg:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Question Text (English) *
                        </label>
                        <textarea name="text" 
                                  rows="4"
                                  class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white transition-all duration-200 resize-none"
                                  placeholder="Enter question text in English..."
                                  required>{{ old('text', $draftData['questions'][$step - 1]['text'] ?? '') }}</textarea>
                        @error('text')
                            <p class="mt-2 text-sm text-red-600 dark:text-red-400 flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Question Text - Rwanda -->
                    <div class="lg:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Question Text (Kinyarwanda) *
                        </label>
                        <textarea name="text_rw" 
                                  rows="4"
                                  class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white transition-all duration-200 resize-none"
                                  placeholder="Andika umubano mu Kinyarwanda..."
                                  required>{{ old('text_rw', $draftData['questions'][$step - 1]['text_rw'] ?? '') }}</textarea>
                        @error('text_rw')
                            <p class="mt-2 text-sm text-red-600 dark:text-red-400 flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Question Image -->
                    <div class="lg:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Question Image (Optional)
                        </label>
                        <div class="flex items-center space-x-4">
                            <input type="file" 
                                   name="image" 
                                   accept="image/*"
                                   onchange="previewImage(this, 'question-image-preview')"
                                   class="hidden">
                            <button type="button" 
                                    onclick="document.querySelector('input[name=&quot;image&quot;]').click()"
                                    class="px-4 py-2 bg-gray-200 dark:bg-gray-600 text-gray-700 dark:text-gray-300 font-medium rounded-lg hover:bg-gray-300 dark:hover:bg-gray-500 transition-colors flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                Choose Image
                            </button>
                            <div id="question-image-preview" class="image-preview-container">
                                @if(isset($draftData['questions'][$step - 1]['image_url']))
                                    <img src="{{ asset('storage/' . $draftData['questions'][$step - 1]['image_url']) }}" class="image-preview mt-2">
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Answer Options -->
            <div class="mb-8">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                        <svg class="w-5 h-5 mr-2 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                        Answer Options (4 required)
                    </h3>
                    <span class="text-sm text-gray-500 dark:text-gray-400">Mark the correct answer</span>
                </div>

                <div class="space-y-4">
                    <!-- Hidden default radio button to prevent browser auto-selecting first visible option -->
                    <div style="display: none;">
                        <input type="radio" 
                               name="correct_answer" 
                               value="0" 
                               id="default-correct-answer">
                    </div>
                    
                    @foreach (['A', 'B', 'C', 'D'] as $letter => $optionLetter)
                        <div class="flex items-start space-x-3 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg border border-gray-200 dark:border-gray-600">
                            <div class="pt-2">
                                <input type="radio" 
                                       name="correct_answer" 
                                       value="{{ $letter + 1 }}"
                                       {{ old('correct_answer', isset($draftData['questions'][$step - 1]) ? $draftData['questions'][$step - 1]['correct_answer'] + 1 : null) == $letter + 1 ? 'checked' : '' }}
                                       class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 dark:border-gray-600">
                            </div>
                            <div class="w-8 h-8 rounded-full bg-blue-100 dark:bg-blue-900/20 flex items-center justify-center text-blue-600 dark:text-blue-400 font-semibold text-sm flex-shrink-0 mt-2">
                                {{ $optionLetter }}
                            </div>
                            <div class="flex-1 space-y-3">
                                <input type="text" 
                                       name="options[{{ $letter }}][text]" 
                                       value="{{ old('options.' . $letter . '.text', $draftData['questions'][$step - 1]['options'][$letter]['text'] ?? '') }}"
                                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-800 dark:text-white option-input"
                                       placeholder="Option {{ $optionLetter }} text (English)..."
                                       required>
                                <input type="text" 
                                       name="options[{{ $letter }}][text_rw]" 
                                       value="{{ old('options.' . $letter . '.text_rw', $draftData['questions'][$step - 1]['options'][$letter]['text_rw'] ?? '') }}"
                                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-800 dark:text-white option-input"
                                       placeholder="Ibisubizo mu Kinyarwanda..."
                                       required>
                            </div>
                            <div class="flex items-center space-x-2">
                                <input type="file" 
                                       name="options[{{ $letter }}][image]" 
                                       accept="image/*"
                                       onchange="previewOptionImage(this, 'option-image-preview-{{ $letter }}')"
                                       class="hidden">
                                <button type="button" 
                                        onclick="document.querySelector('input[name=&quot;options[{{ $letter }}][image]&quot;]').click()"
                                        class="p-2 bg-gray-100 dark:bg-gray-600 text-gray-600 dark:text-gray-400 rounded hover:bg-gray-200 dark:hover:bg-gray-500 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                </button>
                                <div id="option-image-preview-{{ $letter }}" class="image-preview-container">
                                    @if(isset($draftData['questions'][$step - 1]['options'][$letter]['image_url']))
                                        <img src="{{ asset('storage/' . $draftData['questions'][$step - 1]['options'][$letter]['image_url']) }}" class="image-preview ml-2">
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                @error('options')
                    <p class="mt-2 text-sm text-red-600 dark:text-red-400 flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <!-- Navigation Buttons -->
            <div class="flex justify-between items-center pt-6 border-t border-gray-200 dark:border-gray-700">
                <div>
                    @if ($step > 1)
                        <a href="{{ route('admin.quizzes.create.question', ['step' => $step - 1]) }}" 
                           class="inline-flex items-center px-6 py-3 bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-300 font-medium rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition-all duration-200">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                            </svg>
                            Previous Question
                        </a>
                    @endif
                </div>
                
                <div class="flex items-center space-x-4">
                    <a href="{{ route('admin.quizzes.create.review') }}" 
                       class="text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 font-medium">
                        Save & Review Later
                    </a>
                    
                    @if ($step < 20)
                        <button type="submit" 
                                onclick="return validateCorrectAnswer()"
                                class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-medium rounded-lg shadow-sm hover:shadow-md transition-all duration-200">
                            Save & Next Question
                            <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </button>
                    @else
                        <a href="{{ route('admin.quizzes.create.review') }}" 
                           class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white font-medium rounded-lg shadow-sm hover:shadow-md transition-all duration-200">
                            Review All Questions
                            <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </a>
                    @endif
                </div>
            </div>
        </form>
    </div>

    <!-- Quick Tips -->
    <div class="bg-blue-50 dark:bg-blue-900/20 rounded-xl border border-blue-200 dark:border-blue-800 p-6 fade-in fade-in-delay-3">
        <div class="flex items-start">
            <svg class="w-6 h-6 text-blue-600 dark:text-blue-400 mr-3 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <div>
                <h3 class="text-lg font-semibold text-blue-900 dark:text-blue-100 mb-2">Question {{ $step }} Guidelines</h3>
                <ul class="text-sm text-blue-800 dark:text-blue-200 space-y-1">
                    <li>• Use clear and unambiguous language suitable for driving students</li>
                    <li>• Include relevant traffic scenarios or road safety situations</li>
                    <li>• Ensure only one option is clearly correct</li>
                    <li>• Make incorrect options plausible but clearly wrong</li>
                    <li>• Images can help clarify complex scenarios</li>
                    <li>• Your progress is automatically saved as you work</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function previewImage(input, previewId) {
    const preview = document.getElementById(previewId);
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.innerHTML = `<img src="${e.target.result}" class="image-preview mt-2">`;
        };
        reader.readAsDataURL(input.files[0]);
    }
}

function previewOptionImage(input, previewId) {
    const preview = document.getElementById(previewId);
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.innerHTML = `<img src="${e.target.result}" class="image-preview ml-2">`;
        };
        reader.readAsDataURL(input.files[0]);
    }
}

// Auto-save functionality
let autoSaveTimer = null;

function autoSave() {
    const form = document.querySelector('form[action*="create.question"]');
    console.log('Form found:', form);
    if (!form) {
        console.log('Quiz form not found');
        return;
    }
    const formData = new FormData(form);
    
    // Debug: Log FormData contents
    console.log('FormData entries:');
    let hasData = false;
    for (let [key, value] of formData.entries()) {
        console.log(key, value);
        if (value && value.trim() !== '' && key !== '_token') {
            hasData = true;
        }
    }
    
    // Only save if there's actual data (besides CSRF token)
    if (!hasData) {
        console.log('No data to save, skipping auto-save');
        return;
    }
    
    fetch('{{ route('admin.quizzes.save-draft') }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            updateAutoSaveIndicator(new Date());
        } else {
            console.error('Auto-save failed:', data.message);
        }
    })
    .catch(error => {
        console.error('Auto-save error:', error);
    });
}

function updateAutoSaveIndicator(lastSaved) {
    const indicator = document.getElementById('auto-save-indicator');
    if (!indicator) return;
    
    const now = new Date();
    const diff = Math.floor((now - lastSaved) / 1000);
    
    if (diff < 5) {
        indicator.innerHTML = `<span class="text-green-600 dark:text-green-400">✓ Saved ${diff}s ago</span>`;
    } else if (diff < 60) {
        indicator.innerHTML = `<span class="text-blue-600 dark:text-blue-400">⏱ Saved ${diff}s ago</span>`;
    } else {
        indicator.innerHTML = `<span class="text-gray-500 dark:text-gray-400">📝 Draft from ${Math.floor(diff/60)}m ago</span>`;
    }
}

// Simple validation for correct answer selection
function validateCorrectAnswer() {
    const correctAnswer = document.querySelector('input[name="correct_answer"]:checked');
    
    if (!correctAnswer || !['1', '2', '3', '4'].includes(correctAnswer.value)) {
        alert('You must select a correct answer (A, B, C, or D) before proceeding');
        return false;
    }
    
    return true;
}

// Form validation before submission
function validateForm() {
    const correctAnswer = document.querySelector('input[name="correct_answer"]:checked');
    const questionText = document.querySelector('textarea[name="text"]').value.trim();
    const questionTextRw = document.querySelector('textarea[name="text_rw"]').value.trim();
    const options = document.querySelectorAll('input[name^="options"][name$="[text]"]');
    
    let allOptionsFilled = true;
    let allOptionsRwFilled = true;
    
    options.forEach((option, index) => {
        if (!option.value.trim()) {
            allOptionsFilled = false;
        }
        const rwOption = document.querySelector(`input[name="options[${index}][text_rw]"]`);
        if (rwOption && !rwOption.value.trim()) {
            allOptionsRwFilled = false;
        }
    });
    
    const errors = [];
    
    if (!questionText) {
        errors.push('Question text (English) is required');
    }
    
    if (!questionTextRw) {
        errors.push('Question text (Kinyarwanda) is required');
    }
    
    if (!allOptionsFilled) {
        errors.push('All option texts (English) are required');
    }
    
    if (!allOptionsRwFilled) {
        errors.push('All option texts (Kinyarwanda) are required');
    }
    
    // Check if correct answer is selected and has valid value
    console.log('Correct answer validation:', {
        correctAnswer: correctAnswer,
        correctAnswerValue: correctAnswer ? correctAnswer.value : 'none',
        correctAnswerId: correctAnswer ? correctAnswer.id : 'none'
    });
    
    if (!correctAnswer || !['1', '2', '3', '4'].includes(correctAnswer.value)) {
        errors.push('You must select a correct answer (A, B, C, or D) before proceeding');
    }
    
    if (errors.length > 0) {
        alert('Please fix the following errors:\n\n' + errors.join('\n'));
        return false;
    }
    
    return true;
}

// Add form submission validation
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form[action*="create.question"]');
    if (form) {
        form.addEventListener('submit', function(e) {
            if (!validateForm()) {
                e.preventDefault();
                return false;
            }
        });
        
        form.addEventListener('input', () => {
            clearTimeout(autoSaveTimer);
            autoSaveTimer = setTimeout(autoSave, 3000);
        });
        
        form.addEventListener('change', autoSave);
        
        // Save before page unload
        window.addEventListener('beforeunload', autoSave);
        
        // Save when user switches tabs
        document.addEventListener('visibilitychange', function() {
            if (document.hidden) {
                autoSave();
            }
        });
    }
});
</script>
@endpush
