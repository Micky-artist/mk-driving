<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create or update admin user
        User::updateOrCreate(
            ['email' => 'admin@mkdriving.com'],
            [
                'first_name' => 'Admin',
                'last_name' => 'User',
                'email' => 'admin@mkdriving.com',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'role' => User::ROLE_ADMIN,
                'is_admin' => true,
                'has_attempted_guest_quiz' => false,
                'phone' => '1234567890',
            ]
        );

        $this->command->info('Admin user created successfully!');
        $this->command->warn('Email: admin@mkdriving.com');
        $this->command->warn('Password: password');
        $this->command->warn('Please change this password after first login!');
    }
}
