@extends('layouts.app')

@section('content')
<div class="h-[calc(100vh-4rem)]">
    @php
        // Format quiz data for the unified component
        $formattedQuiz = [
            'id' => $quiz->id,
            'title' => $quiz->getTranslation('title', app()->getLocale()),
            'description' => $quiz->getTranslation('description', app()->getLocale()),
            'time_limit_minutes' => $quiz->time_limit_minutes,
            'questions' => $quiz->questions->map(function($question) {
                return [
                    'id' => $question->id,
                    'text' => $question->getTranslation('text', app()->getLocale()),
                    'image_path' => $question->image_path ? asset('storage/' . $question->image_path) : null,
                    'options' => $question->options->map(function($option) {
                        return [
                            'id' => $option->id,
                            'text' => $option->getTranslation('option_text', app()->getLocale()),
                            'is_correct' => (bool)$option->is_correct,
                            'explanation' => $option->getTranslation('explanation', app()->getLocale())
                        ];
                    })->toArray()
                ];
            })->toArray()
        ];
    @endphp

    @include('components.unified-quiz-taker', [
        'quiz' => $formattedQuiz,
        'attempt' => $attempt ?? null,
        'showHeader' => true,
        'compactMode' => false,
        'allowNavigation' => true
    ])
</div>
@endsection