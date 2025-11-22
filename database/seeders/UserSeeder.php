<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create regular users if they don't exist
        if (User::count() === 0) {
            User::factory(20)->create();
        }

        // Create or update test user
        User::updateOrCreate(
            ['email' => 'user@example.com'],
            [
                'first_name' => 'Test',
                'last_name' => 'User',
                'email' => 'user@example.com',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'role' => 'USER',
                'is_admin' => false,
                'has_attempted_guest_quiz' => false,
            ]
        );
    }
}
