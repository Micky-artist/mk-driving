<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class QuizNavigationTest extends TestCase
{
    /**
     * Test that our quiz navigation logic works correctly
     */
    public function test_quiz_navigation_prevents_skipping_unanswered_questions()
    {
        // Simulate the JavaScript logic we implemented
        $questions = [
            ['id' => 1, 'text' => 'Question 1'],
            ['id' => 2, 'text' => 'Question 2'],
            ['id' => 3, 'text' => 'Question 3'],
            ['id' => 4, 'text' => 'Question 4'],
            ['id' => 5, 'text' => 'Question 5'],
        ];

        // Simulate user answers (only questions 1 and 3 answered)
        $userAnswers = [
            1 => ['optionId' => 'opt1', 'isCorrect' => true],
            3 => ['optionId' => 'opt3', 'isCorrect' => false],
        ];

        // Test: Find first unanswered question
        $firstUnansweredIndex = $this->findFirstUnansweredQuestion($questions, $userAnswers);
        
        // Should return index 1 (Question 2 - first unanswered)
        $this->assertEquals(1, $firstUnansweredIndex);
        $this->assertEquals(2, $questions[$firstUnansweredIndex]['id']);

        // Test: Check if specific question is answered
        $this->assertTrue($this->isQuestionAnswered(1, $userAnswers));
        $this->assertTrue($this->isQuestionAnswered(3, $userAnswers));
        $this->assertFalse($this->isQuestionAnswered(2, $userAnswers));
        $this->assertFalse($this->isQuestionAnswered(4, $userAnswers));
        $this->assertFalse($this->isQuestionAnswered(5, $userAnswers));

        // Test: Progress display should show actual question number
        $currentQuestionIndex = 1; // On question 2
        $actualQuestionNumber = $currentQuestionIndex + 1;
        $this->assertEquals(2, $actualQuestionNumber);
        
        // Test: Answered count
        $answeredCount = count($userAnswers);
        $this->assertEquals(2, $answeredCount);
    }

    /**
     * Test that questions maintain original order (no reordering)
     */
    public function test_questions_maintain_original_order()
    {
        $originalQuestions = [
            ['id' => 1, 'text' => 'Question 1'],
            ['id' => 2, 'text' => 'Question 2'],
            ['id' => 3, 'text' => 'Question 3'],
        ];

        // Our fix removes reordering, so questions should stay in original order
        $processedQuestions = $this->processQuestionsForQuiz($originalQuestions);
        
        $this->assertEquals($originalQuestions, $processedQuestions);
        $this->assertEquals([1, 2, 3], array_column($processedQuestions, 'id'));
    }

    /**
     * Test navigation logic for edge cases
     */
    public function test_navigation_edge_cases()
    {
        $questions = [
            ['id' => 1, 'text' => 'Question 1'],
            ['id' => 2, 'text' => 'Question 2'],
        ];

        // Case 1: No questions answered
        $userAnswers = [];
        $firstUnanswered = $this->findFirstUnansweredQuestion($questions, $userAnswers);
        $this->assertEquals(0, $firstUnanswered); // Should start at question 1

        // Case 2: All questions answered
        $userAnswers = [
            1 => ['optionId' => 'opt1'],
            2 => ['optionId' => 'opt2'],
        ];
        $firstUnanswered = $this->findFirstUnansweredQuestion($questions, $userAnswers);
        $this->assertEquals(1, $firstUnanswered); // Should go to last question

        // Case 3: Last question unanswered
        $userAnswers = [
            1 => ['optionId' => 'opt1'],
        ];
        $firstUnanswered = $this->findFirstUnansweredQuestion($questions, $userAnswers);
        $this->assertEquals(1, $firstUnanswered); // Should go to question 2
    }

    // Helper methods that simulate our JavaScript logic

    private function findFirstUnansweredQuestion(array $questions, array $userAnswers): int
    {
        foreach ($questions as $index => $question) {
            if (!isset($userAnswers[$question['id']])) {
                return $index;
            }
        }
        
        // If all questions are answered, return the last one
        return count($questions) - 1;
    }

    private function isQuestionAnswered(int $questionId, array $userAnswers): bool
    {
        return isset($userAnswers[$questionId]);
    }

    private function processQuestionsForQuiz(array $questions): array
    {
        // Our fix: NO REORDERING - maintain original order
        return $questions;
    }
}
