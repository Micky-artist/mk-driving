@php
    $optionId = $option->id ?? 'opt_' . $questionIndex . '_' . $optionIndex;
    $optionName = "questions[{$questionIndex}][options][{$optionIndex}]";
    $isCorrect = isset($question->correct_option_index) ? (int)$question->correct_option_index === (int)$optionIndex : false;
    $option = (object) $option; // Ensure $option is an object
@endphp

<div class="option-row mb-2" data-correct="{{ $isCorrect ? 'true' : 'false' }}">
    <div class="input-group">
        <div class="input-group-text">
            <input class="form-check-input mt-0" type="radio" 
                   name="questions[{{ $questionIndex }}][correct_option_index]"
                   value="{{ $optionIndex }}"
                   {{ $isCorrect ? 'checked' : '' }}
                   required>
        </div>
        
        <input type="hidden" name="{{ $optionName }}[id]" value="{{ $optionId }}">
        
        <div class="col-md-5">
            <input type="text" class="form-control" 
                   name="{{ $optionName }}[text][en]" 
                   placeholder="Option (English)" 
                   value="{{ old("questions.{$questionIndex}.options.{$optionIndex}.text.en", $option->text['en'] ?? '') }}" 
                   required>
        </div>
        
        <div class="col-md-5">
            <input type="text" class="form-control" 
                   name="{{ $optionName }}[text][rw]" 
                   placeholder="Option (Kinyarwanda)" 
                   value="{{ old("questions.{$questionIndex}.options.{$optionIndex}.text.rw", $option->text['rw'] ?? '') }}" 
                   required>
        </div>
        
        <button type="button" class="btn btn-outline-danger delete-option">
            <i class="fas fa-times"></i>
        </button>
    </div>
</div>
