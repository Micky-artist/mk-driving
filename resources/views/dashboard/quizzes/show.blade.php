@extends('layouts.app')

@section('content')
<div class="h-[calc(100vh-4rem)]">
    @php
        use App\Services\OptionTextService;
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
                        return OptionTextService::processOptionForApi($option);
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