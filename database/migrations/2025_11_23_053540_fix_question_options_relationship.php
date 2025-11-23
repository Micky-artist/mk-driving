<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use App\Models\Question;
use App\Models\Option;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // First, let's ensure all options have the correct question_id
        $questions = Question::withTrashed()->get();
        
        foreach ($questions as $question) {
            if ($question->correct_option_id) {
                // Find the correct option and ensure it's linked to this question
                $option = Option::find($question->correct_option_id);
                if ($option) {
                    $option->update([
                        'question_id' => $question->id,
                        'is_correct' => true
                    ]);
                }
            }
            
            // Ensure all options for this question are properly linked
            Option::where('question_id', $question->id)
                ->update(['is_correct' => false]);
                
            // Set the first option as correct if none is set
            if (!$question->correct_option_id) {
                $firstOption = Option::where('question_id', $question->id)->first();
                if ($firstOption) {
                    $question->update([
                        'correct_option_id' => $firstOption->id
                    ]);
                    $firstOption->update(['is_correct' => true]);
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No need to reverse this migration as it only fixes data
    }
};
