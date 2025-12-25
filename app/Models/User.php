<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\QuizAttempt;
use App\Models\Quiz;
use App\Models\Subscription;
use App\Models\Bookmark;
use App\Enums\Role;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Mail;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    public const ROLE_ADMIN = 'admin';
    public const ROLE_INSTRUCTOR = 'instructor';
    public const ROLE_STUDENT = 'student';

    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable, CanResetPassword;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'google_id',
        'role',
        'phone_number',
        'profile_image',
        'is_active',
        'subscription_plan_id',
        'points',
        'streak_days',
        'last_activity_date',
        'quiz_completion_streak',
        'forum_contributions',
        'helpful_answers',
        'current_rank',
        'previous_rank',
        'achievement_badges',
        'last_streak_date',
        // Location and device tracking fields
        'country',
        'city',
        'timezone',
        'device_fingerprint',
        'registration_ip',
        'registration_user_agent',
        'registration_device_type',
        'registration_browser',
        'registration_platform',
        'registered_at',
        'last_seen_at'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */

    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'last_login_at' => 'datetime',
            'last_activity_date' => 'datetime',
            'last_streak_date' => 'datetime',
            'achievement_badges' => 'array',
            'registered_at' => 'datetime',
            'last_seen_at' => 'datetime',
        ];
    }

    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    /**
     * Get the user's full name (alias for getFullNameAttribute).
     *
     * @return string
     */
    public function getNameAttribute(): string
    {
        return $this->getFullNameAttribute();
    }

    /**
     * Check if the user is subscribed to a specific plan
     */
    /**
     * Check if the user is subscribed to a specific plan
     *
     * @param int|string $planId The ID of the plan to check
     * @return bool True if the user is subscribed to the plan, false otherwise
     */
    public function subscribedToPlan($planId): bool
    {
        // Ensure both values are of the same type for comparison
        return (string) $this->subscription_plan_id === (string) $planId;
    }

    /**
     * Get all quizzes created by this user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<Quiz>
     */
    public function createdQuizzes(): HasMany
    {
        return $this->hasMany(Quiz::class, 'creator_id');
    }

    /**
     * Get all quizzes associated with this user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<Quiz>
     */
    public function quizzes(): HasMany
    {
        return $this->hasMany(Quiz::class);
    }

    /**
     * Get all quiz attempts for this user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<QuizAttempt>
     */
    public function quizAttempts(): HasMany
    {
        return $this->hasMany(QuizAttempt::class);
    }
    
    /**
     * Get all bookmarks for the user.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<Bookmark>
     */
    public function bookmarks(): HasMany
    {
        return $this->hasMany(Bookmark::class);
    }

    /**
     * Check if the user has bookmarked a specific quiz.
     */
    public function hasBookmarked(Quiz $quiz): bool
    {
        return $this->bookmarks()->where('quiz_id', $quiz->id)->exists();
    }
    
    /**
     * Get the user's current streak of days with at least one quiz attempt.
     *
     * @return int The current streak in days
     */
    public function getCurrentStreak(): int
    {
        $dates = $this->quizAttempts()
            ->selectRaw('DATE(created_at) as attempt_date')
            ->distinct()
            ->orderBy('attempt_date', 'desc')
            ->pluck('attempt_date')
            ->toArray();

        $streak = 0;
        $yesterday = now()->subDay()->startOfDay();
        
        foreach ($dates as $date) {
            $attemptDate = \Carbon\Carbon::parse($date)->startOfDay();
            
            if ($attemptDate->isToday()) {
                // Count today's attempts towards the streak
                $streak++;
            } elseif ($attemptDate->equalTo($yesterday)) {
                // If the last attempt was yesterday, continue the streak
                $streak++;
                $yesterday->subDay();
            } elseif ($attemptDate->lessThan($yesterday)) {
                // If there's a gap of more than one day, break the streak
                break;
            }
        }
        
        return $streak;
    }
    
    /**
     * Get all forum questions created by this user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<ForumQuestion>
     */
    public function forumQuestions(): HasMany
    {
        return $this->hasMany(ForumQuestion::class);
    }

    /**
     * Get all forum answers created by this user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<ForumAnswer>
     */
    public function forumAnswers(): HasMany
    {
        return $this->hasMany(ForumAnswer::class);
    }
    
    /**
     * Get the user's subscription plan.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<SubscriptionPlan, User>
     */
    public function subscriptionPlan(): BelongsTo
    {
        return $this->belongsTo(SubscriptionPlan::class);
    }
    
    /**
     * Get all subscriptions for the user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<Subscription>
     */
    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class, 'user_id');
    }

    /**
     * Get active subscriptions
     */
    /**
     * Get active subscriptions for the user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<Subscription>
     */
    public function activeSubscriptions(): HasMany
    {
        return $this->subscriptions()
            ->where('status', 'ACTIVE')
            ->where('ends_at', '>', now());
    }

    /**
     * Check if user has an active subscription.
     *
     * @return bool True if the user has an active subscription, false otherwise.
     * @see \App\Models\User::activeSubscriptions()
     */
    public function hasActiveSubscription(): bool
    {
        return $this->activeSubscriptions()->exists();
    }

    /**
     * Check if user is an admin.
     *
     * @return bool True if the user has admin role, false otherwise.
     */
    public function isAdmin(): bool
    {
        // Check both the enum value and the string value for backward compatibility
        return in_array(strtoupper($this->role), [Role::ADMIN->value, 'ADMIN', 'admin']);
    }
    
    /**
     * Check if user has the specified role.
     *
     * @param string $role The role to check for (e.g., 'ADMIN', 'INSTRUCTOR', 'USER')
     * @return bool True if the user has the role, false otherwise.
     */
    public function hasRole($role): bool
    {
        if (is_string($role) && str_contains($role, '|')) {
            // Handle pipe-separated roles (e.g., 'admin|instructor')
            $roles = array_map('trim', explode('|', $role));
            foreach ($roles as $r) {
                if (strtoupper($this->role) === strtoupper($r)) {
                    return true;
                }
            }
            return false;
        }
        
        // Single role check
        return strtoupper($this->role) === strtoupper($role);
    }

    /**
     * Determine if the user has the given ability.
     *
     * @param  string  $ability
     * @param  array|mixed  $arguments
     * @return bool
     */
    public function can($ability, $arguments = [])
    {
        if ($ability === 'admin') {
            return $this->isAdmin();
        }

        return parent::can($ability, $arguments);
    }

    /**
     * Update user's rank based on points
     */
    public function updateRank(): void
    {
        $totalPoints = $this->points;
        
        // Define rank thresholds
        $rankThresholds = [
            1 => 0,      // Bronze
            2 => 1000,   // Silver  
            3 => 5000,   // Gold
            4 => 10000,  // Platinum
            5 => 25000,  // Diamond
            6 => 50000,  // Master
        ];

        $newRank = 1; // Default to Bronze
        foreach ($rankThresholds as $rank => $threshold) {
            if ($totalPoints >= $threshold) {
                $newRank = $rank;
            }
        }

        $this->update(['current_rank' => $newRank]);
    }

    /**
     * Update daily streak
     */
    public function updateStreak(): void
    {
        $today = now()->startOfDay();
        $lastActivity = $this->last_activity_date ? $this->last_activity_date->startOfDay() : null;

        if ($lastActivity && $lastActivity->eq($today->copy()->subDay())) {
            // User was active yesterday - increment streak
            $this->increment('streak_days');
            $this->update(['last_streak_date' => $today]);
        } elseif (!$lastActivity || $lastActivity->lt($today->copy()->subDay())) {
            // Streak broken - reset to 1
            $this->update(['streak_days' => 1, 'last_streak_date' => $today]);
        }

        // Update last activity
        $this->update(['last_activity_date' => now()]);
    }

    /**
     * Award points to user
     */
    public function awardPoints(int $points, string $reason, array $metadata = []): UserPointsHistory
    {
        return UserPointsHistory::awardPoints($this, $points, $reason, $metadata);
    }

    /**
     * Get user's leaderboard position
     */
    public function getLeaderboardPosition(string $type = Leaderboard::TYPE_ALL_TIME): ?int
    {
        return LeaderboardEntry::getUserRank($this->id, $type);
    }

    /**
     * Check if user has specific badge
     */
    public function hasBadge(string $badge): bool
    {
        $badges = $this->achievement_badges ?? [];
        return in_array($badge, $badges);
    }

    /**
     * Award badge to user
     */
    public function awardBadge(string $badge): bool
    {
        $badges = $this->achievement_badges ?? [];
        
        if (in_array($badge, $badges)) {
            return false; // Already has badge
        }

        $badges[] = $badge;
        $this->update(['achievement_badges' => $badges]);
        
        // Award points for badge
        $this->awardPoints(50, UserPointsHistory::REASON_ACHIEVEMENT_UNLOCK, ['badge' => $badge]);
        
        return true;
    }

    /**
     * Get user's rank name
     */
    public function getRankName(): string
    {
        $rankNames = [
            1 => 'Bronze',
            2 => 'Silver',
            3 => 'Gold',
            4 => 'Platinum',
            5 => 'Diamond',
            6 => 'Master',
        ];

        return $rankNames[$this->current_rank] ?? 'Bronze';
    }

    /**
     * Get rank progress percentage
     */
    public function getRankProgress(): float
    {
        $rankThresholds = [
            1 => 0,
            2 => 1000,
            3 => 5000,
            4 => 10000,
            5 => 25000,
            6 => 50000,
        ];

        $currentThreshold = $rankThresholds[$this->current_rank] ?? 0;
        $nextThreshold = $rankThresholds[$this->current_rank + 1] ?? 50000;

        if ($this->current_rank >= 6) {
            return 100; // Master rank is max
        }

        $range = $nextThreshold - $currentThreshold;
        $progress = $this->points - $currentThreshold;

        return $range > 0 ? ($progress / $range) * 100 : 0;
    }

    /**
     * Check if the user is active.
     * A user is considered active if they have an active subscription or if they are an admin.
     *
     * @return bool
     */
    public function isActive(): bool
    {
        // Admins are always considered active
        if ($this->isAdmin()) {
            return true;
        }

        // Check if the user has an active subscription
        return $this->hasActiveSubscription();
    }

    /**
     * Check if the user is suspended.
     *
     * @return bool
     */
    public function isSuspended(): bool
    {
        return (bool) $this->is_suspended;
    }

    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        Mail::to($this->email)->send(new \App\Mail\ResetPasswordNotification($token, $this->email));
    }
}
