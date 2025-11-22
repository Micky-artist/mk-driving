<?php

namespace Database\Seeders;

use App\Models\ForumAnswer;
use App\Models\ForumQuestion;
use App\Models\User;
use Illuminate\Database\Seeder;

class ForumAnswerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $questions = ForumQuestion::all();
        $users = User::take(10)->get();
        
        $answers = [
            [
                'en' => 'That\'s a great question! Here\'s what I\'ve found works best...',
                'rw' => 'Ibi ni ibibazo byiza! Dore ibyo nashoboye kubona bishobora kugufasha...'
            ],
            [
                'en' => 'I had the same issue when I was starting out. My advice would be...',
                'rw' => 'Narwaye nk\'ibyo nigeze kugira iyo nari ntangira. Inama ngiye kuguhaye ni uko...'
            ],
            [
                'en' => 'According to the official driving manual, the correct approach is...',
                'rw' => 'Nk\'uko biri mu itegeko ry\'imodoka, uko ukwiye gukora ni uko...'
            ],
            [
                'en' => 'I\'ve been driving for 10 years and here\'s what I recommend...',
                'rw' => 'Nkaba nkoresha imodoka imyaka 10 kandi ibyo nshobora kuguhanga ni...'
            ],
            [
                'en' => 'There are a few techniques you can try...',
                'rw' => 'Hari uburyo butandukanye ushobora kugerageza...'
            ],
        ];
        
        foreach ($questions as $question) {
            // Create 2-5 answers for each question
            $answerCount = rand(2, 5);
            
            for ($i = 0; $i < $answerCount; $i++) {
                $user = $users->random();
                $answer = $answers[array_rand($answers)];
                
                ForumAnswer::create([
                    'question_id' => $question->id,
                    'user_id' => $user->id,
                    'content' => json_encode($answer),
                    'is_approved' => true,
                    'parent_id' => null, // Top-level answer
                ]);
            }
        }
    }
}
