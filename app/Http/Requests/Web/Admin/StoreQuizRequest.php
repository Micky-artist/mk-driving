<?php

namespace App\Http\Requests\Web\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreQuizRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'title' => 'required|array',
            'title.en' => 'required|string|max:255',
            'title.rw' => 'required|string|max:255',
            'description' => 'required|array',
            'description.en' => 'required|string',
            'description.rw' => 'required|string',
            'topics' => 'required|array',
            'topics.*' => 'string|max:255',
            'time_limit_minutes' => 'required|integer|min:1',
            'is_active' => 'boolean',
            'is_guest_quiz' => 'boolean',
            'subscription_plan_id' => 'nullable|exists:subscription_plans,id',
            'questions' => 'required|array|min:1',
            'questions.*.text' => 'required|array',
            'questions.*.text.en' => 'required|string',
            'questions.*.text.rw' => 'required|string',
            'questions.*.options' => 'required|array|min:2',
            'questions.*.options.*.text' => 'required|array',
            'questions.*.options.*.text.en' => 'required|string',
            'questions.*.options.*.text.rw' => 'required|string',
            'questions.*.correct_option_index' => 'required|integer|min:0',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $data = $this->all();
            
            // Validate that either subscription_plan_id is provided or is_guest_quiz is true
            if (empty($data['subscription_plan_id']) && !($data['is_guest_quiz'] ?? false)) {
                $validator->errors()->add('subscription_plan_id', 'The quiz must be associated with a subscription plan or marked as a guest quiz.');
            }

            // Validate that correct_option_index is within bounds
            foreach ($data['questions'] ?? [] as $index => $question) {
                $optionCount = count($question['options'] ?? []);
                if (isset($question['correct_option_index']) && $question['correct_option_index'] >= $optionCount) {
                    $validator->errors()->add("questions.{$index}.correct_option_index", 'The selected correct option is invalid.');
                }
            }
        });
    }
}
