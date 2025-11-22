<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Enums\QuizAttemptStatus;

class QuizAttempt extends Model
{
    protected $fillable = [
        'status',
        'score',
        'user_id',
        'quiz_id',
        'started_at',
        'completed_at',
        'active_quiz_id_for_user'
    ];

    protected $casts = [
        'status' => QuizAttemptStatus::class,
        'score' => 'integer',
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
     */
    public function userAnswers()
    {
        return $this->hasMany(UserAnswer::class, 'attempt_id');
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
        return $this->status === 'in_progress';
    }

    /**
     * Check if the attempt is completed.
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
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
