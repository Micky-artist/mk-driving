<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Enums\QuizAttemptStatus;

class QuizAttempt extends Model
{
    protected $table = 'quiz_attempts';
    
    protected $fillable = [
        'status',
        'score',
        'score_percentage',
        'user_id',
        'quiz_id',
        'started_at',
        'completed_at',
        'time_spent_seconds',
        'total_questions',
        'active_quiz_id_for_user'
    ];

    protected $casts = [
        'score' => 'integer',
        'score_percentage' => 'decimal:2',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'time_spent_seconds' => 'integer',
        'time_taken' => 'integer',
        'total_questions' => 'integer',
        'percentage' => 'decimal:2',
        'passed' => 'boolean',
        'answers' => 'array',
    ];
    
    // Add these properties to match Prisma schema
    protected $dates = [
        'started_at',
        'completed_at',
    ];
    
    // Disable auto-incrementing for UUIDs
    public $incrementing = false;
    protected $keyType = 'string';

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function quiz(): BelongsTo
    {
        return $this->belongsTo(Quiz::class);
    }

    /**
     * Get all user answers for this attempt.
     * Now returns answers from the JSON column.
     */
    public function getUserAnswersAttribute()
    {
        return collect($this->answers ?? []);
    }

    /**
     * Alias for userAnswers for backward compatibility.
     */
    public function answers()
    {
        // Return a query builder that won't actually query the database
        // since we're storing answers in the JSON column
        return new class($this) {
            protected $attempt;
            
            public function __construct($attempt) {
                $this->attempt = $attempt;
            }
            
            public function get() {
                return collect($this->attempt->answers ?? []);
            }
            
            public function toArray() {
                return $this->get()->toArray();
            }
        };
    }

    /**
     * Get the score as a percentage.
     */
    public function getScorePercentageAttribute(): float
    {
        if ($this->total_questions === 0) {
            return 0;
        }
        return round(($this->score / $this->total_questions) * 100, 2);
    }

    /**
     * Check if the attempt is in progress.
     */
    public function isInProgress(): bool
    {
        return $this->status === 'IN_PROGRESS';
    }

    /**
     * Check if the attempt is completed.
     */
    public function isCompleted(): bool
    {
        return $this->status === 'COMPLETED';
    }

    /**
     * Complete the attempt.
     */
    public function complete(): bool
    {
        return $this->update([
            'status' => 'completed',
            'completed_at' => now(),
            'time_spent_seconds' => now()->diffInSeconds($this->started_at),
        ]);
    }
}
