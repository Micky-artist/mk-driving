<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserPoint extends Model
{
    protected $fillable = [
        'user_id',
        'total_points',
        'weekly_points',
        'monthly_points',
        'last_activity_at',
    ];

    protected $casts = [
        'last_activity_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function addPoints(int $points): void
    {
        $this->increment('total_points', $points);
        $this->increment('weekly_points', $points);
        $this->increment('monthly_points', $points);
        $this->update(['last_activity_at' => now()]);
    }

    public function resetWeeklyPoints(): void
    {
        $this->update(['weekly_points' => 0]);
    }

    public function resetMonthlyPoints(): void
    {
        $this->update(['monthly_points' => 0]);
    }
}
