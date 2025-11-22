<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateQuizRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Adjust authorization logic as needed
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'topics' => 'nullable|array',
            'time_limit_minutes' => 'sometimes|integer|min:1',
            'is_active' => 'sometimes|boolean',
            'is_guest_quiz' => 'sometimes|boolean',
            'subscription_plan_id' => 'nullable|exists:subscription_plans,id',
            'questions' => 'sometimes|array|min:1',
            'questions.*.text' => 'required_with:questions|string',
            'questions.*.options' => 'required_with:questions|array|min:2',
            'questions.*.options.*.text' => 'required|string',
            'questions.*.options.*.is_correct' => 'required|boolean',
        ];
    }
}
