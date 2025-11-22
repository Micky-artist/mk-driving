<?php

namespace App\Http\Requests\Subscription;

use Illuminate\Foundation\Http\FormRequest;

class SimulatePaymentRequest extends FormRequest
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
            'plan_id' => ['required', 'string', 'exists:subscription_plans,id'],
            'payment_method' => ['required', 'string', 'in:card,mobile_money,bank_transfer'],
            'payment_reference' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:0'],
        ];
    }
}
