@php
    $isEdit = isset($quiz);
    $title = $isEdit ? 'Edit Quiz' : 'Create New Quiz';
    $route = $isEdit ? route('admin.quizzes.update', $quiz) : route('admin.quizzes.store');
    $method = $isEdit ? 'PUT' : 'POST';
    
    // Initialize empty quiz if creating new
    $quiz = $quiz ?? new \stdClass();
    $quiz->title = $quiz->title ?? ['en' => '', 'rw' => ''];
    $quiz->description = $quiz->description ?? ['en' => '', 'rw' => ''];
    $quiz->topics = $quiz->topics ?? [];
    $quiz->time_limit_minutes = $quiz->time_limit_minutes ?? 30;
    $quiz->is_active = $quiz->is_active ?? true;
    $quiz->is_guest_quiz = $quiz->is_guest_quiz ?? false;
    $quiz->subscription_plan_id = $quiz->subscription_plan_id ?? null;
    $quiz->questions = $quiz->questions ?? [];
    
    // Initialize empty question if none exists
    if (empty($quiz->questions)) {
        $quiz->questions = [
            [
                'id' => 'new_1',
                'text' => ['en' => '', 'rw' => ''],
                'options' => [
                    ['id' => 'opt_1', 'text' => ['en' => '', 'rw' => ''], 'is_correct' => true],
                    ['id' => 'opt_2', 'text' => ['en' => '', 'rw' => ''], 'is_correct' => false],
                ],
                'correct_option_index' => 0
            ]
        ];
    }
@endphp

@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">{{ $title }}</h1>
        <div>
            <a href="{{ route('admin.quizzes.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Back to Quizzes
            </a>
        </div>
    </div>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ $route }}" method="POST" id="quizForm">
        @csrf
        @if($isEdit) @method('PUT') @endif
        
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Quiz Information</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="title_en" class="form-label">Title (English) <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="title_en" name="title[en]" 
                                   value="{{ old('title.en', $quiz->title['en'] ?? '') }}" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="title_rw" class="form-label">Title (Kinyarwanda) <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="title_rw" name="title[rw]" 
                                   value="{{ old('title.rw', $quiz->title['rw'] ?? '') }}" required>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="description_en" class="form-label">Description (English) <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="description_en" name="description[en]" rows="3" required>{{ old('description.en', $quiz->description['en'] ?? '') }}</textarea>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="description_rw" class="form-label">Description (Kinyarwanda) <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="description_rw" name="description[rw]" rows="3" required>{{ old('description.rw', $quiz->description['rw'] ?? '') }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="topics" class="form-label">Topics <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="topics" name="topics" 
                                   value="{{ old('topics', implode(',', $quiz->topics ?? [])) }}" 
                                   placeholder="e.g. traffic-signs,rules,driving" required>
                            <div class="form-text">Separate multiple topics with commas</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="time_limit_minutes" class="form-label">Time Limit (minutes) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="time_limit_minutes" 
                                   name="time_limit_minutes" min="1" 
                                   value="{{ old('time_limit_minutes', $quiz->time_limit_minutes) }}" required>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" 
                                       value="1" {{ old('is_active', $quiz->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">Active</label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="subscription_plan_id" class="form-label">Subscription Plan</label>
                            <select class="form-select" id="subscription_plan_id" name="subscription_plan_id">
                                <option value="">-- Select Plan (Optional) --</option>
                                @foreach($subscriptionPlans as $plan)
                                    <option value="{{ $plan->id }}" 
                                            {{ old('subscription_plan_id', $quiz->subscription_plan_id) == $plan->id ? 'selected' : '' }}>
                                        {{ $plan->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Guest Quiz</label>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="is_guest_quiz" 
                                       name="is_guest_quiz" value="1" 
                                       {{ old('is_guest_quiz', $quiz->is_guest_quiz) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_guest_quiz">Make this a guest quiz (no subscription required)</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Questions Section -->
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Questions</h5>
                <button type="button" class="btn btn-sm btn-primary" id="addQuestion">
                    <i class="fas fa-plus"></i> Add Question
                </button>
            </div>
            <div class="card-body" id="questionsContainer">
                @foreach($quiz->questions as $qIndex => $question)
                    @include('admin.quizzes.partials.question', [
                        'questionIndex' => $qIndex,
                        'question' => (object) $question
                    ])
                @endforeach
            </div>
        </div>

        <div class="text-end mb-4">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> {{ $isEdit ? 'Update' : 'Create' }} Quiz
            </button>
            <a href="{{ route('admin.quizzes.index') }}" class="btn btn-outline-secondary">
                Cancel
            </a>
        </div>
    </form>
</div>

<!-- Question Template (Hidden) -->
<template id="questionTemplate">
    @include('admin.quizzes.partials.question', [
        'questionIndex' => '{{ '{{' }} questionIndex {{ '}}' }}',
        'question' => (object) [
            'id' => '{{ '{{' }} questionId {{ '}}' }}',
            'text' => ['en' => '', 'rw' => ''],
            'options' => [
                ['id' => '{{ '{{' }} questionId {{ '}}' }}_1', 'text' => ['en' => '', 'rw' => ''], 'is_correct' => true],
                ['id' => '{{ '{{' }} questionId {{ '}}' }}_2', 'text' => ['en' => '', 'rw' => ''], 'is_correct' => false],
            ],
            'correct_option_index' => 0
        ]
    ])
</template>

<!-- Option Template (Hidden) -->
<template id="optionTemplate">
    @include('admin.quizzes.partials.option', [
        'questionIndex' => '{{ '{{' }} questionIndex {{ '}}' }}',
        'optionIndex' => '{{ '{{' }} optionIndex {{ '}}' }}',
        'option' => (object) [
            'id' => '{{ '{{' }} optionId {{ '}}' }}',
            'text' => ['en' => '', 'rw' => ''],
            'is_correct' => false
        ]
    ])
</template>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        let questionCounter = {{ count($quiz->questions) }};
        let optionCounters = {};
        
        // Initialize option counters for existing questions
        @foreach($quiz->questions as $qIndex => $question)
            optionCounters[{{ $qIndex }}] = {{ count($question['options'] ?? []) }};
        @endforeach

        // Add new question
        document.getElementById('addQuestion').addEventListener('click', function() {
            const questionId = 'new_' + (++questionCounter);
            const questionIndex = questionCounter - 1;
            optionCounters[questionIndex] = 2; // Start with 2 options
            
            let template = document.getElementById('questionTemplate').innerHTML
                .replace(/\{\{ questionIndex \}\}/g, questionIndex)
                .replace(/\{\{ questionId \}\}/g, questionId);
                
            const container = document.createElement('div');
            container.innerHTML = template;
            document.getElementById('questionsContainer').appendChild(container.firstElementChild);
            
            // Initialize any JavaScript for the new question
            initializeQuestion(questionIndex);
        });
        
        // Initialize existing questions
        document.querySelectorAll('.question-container').forEach((container, index) => {
            initializeQuestion(index);
        });
        
        function initializeQuestion(questionIndex) {
            const questionContainer = document.querySelector(`#question-${questionIndex}`);
            const optionsContainer = questionContainer.querySelector('.options-container');
            
            // Add option button
            const addOptionBtn = questionContainer.querySelector('.add-option');
            if (addOptionBtn) {
                addOptionBtn.addEventListener('click', function() {
                    addOption(questionIndex, optionsContainer);
                });
            }
            
            // Delete question button
            const deleteQuestionBtn = questionContainer.querySelector('.delete-question');
            if (deleteQuestionBtn) {
                deleteQuestionBtn.addEventListener('click', function() {
                    if (confirm('Are you sure you want to delete this question?')) {
                        questionContainer.remove();
                    }
                });
            }
            
            // Initialize existing options
            const optionElements = questionContainer.querySelectorAll('.option-row');
            optionElements.forEach((optionEl, index) => {
                initializeOption(questionIndex, index, optionEl);
            });
        }
        
        function addOption(questionIndex, container) {
            if (!optionCounters[questionIndex]) {
                optionCounters[questionIndex] = 0;
            }
            
            const optionIndex = optionCounters[questionIndex]++;
            const optionId = `opt_${Date.now()}_${questionIndex}_${optionIndex}`;
            
            let template = document.getElementById('optionTemplate').innerHTML
                .replace(/\{\{ questionIndex \}\}/g, questionIndex)
                .replace(/\{\{ optionIndex \}\}/g, optionIndex)
                .replace(/\{\{ optionId \}\}/g, optionId);
                
            const optionElement = document.createElement('div');
            optionElement.innerHTML = template;
            container.appendChild(optionElement.firstElementChild);
            
            // Initialize the new option
            initializeOption(questionIndex, optionIndex, optionElement.firstElementChild);
        }
        
        function initializeOption(questionIndex, optionIndex, optionElement) {
            // Set correct option radio button
            const radio = optionElement.querySelector('input[type="radio"]');
            if (radio) {
                radio.name = `questions[${questionIndex}][correct_option_index]`;
                radio.value = optionIndex;
                radio.checked = optionElement.dataset.correct === 'true';
                
                radio.addEventListener('change', function() {
                    // Update the hidden input for form submission
                    document.querySelector(`input[name="questions[${questionIndex}][correct_option_index]"]`).value = optionIndex;
                });
            }
            
            // Delete option button
            const deleteBtn = optionElement.querySelector('.delete-option');
            if (deleteBtn) {
                deleteBtn.addEventListener('click', function() {
                    const optionsContainer = optionElement.closest('.options-container');
                    const options = optionsContainer.querySelectorAll('.option-row');
                    
                    if (options.length <= 2) {
                        alert('A question must have at least 2 options.');
                        return;
                    }
                    
                    optionElement.remove();
                    
                    // Re-index options
                    const remainingOptions = optionsContainer.querySelectorAll('.option-row');
                    remainingOptions.forEach((opt, idx) => {
                        const radioInput = opt.querySelector('input[type="radio"]');
                        if (radioInput) {
                            radioInput.value = idx;
                            if (radioInput.checked) {
                                document.querySelector(`input[name="questions[${questionIndex}][correct_option_index]"]`).value = idx;
                            }
                        }
                    });
                });
            }
        }
        
        // Toggle subscription plan field based on guest quiz checkbox
        const guestQuizCheckbox = document.getElementById('is_guest_quiz');
        const subscriptionPlanField = document.getElementById('subscription_plan_id').closest('.mb-3');
        
        function updateSubscriptionPlanField() {
            if (guestQuizCheckbox.checked) {
                subscriptionPlanField.style.opacity = '0.5';
                subscriptionPlanField.querySelector('select').disabled = true;
            } else {
                subscriptionPlanField.style.opacity = '1';
                subscriptionPlanField.querySelector('select').disabled = false;
            }
        }
        
        if (guestQuizCheckbox) {
            guestQuizCheckbox.addEventListener('change', updateSubscriptionPlanField);
            updateSubscriptionPlanField(); // Initial check
        }
    });
</script>
@endpush
