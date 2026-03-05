<?php

namespace App\Http\Requests\Forum;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreQuestionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['sometimes', 'required_without:question', 'string', 'max:255'],
            'content' => ['sometimes', 'required_without:question', 'string'],
            'question' => ['sometimes', 'required_without:title,content', 'string', 'max:500'],
            'quiz_id' => ['sometimes', 'nullable', 'exists:quizzes,id'],
            'topics' => ['sometimes', 'array'],
            'topics.*' => ['string', 'max:50'],
        ];
    }
}
