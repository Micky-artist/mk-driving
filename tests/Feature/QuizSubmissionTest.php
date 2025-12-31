<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Quiz;
use App\Models\Question;
use App\Models\Option;
use App\Models\QuizAttempt;
use App\Models\UserAnswer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QuizSubmissionTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Quiz $quiz;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::factory()->create();
        $this->quiz = Quiz::factory()->create([
            'title' => 'Test Quiz',
            'time_limit_minutes' => 30,
            'passing_score' => 70
        ]);

        // Create 3 questions with options
        for ($i = 1; $i <= 3; $i++) {
            $question = Question::factory()->create([
                'quiz_id' => $this->quiz->id,
                'text' => "Question {$i}",
                'question_type' => 'multiple_choice'
            ]);

            // Create options (1 correct, 3 incorrect)
            for ($j = 1; $j <= 4; $j++) {
                Option::factory()->create([
                    'question_id' => $question->id,
                    'text' => "Option {$j}",
                    'is_correct' => $j === 1 // First option is correct
                ]);
            }
        }
    }

    /** @test */
    public function it_can_start_a_quiz_attempt()
    {
        $response = $this->actingAs($this->user)
            ->postJson('/api/quizzes/start', ['quizId' => $this->quiz->id]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'attempt' => [
                    'id',
                    'quiz_id',
                    'status',
                    'answers'
                ]
            ]);

        $this->assertDatabaseHas('quiz_attempts', [
            'user_id' => $this->user->id,
            'quiz_id' => $this->quiz->id,
            'status' => 'in_progress'
        ]);
    }

    /** @test */
    public function it_can_submit_answers_during_quiz()
    {
        // Start an attempt
        $startResponse = $this->actingAs($this->user)
            ->postJson('/api/quizzes/start', ['quizId' => $this->quiz->id]);
        
        $attempt = $startResponse->json('attempt');
        $attemptId = $attempt['id'];

        // Get questions to answer
        $questions = $this->quiz->questions()->with('options')->get();
        
        // Submit first answer (correct)
        $firstQuestion = $questions[0];
        $correctOption = $firstQuestion->options->where('is_correct', true)->first();

        $response = $this->actingAs($this->user)
            ->putJson("/api/attempts/{$attemptId}", [
                'answers' => [
                    $firstQuestion->id => $correctOption->id
                ],
                'time_taken' => 60
            ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'attempt' => [
                    'id',
                    'answers'
                ]
            ]);

        // Check answer was saved
        $this->assertDatabaseHas('user_answers', [
            'quiz_attempt_id' => $attemptId,
            'question_id' => $firstQuestion->id,
            'option_id' => $correctOption->id,
            'is_correct' => true
        ]);
    }

    /** @test */
    public function it_can_complete_and_submit_quiz()
    {
        // Start an attempt
        $startResponse = $this->actingAs($this->user)
            ->postJson('/api/quizzes/start', ['quizId' => $this->quiz->id]);
        
        $attempt = $startResponse->json('attempt');
        $attemptId = $attempt['id'];

        // Answer all questions
        $questions = $this->quiz->questions()->with('options')->get();
        $answers = [];
        $correctCount = 0;

        foreach ($questions as $question) {
            $correctOption = $question->options->where('is_correct', true)->first();
            $answers[$question->id] = $correctOption->id;
            $correctCount++;
        }

        // Submit all answers
        $this->actingAs($this->user)
            ->putJson("/api/attempts/{$attemptId}", [
                'answers' => $answers,
                'time_taken' => 300
            ]);

        // Complete the quiz
        $response = $this->actingAs($this->user)
            ->putJson("/api/attempts/{$attemptId}", [
                'answers' => $answers,
                'completed' => true,
                'time_taken' => 300
            ]);

        $response->assertStatus(200);

        // Check attempt was marked as completed
        $this->assertDatabaseHas('quiz_attempts', [
            'id' => $attemptId,
            'user_id' => $this->user->id,
            'status' => 'completed',
            'passed' => true, // All answers correct
            'score' => 100
        ]);

        // Check all answers were saved
        $this->assertEquals(3, UserAnswer::where('quiz_attempt_id', $attemptId)->count());
    }

    /** @test */
    public function it_calculates_score_correctly()
    {
        // Start an attempt
        $startResponse = $this->actingAs($this->user)
            ->postJson('/api/quizzes/start', ['quizId' => $this->quiz->id]);
        
        $attempt = $startResponse->json('attempt');
        $attemptId = $attempt['id'];

        // Get questions
        $questions = $this->quiz->questions()->with('options')->get();
        
        // Answer 2 correct, 1 incorrect
        $answers = [];
        $answers[$questions[0]->id] = $questions[0]->options->where('is_correct', true)->first()->id; // Correct
        $answers[$questions[1]->id] = $questions[1]->options->where('is_correct', true)->first()->id; // Correct
        $answers[$questions[2]->id] = $questions[2]->options->where('is_correct', false)->first()->id; // Incorrect

        // Complete the quiz
        $response = $this->actingAs($this->user)
            ->putJson("/api/attempts/{$attemptId}", [
                'answers' => $answers,
                'completed' => true,
                'time_taken' => 300
            ]);

        $response->assertStatus(200);

        // Check score calculation (2/3 = 66.67%)
        $this->assertDatabaseHas('quiz_attempts', [
            'id' => $attemptId,
            'status' => 'completed',
            'score' => 67 // Rounded
        ]);
    }

    /** @test */
    public function it_prevents_submission_without_all_answers()
    {
        // Start an attempt
        $startResponse = $this->actingAs($this->user)
            ->postJson('/api/quizzes/start', ['quizId' => $this->quiz->id]);
        
        $attempt = $startResponse->json('attempt');
        $attemptId = $attempt['id'];

        // Try to complete with only 1 out of 3 answers
        $questions = $this->quiz->questions()->with('options')->get();
        $answers = [];
        $answers[$questions[0]->id] = $questions[0]->options->where('is_correct', true)->first()->id;

        $response = $this->actingAs($this->user)
            ->putJson("/api/attempts/{$attemptId}", [
                'answers' => $answers,
                'completed' => true,
                'time_taken' => 300
            ]);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'message' => 'Please answer all questions before submitting'
            ]);

        // Attempt should still be in progress
        $this->assertDatabaseHas('quiz_attempts', [
            'id' => $attemptId,
            'status' => 'in_progress'
        ]);
    }

    /** @test */
    public function it_can_reset_quiz_attempt()
    {
        // Start an attempt
        $startResponse = $this->actingAs($this->user)
            ->postJson('/api/quizzes/start', ['quizId' => $this->quiz->id]);
        
        $attempt = $startResponse->json('attempt');
        $attemptId = $attempt['id'];

        // Answer some questions
        $questions = $this->quiz->questions()->with('options')->get();
        $answers = [];
        $answers[$questions[0]->id] = $questions[0]->options->where('is_correct', true)->first()->id;

        $this->actingAs($this->user)
            ->putJson("/api/attempts/{$attemptId}", [
                'answers' => $answers,
                'time_taken' => 60
            ]);

        // Verify answers were saved
        $this->assertDatabaseHas('user_answers', [
            'quiz_attempt_id' => $attemptId,
            'question_id' => $questions[0]->id
        ]);

        // Reset the attempt
        $response = $this->actingAs($this->user)
            ->postJson("/api/attempts/{$attemptId}/reset");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Quiz attempt reset successfully'
            ]);

        // Verify answers were cleared
        $this->assertDatabaseMissing('user_answers', [
            'quiz_attempt_id' => $attemptId
        ]);

        // Verify attempt was reset
        $this->assertDatabaseHas('quiz_attempts', [
            'id' => $attemptId,
            'answers' => '[]',
            'score' => 0
        ]);
    }

    /** @test */
    public function it_prevents_unauthorized_access_to_attempts()
    {
        // Create attempt for different user
        $otherUser = User::factory()->create();
        $startResponse = $this->actingAs($otherUser)
            ->postJson('/api/quizzes/start', ['quizId' => $this->quiz->id]);
        
        $attempt = $startResponse->json('attempt');
        $attemptId = $attempt['id'];

        // Try to access with original user
        $response = $this->actingAs($this->user)
            ->getJson("/api/attempts/{$attemptId}");

        $response->assertStatus(404);
    }
}
