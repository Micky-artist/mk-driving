<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class UpdateUserRequest extends FormRequest
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
        $user = $this->route('user') ?? Auth::id();
        
        return [
            'first_name' => ['sometimes', 'string', 'max:255'],
            'last_name' => ['sometimes', 'string', 'max:255'],
            'email' => [
                'sometimes',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user),
            ],
            'phone' => ['nullable', 'string', 'max:20'],
            'role' => ['sometimes', 'string', 'in:admin,instructor,student'],
            'password' => ['sometimes', 'string', 'min:8', 'confirmed'],
            'profile_image' => ['nullable', 'string'],
            'is_active' => ['sometimes', 'boolean'],
            'has_attempted_guest_quiz' => ['sometimes', 'boolean'],
        ];
    }
}
