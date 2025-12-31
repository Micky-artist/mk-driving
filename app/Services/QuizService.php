<?php

namespace App\Services;

use App\Models\Quiz;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use App\Services\OptionTextService;

class QuizService
{
    public function __construct()
    {
        // Constructor if needed
    }

    public function createQuiz(array $data, $userId, array $optionImages = [])
    {
        // Process option images if provided
        $imageUrls = [];
        
        foreach ($optionImages as $image) {
            if ($image instanceof UploadedFile) {
                $path = $image->store('public/quiz-options');
                $imageUrls[] = Storage::url($path);
            }
        }

        // Create the quiz
        $quiz = Quiz::create([
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'topics' => $data['topics'] ?? null,
            'time_limit_minutes' => $data['time_limit_minutes'],
            'is_active' => $data['is_active'] ?? false,
            'is_guest_quiz' => $data['is_guest_quiz'] ?? false,
            'creator_id' => $userId,
            'subscription_plan_id' => $data['subscription_plan_id'] ?? null,
        ]);

        return $quiz;
    }

    public function updateQuiz(Quiz $quiz, array $data, array $optionImages = [])
    {
        // Handle image updates if needed
        // ...
        
        $quiz->update($data);
        return $quiz->fresh();
    }

    public function deleteQuiz(Quiz $quiz)
    {
        // Handle any cleanup (like deleting associated files)
        // ...
        
        return $quiz->delete();
    }

    public function getQuizWithQuestions($id)
    {
        $quiz = Quiz::with('questions.options')->findOrFail($id);
        
        // Process questions and options to clean HTML and add image URLs
        $quiz->questions->each(function($question) {
            $question->options->each(function($option) {
                // Use OptionTextService to process the option
                $processedOption = OptionTextService::processOptionForApi($option);
                
                // Add processed data as attributes
                $option->clean_text = $processedOption['text'];
                $option->image_url = $processedOption['image_url'];
            });
        });
        
        return $quiz;
    }

    // Add other business logic methods from the NestJS service
}
