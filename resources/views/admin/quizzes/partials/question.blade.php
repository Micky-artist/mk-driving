@php
    $questionId = $question->id ?? 'new_' . $questionIndex;
    $questionName = "questions[{$questionIndex}]";
    $isCorrect = isset($question->correct_option_index) ? (int)$question->correct_option_index : -1;
@endphp

<div class="card mb-3 question-container" id="question-{{ $questionIndex }}">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h6 class="mb-0">Question #<span class="question-number">{{ $questionIndex + 1 }}</span></h6>
        <button type="button" class="btn btn-sm btn-outline-danger delete-question">
            <i class="fas fa-trash"></i> Delete
        </button>
    </div>
    <div class="card-body">
        <input type="hidden" name="{{ $questionName }}[id]" value="{{ $questionId }}">
        <input type="hidden" name="{{ $questionName }}[correct_option_index]" value="{{ $isCorrect }}">
        
        <div class="row mb-3">
            <div class="col-md-6">
                <label class="form-label">Question (English) <span class="text-danger">*</span></label>
                <input type="text" class="form-control" 
                       name="{{ $questionName }}[text][en]" 
                       value="{{ old("questions.{$questionIndex}.text.en", $question->text['en'] ?? '') }}" 
                       required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Question (Kinyarwanda) <span class="text-danger">*</span></label>
                <input type="text" class="form-control" 
                       name="{{ $questionName }}[text][rw]" 
                       value="{{ old("questions.{$questionIndex}.text.rw", $question->text['rw'] ?? '') }}" 
                       required>
            </div>
        </div>

        <div class="mb-3">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <label class="form-label mb-0">Options <span class="text-danger">*</span></label>
                <button type="button" class="btn btn-sm btn-outline-primary add-option">
                    <i class="fas fa-plus"></i> Add Option
                </button>
            </div>
            
            <div class="options-container">
                @foreach($question->options as $oIndex => $option)
                    @include('admin.quizzes.partials.option', [
                        'questionIndex' => $questionIndex,
                        'optionIndex' => $oIndex,
                        'option' => (object) $option
                    ])
                @endforeach
            </div>
        </div>
    </div>
</div>
