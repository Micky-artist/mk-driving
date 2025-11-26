<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SubscriptionPlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $subscriptionPlans = [
            [
                'name' => json_encode([
                    'en' => 'Basic Plan',
                    'rw' => 'Paka y\'Umunsi'
                ]),
                'description' => json_encode([
                    'en' => 'Access to 15 quizzes for 24 hours',
                    'rw' => 'Gerageza ibizamini 15 mu masaha 24'
                ]),
                'price' => 1000,
                'slug' => 'basic-plan',
                'features' => json_encode([
                    'en' => ['15 quizzes', '24 hours'],
                    'rw' => ['ibizamini 15', 'amasaha 24']
                ]),
                'duration' => 1,
                'duration_in_days' => 1,
                'max_quizzes' => 15,
                'is_active' => true,
                'color' => '#3b82f6',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => json_encode([
                    'en' => 'Standard Plan',
                    'rw' => 'Paka y\'Icyumweru'
                ]),
                'description' => json_encode([
                    'en' => 'Access to 25 quizzes for 7 days',
                    'rw' => 'Gerageza ibizamini 25 mu minsi 7'
                ]),
                'price' => 2000,
                'slug' => 'standard-plan',
                'features' => json_encode([
                    'en' => ['25 quizzes', '7 days'],
                    'rw' => ['ibizamini 25', 'iminsi 7']
                ]),
                'duration' => 7,
                'duration_in_days' => 7,
                'max_quizzes' => 25,
                'is_active' => true,
                'color' => '#10b981',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => json_encode([
                    'en' => 'Premium Plan',
                    'rw' => 'Paka y\'Ukwezi'
                ]),
                'description' => json_encode([
                    'en' => 'Unlimited quizzes for 1 month',
                    'rw' => 'Hozaho ku ibizamini byose mu kwezi 1'
                ]),
                'price' => 10000,
                'slug' => 'premium-plan',
                'features' => json_encode([
                    'en' => ['Unlimited quizzes', '1 month'],
                    'rw' => ['Ibizamini byose', 'Ukwezi 1']
                ]),
                'duration' => 30,
                'duration_in_days' => 30,
                'max_quizzes' => 0, // 0 means unlimited
                'is_active' => true,
                'color' => '#8b5cf6',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => json_encode([
                    'en' => 'Gold Unlimited Plan',
                    'rw' => "Paka y'Ubudashira"
                ]),
                'description' => json_encode([
                    'en' => 'Unlimited access with no time limit',
                    'rw' => 'Hozaho ku ikizamini byose ubudashira'
                ]),
                'price' => 25000,
                'slug' => 'gold-unlimited-plan',
                'features' => json_encode([
                    'en' => ['unlimited quizzes', 'no limit'],
                    'rw' => ['ibizamini byose', 'ubudashira']
                ]),
                'duration' => 0, // 0 means no expiration
                'duration_in_days' => 9999, // Large number to represent unlimited duration
                'max_quizzes' => 0, // 0 means unlimited
                'is_active' => true,
                'color' => '#f59e0b',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($subscriptionPlans as $plan) {
            DB::table('subscription_plans')->updateOrInsert(
                ['slug' => $plan['slug']],
                $plan
            );
        }

        $this->command->info('Subscription plans seeded successfully!');
    }
}
