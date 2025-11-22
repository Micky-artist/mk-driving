<?php

namespace Database\Seeders;

use App\Models\SubscriptionPlan;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SubscriptionPlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $plans = [
            [
                // Auto-incrementing ID
                'name' => json_encode(['en' => 'Basic Plan', 'rw' => 'Paka ya Nyarutsa']),
                'description' => json_encode([
                    'en' => 'Access to 15 quizzes for 24 hours',
                    'rw' => 'Gerageza ibizamini 15 mu masaha 24',
                ]),
                'price' => 1000,
                'max_quizzes' => 15,
                'duration' => 1,
                'is_active' => true,
                'features' => json_encode([
                    'en' => ['15 quizzes', '24 hours'],
                    'rw' => ['ibizamini 15', 'amasaha 24'],
                ]),
                'color' => '#3B82F6',
            ],
            [
                // Auto-incrementing ID
                'name' => json_encode(['en' => 'Standard Plan', 'rw' => 'Paka ya Zindaro']),
                'description' => json_encode([
                    'en' => 'Access to 25 quizzes for 7 days',
                    'rw' => 'Gerageza ibizamini 25 mu minsi 7',
                ]),
                'price' => 2000,
                'max_quizzes' => 25,
                'duration' => 7,
                'is_active' => true,
                'features' => json_encode([
                    'en' => ['25 quizzes', '7 days'],
                    'rw' => ['ibizamini 25', 'iminsi 7'],
                ]),
                'color' => '#8B5CF6',
            ],
            [
                // Auto-incrementing ID
                'name' => json_encode(['en' => 'Premium Plan', 'rw' => 'Paka ya Hozaho']),
                'description' => json_encode([
                    'en' => 'Unlimited quizzes for 1 month',
                    'rw' => 'Hozaho ku ibizamini byose mu kwezi 1',
                ]),
                'price' => 10000,
                'max_quizzes' => 0, // 0 means unlimited
                'duration' => 30,
                'is_active' => true,
                'features' => json_encode([
                    'en' => ['Unlimited quizzes', '1 month'],
                    'rw' => ['Ibizamini byose', 'Ukwezi 1'],
                ]),
                'color' => '#10B981',
            ],
            [
                // Auto-incrementing ID
                'name' => json_encode(['en' => 'Gold Unlimited Plan', 'rw' => "Paka y'Ubudashira"]),
                'description' => json_encode([
                    'en' => 'Unlimited access with no time limit',
                    'rw' => 'Hozaho ku ikizamini byose ubudashira',
                ]),
                'price' => 25000,
                'max_quizzes' => 0, // 0 means unlimited
                'duration' => 0, // 0 means no expiration
                'is_active' => true,
                'features' => json_encode([
                    'en' => ['unlimited quizzes', 'no limit'],
                    'rw' => ['ibizamini byose', 'ubudashira'],
                ]),
                'color' => '#F59E0B',
            ],
        ];

        foreach ($plans as $planData) {
            // Remove any ID that might be in the array
            unset($planData['id']);
            
            // Set a default slug based on the plan name if not provided
            if (!isset($planData['slug'])) {
                $name = json_decode($planData['name'], true)['en'] ?? 'plan';
                $planData['slug'] = strtolower(str_replace(' ', '-', $name));
            }
            
            // Use firstOrCreate to avoid duplicates based on slug
            SubscriptionPlan::firstOrCreate(
                ['slug' => $planData['slug']],
                $planData
            );
        }
    }
}
