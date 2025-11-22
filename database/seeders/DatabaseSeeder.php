<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            SubscriptionPlanSeeder::class,
            UserSeeder::class,
            AdminUserSeeder::class,
            QuizSeeder::class,
            QuestionSeeder::class,
            NewsSeeder::class,
            BlogSeeder::class,
            ForumQuestionSeeder::class,
            ForumAnswerSeeder::class,
        ]);
    }
}
