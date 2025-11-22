<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\QuizAttempt;
use App\Models\Quiz;
use App\Models\Subscription;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    public const ROLE_ADMIN = 'admin';
    public const ROLE_INSTRUCTOR = 'instructor';
    public const ROLE_STUDENT = 'student';

    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

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
        'role',
        'phone_number',
        'profile_image',
        'is_active',
        'subscription_plan_id'
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
        ];
    }

    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
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
        return $this->hasMany(Subscription::class);
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
        return $this->role === self::ROLE_ADMIN;
    }
}
