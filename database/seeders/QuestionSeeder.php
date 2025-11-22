<?php

namespace Database\Seeders;

use App\Models\Question;
use App\Models\Quiz;
use Illuminate\Database\Seeder;

class QuestionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $quizzes = Quiz::all();
        
        foreach ($quizzes as $quiz) {
            // Get the first word of the English title to determine question count
            $title = json_decode($quiz->title, true)['en'];
            $firstWord = strtolower(explode(' ', $title)[0]);
            
            // Set question count based on the first word of the title
            $questionCount = match($firstWord) {
                'basic' => 10,
                'traffic' => 15, // For 'Traffic Rules and Regulations'
                'defensive' => 20,
                default => 10
            };
            
            for ($i = 1; $i <= $questionCount; $i++) {
                $questionData = [
                    'quiz_id' => $quiz->id,
                    'question_text' => [
                        'en' => 'Sample question ' . $i . ' for ' . $title,
                        'rw' => 'Ikibazo kibanza ' . $i . ' kuri ' . $title
                    ],
                    'type' => 'multiple_choice',
                    'points' => 1,
                    'order' => $i,
                    'explanation' => [
                        'en' => 'This is an explanation for the correct answer.',
                        'rw' => 'Iyi ni ibisobanuro by\'inyishuro nyayo.'
                    ],
                    'is_active' => true
                ];

                // Convert arrays to JSON strings
                $questionData['question_text'] = json_encode($questionData['question_text']);
                $questionData['explanation'] = json_encode($questionData['explanation']);

                $question = Question::updateOrCreate(
                    ['quiz_id' => $quiz->id, 'order' => $i],
                    $questionData
                );

                // Create options for each question
                $this->createOptions($question, $i);
            }
        }
    }

    private function getQuestionText(string $type, int $number, string $quizId): array
    {
        $questions = [
            'basic' => [
                'en' => "Basic road sign question #{$number}",
                'rw' => "Ikibazo cy\'ikimenyetso cy\'umuhanda #{$number}"
            ],
            'traffic' => [
                'en' => "Traffic rules question #{$number}",
                'rw' => "Ikibazo cy\'amategeko y\'umuhanda #{$number}"
            ],
            'defensive' => [
                'en' => "Defensive driving question #{$number}",
                'rw' => "Ikibazo cy\'ukugendera mu kugabana n\'umuhanda #{$number}"
            ]
        ];

        return $questions[$type] ?? $questions['basic'];
    }

    private function createOptions($question, $questionNumber): void
    {
        $options = [
            [
                'option_text' => [
                    'en' => 'Correct answer',
                    'rw' => 'Inyishuro nyayo'
                ],
                'is_correct' => true,
                'order' => 1
            ],
            [
                'option_text' => [
                    'en' => 'Incorrect answer 1',
                    'rw' => 'Inyishuro ntoza 1'
                ],
                'is_correct' => false,
                'order' => 2
            ],
            [
                'option_text' => [
                    'en' => 'Incorrect answer 2',
                    'rw' => 'Inyishuro ntoza 2'
                ],
                'is_correct' => false,
                'order' => 3
            ],
            [
                'option_text' => [
                    'en' => 'Incorrect answer 3',
                    'rw' => 'Inyishuro ntoza 3'
                ],
                'is_correct' => false,
                'order' => 4
            ],
        ];

        // Shuffle options except the first one (correct answer)
        $incorrectOptions = array_slice($options, 1);
        shuffle($incorrectOptions);
        $shuffledOptions = array_merge([$options[0]], $incorrectOptions);

        // Update order after shuffle and save options
        foreach ($shuffledOptions as $index => $option) {
            $optionData = [
                'option_text' => json_encode($option['option_text']),
                'is_correct' => $option['is_correct'],
                'order' => $index + 1
            ];
            
            $question->options()->updateOrCreate(
                [
                    'question_id' => $question->id,
                    'order' => $optionData['order']
                ],
                $optionData
            );
        }
    }
}
