@extends('layouts.app')

@section('content')
    @php
    use App\Services\OptionTextService;
    // Use the cleaned quiz data directly from controller
    $formattedQuiz = [
        'id' => $quiz->id,
        'title' => $quiz->getTranslation('title', app()->getLocale()),
        'description' => $quiz->getTranslation('description', app()->getLocale()),
        'time_limit_minutes' => $quiz->time_limit_minutes,
        'questions' => $quiz->questions->map(function($question) {
            return [
                'id' => $question->id,
                'text' => $question->getTranslation('text', app()->getLocale()),
                'image_url' => $question->image_url, // Use cleaned image_url from controller
                'options' => $question->options->map(function($option) {
                    // Use cleaned data directly, don't re-process with asset()
                    return [
                        'id' => $option->id,
                        'text' => $option->getTranslation('option_text', app()->getLocale()),
                        'image_url' => $option->image_url, // Use cleaned image_url from controller
                        'is_correct' => (bool)$option->is_correct,
                        'explanation' => $option->getTranslation('explanation', app()->getLocale())
                    ];
                })->toArray()
            ];
        })->toArray()
    ];
    @endphp

    <x-quiz-with-companion 
        :quiz="$formattedQuiz"
        :attempt="$attempt ?? null"
        :showHeader="true"
        :compactMode="false"
        :allowNavigation="true"
        :showCompanion="true"
    />
@endsection