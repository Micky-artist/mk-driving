<?php

namespace App\Console\Commands;

use App\Models\Quiz;
use App\Models\Question;
use App\Models\Option;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ImportDrivingQuestions extends Command
{
    protected $signature = 'quiz:import-json';
    protected $description = 'Import driving test questions from JSON format';

    public function handle()
    {
        try {
            $filePath = __DIR__ . '/questions.json';
            
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

            // Disable foreign key checks
            DB::statement('SET FOREIGN_KEY_CHECKS=0');
            
            // Start transaction
            DB::beginTransaction();
            
            try {
                // Clear existing data
                $this->info('Clearing existing data...');
                Option::truncate();
                Question::truncate();
                Quiz::truncate();
                
                $this->info('Existing data cleared successfully.');

                // Process quizzes
                $guestQuizIndex = 10; // 0-based index for quiz 11
                $this->info(sprintf(
                    'Will create %d quizzes (1 guest quiz at position %d, %d regular)',
                    count($quizzes),
                    $guestQuizIndex + 1,
                    count($quizzes) - 1
                ));

                $adminUser = $this->getOrCreateAdminUser();
                $subscriptionPlan = $this->getSubscriptionPlan();

                // Process each quiz
                foreach ($quizzes as $quizIndex => $quizQuestions) {
                    $this->processQuiz($quizIndex, $quizQuestions, $adminUser, $subscriptionPlan);
                }

                // Commit the transaction if we got this far
                DB::commit();
                $this->info('Successfully imported all quizzes and questions!');
                return 0;
            } catch (\Exception $e) {
                // Rollback the transaction on error
                if (DB::transactionLevel() > 0) {
                    DB::rollBack();
                }
                throw $e; // Re-throw to be caught by the outer catch
            } finally {
                // Re-enable foreign key checks
                DB::statement('SET FOREIGN_KEY_CHECKS=1');
            }
        } catch (\Exception $e) {
            $this->logError($e);
            return 1;
        }
    }

    private function getOrCreateAdminUser()
    {
        $adminUser = User::where('email', 'admin@example.com')->first();
        if (!$adminUser) {
            $adminUser = User::create([
                'first_name' => 'Admin',
                'last_name' => 'User',
                'email' => 'admin@example.com',
                'password' => bcrypt('password'),
                'is_admin' => true
            ]);
        }
        return $adminUser;
    }

    private function getSubscriptionPlan()
    {
        $subscriptionPlan = \App\Models\SubscriptionPlan::first();
        if (!$subscriptionPlan) {
            throw new \RuntimeException('No subscription plan found. Please run the subscription plan seeder first.');
        }
        return $subscriptionPlan;
    }

    private function processQuiz($quizIndex, $quizQuestions, $adminUser, $subscriptionPlan)
    {
        if (!is_array($quizQuestions)) {
            $this->warn("Quiz at index {$quizIndex} is not an array, skipping...");
            return;
        }
        
        $quizNumber = $quizIndex + 1;
        $this->info("Processing quiz {$quizNumber} with " . count($quizQuestions) . " questions");
        
        $isGuestQuiz = $quizIndex === 10; // 0-based index, so 10 is the 11th quiz
        
        $quiz = Quiz::create([
            'title' => $isGuestQuiz 
                ? ['rw' => "Umwitozo bwa mbere", 'en' => "Practice Quiz"]
                : ['rw' => "Umwitozo {$quizNumber}", 'en' => "Quiz {$quizNumber}"],
            'description' => $isGuestQuiz
                ? ['rw' => "Igikorwa cy'umuhanda cya mbere", 'en' => "Practice driving test questions set for guests"]
                : ['rw' => "Igikorwa cy'umuhanda {$quizNumber}", 'en' => "Driving test questions set {$quizNumber}"],
            'time_limit_minutes' => 20,
            'is_active' => true,
            'is_guest_quiz' => $isGuestQuiz,
            'creator_id' => $adminUser->id,
            'subscription_plan_slug' => $subscriptionPlan->slug,
        ]);
        
        $this->info("Created quiz with ID: " . $quiz->id);
        
        foreach ($quizQuestions as $qIndex => $questionData) {
            $this->processQuestion($qIndex, $questionData, $quiz);
        }
    }
                
    private function processQuestion($qIndex, $questionData, $quiz)
    {
        if (!is_array($questionData) || !isset($questionData['question'])) {
            $this->warn("Question at index {$qIndex} is not in the expected format, skipping...");
            return;
        }
        
        try {
            // Skip if question data is not in expected format
            if (!isset($questionData['question']) || !isset($questionData['choices']) || !isset($questionData['correct'])) {
                $this->warn("Skipping malformed question at index {$qIndex}");
                return;
            }

            // Track image count for basic plan (quiz ID 1-15)
            static $basicPlanImageCount = 0;
            $isBasicPlan = $quiz->id <= 15;
            
            // Extract image path from imgpath if it exists
            $imageUrl = null;
            if (!empty($questionData['imgpath']) && is_string($questionData['imgpath'])) {
                // Extract the image path using a more flexible regex
                if (preg_match('/src=[\"\']?([^\s\"\'>]+)/i', $questionData['imgpath'], $matches)) {
                    $imgPath = $matches[1];
                    // Remove any '../' prefix and get just the filename
                    $imageName = basename(str_replace('../', '', $imgPath));
                    
                    // Only process .jpg files from examMedia directory
                    if (str_ends_with(strtolower($imageName), '.jpg') && str_contains(strtolower($imgPath), 'exammedia/')) {
                        // For basic plan, only allow the first 15 images
                        if ($isBasicPlan) {
                            if ($basicPlanImageCount < 15) {
                                $imageUrl = 'examMedia/' . $imageName;  // Keep the directory structure
                                $basicPlanImageCount++;
                                $this->info("Assigned image to basic plan (count: {$basicPlanImageCount}/15): " . $imageName);
                            } else {
                                $this->info("Skipping image for basic plan (limit reached): " . $imageName);
                            }
                        } else {
                            // For other plans, allow all images
                            $imageUrl = 'examMedia/' . $imageName;  // Keep the directory structure
                            $this->info("Assigned image to premium plan: " . $imageName);
                        }
                    } else {
                        $this->info("Skipping non-jpg file or file not in examMedia directory: " . $imageName);
                    }
                }
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
                throw new \RuntimeException('Failed to save question: ' . json_encode($question->getErrors() ?? []));
            }

            // Create options
            $options = [];
            foreach ($questionData['choices'] as $choiceIndex => $choice) {
                if (!empty(trim($choice))) { // Skip empty choices
                    $isCorrect = ($choiceIndex + 1) == $questionData['correct'];
                    $option = Option::create([
                        'question_id' => $question->id,
                        'option_text' => ['rw' => trim($choice)],
                        'is_correct' => $isCorrect,
                        'order' => $choiceIndex,
                    ]);
                    
                    if (!$option) {
                        throw new \RuntimeException("Failed to save option for question");
                    }
                    $options[] = $option;
                }
            }

            // Set the correct answer (correct is 1-based in the data)
            $correctIndex = (int)$questionData['correct'] - 1;
            if (isset($options[$correctIndex])) {
                $question->correct_option_id = $options[$correctIndex]->id;
                if (!$question->save()) {
                    throw new \RuntimeException("Failed to update question with correct option");
                }
            } else {
                $this->warn("Invalid correct answer index for question: {$question->id}");
            }

        } catch (\Exception $e) {
            $errorMsg = sprintf(
                "Error processing question %d in quiz %d: %s\nFile: %s:%d",
                $qIndex + 1,
                $quiz->id,
                $e->getMessage(),
                $e->getFile(),
                $e->getLine()
            );
            $this->error($errorMsg);
            throw $e;
        }
    }
    
    private function logError(\Exception $e)
    {
        $errorMessage = 'Error importing questions: ' . $e->getMessage() . 
                      ' in ' . $e->getFile() . ':' . $e->getLine();
        
        $this->error($errorMessage);
        
        // Log the full error for debugging
        Log::error($errorMessage, [
            'exception' => (string) $e,
            'trace' => $e->getTraceAsString()
        ]);
    }
}
