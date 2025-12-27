<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PointConfiguration;

class PointConfigurationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $configurations = [
            [
                'activity_type' => 'quiz_completed',
                'points' => 5, // Base points - kept at 5
                'is_active' => true,
                'conditions' => [
                    'cooldown_hours' => 0, // No cooldown for quiz completion
                ],
            ],
            [
                'activity_type' => 'quiz_passed',
                'points' => 5, // Kept at 5 for intuition
                'is_active' => true,
                'conditions' => [
                    'cooldown_hours' => 0,
                ],
            ],
            [
                'activity_type' => 'quiz_perfect',
                'points' => 10, // Kept at 10 for intuition
                'is_active' => true,
                'conditions' => [
                    'cooldown_hours' => 0,
                ],
            ],
            [
                'activity_type' => 'question_asked',
                'points' => 5, // Kept at 5 for intuition
                'is_active' => true,
                'conditions' => [
                    'cooldown_hours' => 1, // 1 hour cooldown between questions
                ],
            ],
            [
                'activity_type' => 'question_answered',
                'points' => 5, // Kept at 5 for intuition
                'is_active' => true,
                'conditions' => [
                    'cooldown_hours' => 0.5, // 30 minutes cooldown between answers
                ],
            ],
            [
                'activity_type' => 'best_answer_selected',
                'points' => 5, // Kept at 5 for intuition
                'is_active' => true,
                'conditions' => [
                    'cooldown_hours' => 0,
                ],
            ],
            [
                'activity_type' => 'daily_visit',
                'points' => 5, // Kept at 5 for intuition
                'is_active' => true,
                'conditions' => [
                    'cooldown_hours' => 24, // Once per day
                ],
            ],
        ];

        foreach ($configurations as $config) {
            PointConfiguration::updateOrCreate(
                ['activity_type' => $config['activity_type']],
                $config
            );
        }
    }
}
