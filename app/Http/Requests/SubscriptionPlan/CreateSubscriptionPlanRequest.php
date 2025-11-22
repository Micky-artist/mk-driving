<?php

namespace App\Http\Requests\SubscriptionPlan;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateSubscriptionPlanRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Authorization is handled by middleware
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name_en' => ['required', 'string', 'max:255'],
            'name_rw' => ['nullable', 'string', 'max:255'],
            'description_en' => ['nullable', 'string'],
            'description_rw' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'duration' => ['required', 'integer', 'min:1'], // in days
            'features' => ['nullable', 'array'],
            'features.*' => ['string'],
            'is_active' => ['boolean'],
            'max_quizzes' => ['nullable', 'integer', 'min:0'],
            'color' => ['nullable', 'string', 'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'],
        ];
    }
}
