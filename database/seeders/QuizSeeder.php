<?php

namespace Database\Seeders;

use App\Models\Quiz;
use App\Models\SubscriptionPlan;
use Illuminate\Database\Seeder;

class QuizSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $basicPlan = SubscriptionPlan::find('basic');
        $standardPlan = SubscriptionPlan::find('standard');
        $premiumPlan = SubscriptionPlan::find('premium');
        
        if (!$basicPlan || !$standardPlan || !$premiumPlan) {
            $this->command->error('Please run SubscriptionPlanSeeder first!');
            return;
        }
        
        $quizzes = [
            [
                'title' => json_encode(['en' => 'Basic Road Signs', 'rw' => 'Ibihekanezo by\'umuhanda']),
                'description' => json_encode([
                    'en' => 'Test your knowledge of basic road signs',
                    'rw' => 'Gerageza ubumenyi bwawe ku bihekanezo by\'umuhanda'
                ]),
                'topics' => json_encode([
                    'en' => ['Road Signs', 'Traffic Rules'],
                    'rw' => ['Ibihekanezo by\'umuhanda', 'Amategeko y\'umuhanda']
                ]),
                'time_limit_minutes' => 30,
                'is_active' => true,
                'is_guest_quiz' => true,
                'creator_id' => 1, // Assuming admin user has ID 1
                'subscription_plan_id' => $basicPlan->id,
            ],
            [
                'title' => json_encode(['en' => 'Traffic Rules and Regulations', 'rw' => 'Amategeko n\'amabwiriza y\'umuhanda']),
                'description' => json_encode([
                    'en' => 'Test your knowledge of traffic rules and regulations',
                    'rw' => 'Gerageza ubumenyi bwawe ku mategeko n\'amabwiriza y\'umuhanda'
                ]),
                'topics' => json_encode([
                    'en' => ['Traffic Laws', 'Driving Rules'],
                    'rw' => ['Amategeko y\'umuhanda', 'Amabwiriza yo kugendera']
                ]),
                'time_limit_minutes' => 45,
                'is_active' => true,
                'is_guest_quiz' => false,
                'creator_id' => 1,
                'subscription_plan_id' => $standardPlan->id,
            ],
            [
                'title' => json_encode(['en' => 'Defensive Driving', 'rw' => 'Kugendera mu kugabana n\'umuhanda']),
                'description' => json_encode([
                    'en' => 'Advanced test on defensive driving techniques',
                    'rw' => 'Gerageza ubumenyi bwawe ku migenzo myiza yo kugendera mu kugabana n\'umuhanda'
                ]),
                'topics' => json_encode([
                    'en' => ['Defensive Driving', 'Safety'],
                    'rw' => ['Kugendera mu kugabana n\'umuhanda', 'Ukugabana n\'umuhanda neza']
                ]),
                'time_limit_minutes' => 60,
                'is_active' => true,
                'is_guest_quiz' => false,
                'creator_id' => 1,
                'subscription_plan_id' => $premiumPlan->id,
            ],
        ];

        foreach ($quizzes as $quiz) {
            Quiz::updateOrCreate(
                ['title' => $quiz['title']],
                $quiz
            );
        }
    }
}
