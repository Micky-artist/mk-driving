<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserAnswer extends Model
{
    use HasFactory;

    protected $table = 'quiz_attempt_answers';
    
    protected $fillable = [
        'quiz_attempt_id',
        'question_id',
        'option_id',
        'is_correct',
        'points_earned'
    ];
    
    // Override the primary key if needed
    protected $primaryKey = 'id';

    // This table doesn't need timestamps as per Prisma schema
    public $timestamps = false;

    /**
     * Get the quiz attempt that owns the user answer.
     */
    public function attempt(): BelongsTo
    {
        return $this->belongsTo(QuizAttempt::class, 'quiz_attempt_id');
    }

    /**
     * Get the question that the answer belongs to.
     */
    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }

    /**
     * Get the selected option (if any).
     */
    public function option(): BelongsTo
    {
        return $this->belongsTo(Option::class, 'option_id');
    }

    /**
     * Scope a query to only include correct answers.
     */
    public function scopeCorrect($query)
    {
        return $query->where('is_correct', true);
    }

    /**
     * Calculate the score for this answer.
     */
    public function calculateScore(): int
    {
        if ($this->is_correct) {
            return $this->question->points ?? 1; // Default to 1 point if not specified
        }
        return 0;
    }
}