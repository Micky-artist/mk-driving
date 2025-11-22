<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSubscriptionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'subscription_plan_id' => ['required', 'exists:subscription_plans,id'],
            'status' => ['required', 'string', Rule::in(['active', 'expired', 'cancelled', 'pending'])],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after:start_date'],
            'amount' => ['required', 'numeric', 'min:0'],
        ];
    }
}
