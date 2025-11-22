<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreQuizRequest extends FormRequest
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
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'topics' => 'nullable|array',
            'time_limit_minutes' => 'required|integer|min:1',
            'is_active' => 'boolean',
            'is_guest_quiz' => 'boolean',
            'subscription_plan_id' => 'nullable|exists:subscription_plans,id',
            'questions' => 'required|array|min:1',
            'questions.*.text' => 'required|string',
            'questions.*.options' => 'required|array|min:2',
            'questions.*.options.*.text' => 'required|string',
            'questions.*.options.*.is_correct' => 'required|boolean',
        ];
    }
}
