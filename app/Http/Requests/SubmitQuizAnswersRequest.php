<?php

namespace App\Http\Requests;

use App\Models\QuizAttempt;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SubmitQuizAnswersRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $attempt = $this->route('attempt');
        return $attempt && $this->user()->can('update', $attempt);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        $attempt = $this->route('attempt');
        $quiz = $attempt->quiz;
        $questions = $quiz->questions()->with('options')->get();
        
        $rules = [
            'answers' => ['required', 'array'],
            'answers.*' => ['required', 'exists:options,id'],
        ];

        // Add validation for each question
        foreach ($questions as $question) {
            $validOptionIds = $question->options->pluck('id')->toArray();
            $rules['answers.' . $question->id] = [
                'required_with:answers',
                Rule::in($validOptionIds),
            ];
        }

        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'answers.required' => 'Please provide answers for all questions.',
            'answers.*.required' => 'Please select an answer for all questions.',
            'answers.*.exists' => 'The selected answer is invalid.',
            'answers.*.in' => 'The selected answer is invalid for this question.',
        ];
    }
}
