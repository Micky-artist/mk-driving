<?php

namespace App\Console\Commands;

use App\Models\Quiz;
use App\Models\Question;
use App\Models\Option;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;

class ImportDrivingQuestions extends Command
{
    protected $signature = 'quiz:import-json';
    protected $description = 'Import driving test questions from JSON format';

    public function handle()
    {
        $filePath = __DIR__ . '/Questions.json';
        
        if (!File::exists($filePath)) {
            $this->error("The file {$filePath} does not exist.");
            return 1;
        }

        $jsonContent = File::get($filePath);
        $quizzes = json_decode($jsonContent, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->error('JSON decode error: ' . json_last_error_msg());
            return 1;
        }

        if (empty($quizzes)) {
            $this->error('The questions array is empty');
            return 1;
        }

        $this->info(sprintf('Found %d quizzes', count($quizzes)));
        
        // Debug: Show structure of first quiz and its first question
        if (!empty($quizzes[0])) {
            $this->info('First quiz structure: ' . json_encode(array_keys($quizzes[0])));
            if (is_array($quizzes[0]) && !empty($quizzes[0][0])) {
                $this->info('First question in first quiz: ' . json_encode(array_keys($quizzes[0][0])));
            }
        }

        // Start database transaction
        DB::beginTransaction();

        try {
            // Clear existing data
            Option::query()->delete();
            Question::query()->delete();
            Quiz::query()->delete();

            // Get or create admin user
            $adminUser = User::where('email', 'admin@example.com')->first();
            if (!$adminUser) {
                $adminUser = User::create([
                    'name' => 'Admin',
                    'email' => 'admin@example.com',
                    'password' => bcrypt('password'),
                    'is_admin' => true
                ]);
            }

            // Process each quiz in the JSON
            foreach ($quizzes as $quizIndex => $quizQuestions) {
                if (!is_array($quizQuestions)) {
                    $this->warn("Quiz at index {$quizIndex} is not an array, skipping...");
                    continue;
                }
                
                $quizNumber = $quizIndex + 1;
                $this->info("Processing quiz {$quizNumber} with " . count($quizQuestions) . " questions");

                // Get a valid subscription plan
                $subscriptionPlan = \App\Models\SubscriptionPlan::first();
                
                if (!$subscriptionPlan) {
                    $this->error('No subscription plan found. Please run the subscription plan seeder first.');
                    return 1;
                }
                
                $this->info("Creating quiz with subscription plan ID: " . $subscriptionPlan->id);
                
                // Create the quiz with auto-incrementing ID
                $quiz = new Quiz([
                    'title' => ['rw' => "Umwitozo {$quizNumber}"],
                    'description' => ['rw' => "Igikorwa cy'umuhanda {$quizNumber}"],
                    'time_limit_minutes' => 20,
                    'is_active' => true,
                    'is_guest_quiz' => $quizIndex === 0, // Only first quiz is guest quiz
                    'creator_id' => $adminUser->id,
                    'subscription_plan_slug' => $subscriptionPlan->slug, // Use slug instead of ID
                ]);
                
                if (!$quiz->save()) {
                    $this->error('Failed to save quiz');
                    if ($quiz->errors) {
                        $this->error(json_encode($quiz->errors));
                    }
                    continue;
                }
                
                $this->info("Created quiz with ID: " . $quiz->id);

                // Process each question
                foreach ($quizQuestions as $qIndex => $questionData) {
                    if (!is_array($questionData) || !isset($questionData['question'])) {
                        $this->warn("Question at index {$qIndex} is not in the expected format, skipping...");
                        continue;
                    }
                    
                    try {
                        // Skip if question data is not in expected format
                        if (!isset($questionData['question']) || !isset($questionData['choices']) || !isset($questionData['correct'])) {
                            $this->warn("Skipping malformed question at index {$qIndex}");
                            continue;
                        }

                        // Extract image path from imgpath if it exists
                        $imageUrl = null;
                        if (!empty($questionData['imgpath']) && 
                            is_string($questionData['imgpath']) &&
                            preg_match('/src=[\'"]([^\'"]+)[\'"]/', $questionData['imgpath'], $matches)) {
                            $imageUrl = basename($matches[1]);
                        }

                        // Create the question
                        $this->info("Creating question: " . substr(trim($questionData['question']), 0, 50) . "...");
                        
                        $question = new Question([
                            'quiz_id' => $quiz->id,
                            'text' => ['rw' => trim($questionData['question'])],
                            'type' => 'multiple_choice',
                            'points' => 1,
                            'is_active' => true,
                            'image_url' => $imageUrl,
                        ]);
                        
                        if (!$question->save()) {
                            $this->error('Failed to save question: ' . json_encode($question->getErrors()));
                            continue;
                        }

                        // Create options
                        $options = [];
                        foreach ($questionData['choices'] as $choiceIndex => $choice) {
                            if (!empty(trim($choice))) { // Skip empty choices
                                $isCorrect = ($choiceIndex + 1) == $questionData['correct'];
                                $option = new Option([
                                    'question_id' => $question->id,
                                    'option_text' => ['rw' => trim($choice)],
                                    'is_correct' => $isCorrect,
                                    'order' => $choiceIndex,
                                ]);
                                $option->save();
                                
                                if (!$option) {
                                    throw new \Exception("Failed to save option for question");
                                }
                                $options[] = $option;
                            }
                        }

                        // Set the correct answer (correct is 1-based in the data)
                        $correctIndex = (int)$questionData['correct'] - 1;
                        if (isset($options[$correctIndex])) {
                            $question->correct_option_id = $options[$correctIndex]->id;
                            if (!$question->save()) {
                                throw new \Exception("Failed to update question with correct option");
                            }
                        } else {
                            $this->warn("Invalid correct answer index for question: {$question->id}");
                        }

                    } catch (\Exception $e) {
                        $this->error("Error processing question: " . $e->getMessage());
                        DB::rollBack();
                        return 1;
                    }
                }
            }

            DB::commit();
            $this->info('Successfully imported all quizzes and questions!');
            return 0;

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('Error importing questions: ' . $e->getMessage());
            $this->error($e->getTraceAsString());
            return 1;
        }
    }
}
