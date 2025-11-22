<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthService
{
    /**
     * Register a new user
     */
    public function register(array $data): array
    {
        if (User::where('email', $data['email'])->exists()) {
            throw ValidationException::withMessages([
                'email' => [__('auth.email_in_use')],
            ]);
        }

        $user = User::create([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'] ?? null,
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => $data['role'] ?? 'user',
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return [
            'user' => $user,
            'token' => $token,
        ];
    }

    /**
     * Login a user
     */
    public function login(array $credentials): array
    {
        if (!Auth::attempt($credentials)) {
            throw ValidationException::withMessages([
                'email' => [__('auth.invalid_credentials')],
            ]);
        }

        $user = User::where('email', $credentials['email'])->firstOrFail();
        $token = $user->createToken('auth_token')->plainTextToken;

        return [
            'user' => $user,
            'token' => $token,
        ];
    }

    /**
     * Logout the current user
     */
    public function logout(): void
    {
        Auth::user()->tokens()->delete();
    }

    /**
     * Get the authenticated user
     */
    public function user(): ?User
    {
        return Auth::user();
    }

    /**
     * Change user password
     */
    public function changePassword(User $user, string $currentPassword, string $newPassword): void
    {
        if (!Hash::check($currentPassword, $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => [__('auth.invalid_current_password')],
            ]);
        }

        $user->update([
            'password' => Hash::make($newPassword),
        ]);
    }
}
