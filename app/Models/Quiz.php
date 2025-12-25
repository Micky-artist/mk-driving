<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Bookmark;

class Quiz extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'topics',
        'time_limit_minutes',
        'is_active',
        'is_guest_quiz',
        'creator_id',
        'subscription_plan_slug'
    ];

    protected $casts = [
        'title' => 'array',
        'description' => 'array',
        'topics' => 'array',
        'time_limit_minutes' => 'integer',
        'is_active' => 'boolean',
        'is_guest_quiz' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];
    
    // Use auto-incrementing IDs
    public $incrementing = true;
    protected $keyType = 'integer';
    
    /**
     * Get the route key for the model.
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'id'; // This tells Laravel to use the 'id' column for route model binding
    }

    // Relationships
    public function creator()
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function subscriptionPlan()
    {
        return $this->belongsTo(SubscriptionPlan::class, 'subscription_plan_slug', 'slug');
    }

    public function questions()
    {
        return $this->hasMany(Question::class);
    }

    public function attempts()
    {
        return $this->hasMany(QuizAttempt::class);
    }

    public function subscriptionPlans()
    {
        return $this->belongsToMany(SubscriptionPlan::class, 'quiz_subscription_plan');
    }

    /**
     * Get all bookmarks for this quiz.
     */
    public function bookmarks()
    {
        return $this->hasMany(Bookmark::class);
    }

    /**
     * Get the users who bookmarked this quiz.
     */
    public function bookmarkedBy()
    {
        return $this->belongsToMany(User::class, 'quiz_bookmarks', 'quiz_id', 'user_id')
            ->withTimestamps();
    }
    
    /**
     * Get the quiz attempts for the current user
     */
    public function userAttempts()
    {
        return $this->hasMany(QuizAttempt::class)->where('user_id', \Illuminate\Support\Facades\Auth::id());
    }

    /**
     * Get the guest quiz
     */
    public static function getGuestQuiz()
    {
        return static::where('is_guest_quiz', true)
            ->where('is_active', true)
            ->with(['questions' => function($query) {
                $query->orderBy('order')
                    ->with(['answers' => function($query) {
                        $query->orderBy('order');
                    }]);
            }])
            ->first();
    }

    /**
     * Check if the quiz is a guest quiz
     */
    public function isGuestQuiz(): bool
    {
        return (bool) $this->is_guest_quiz;
    }

    /**
     * Get the time limit in seconds
     */
    public function getTimeLimitInSeconds(): int
    {
        return $this->time_limit_minutes * 60;
    }

    /**
     * Get the title attribute with translation support.
     *
     * @param  mixed  $value
     * @return string
     */
    public function getTitleAttribute($value)
    {
        $title = is_array($value) ? $value : (json_decode($value, true) ?: []);
        $locale = app()->getLocale();
        
        if (!empty($title[$locale])) {
            return $title[$locale];
        }
        
        // Fallback to English if current locale not available
        if (!empty($title['en'])) {
            return $title['en'];
        }
        
        // If no translation found, return the first available or localized default
        if (!empty($title) && is_array($title)) {
            $first = reset($title);
            if (!empty($first)) {
                return $first;
            }
        }
        
        // Fall back to language file
        return __("quiz.default.title");
    }
    
    /**
     * Get the description attribute with translation support.
     *
     * @param  mixed  $value
     * @return string
     */
    public function getDescriptionAttribute($value)
    {
        $description = is_array($value) ? $value : (json_decode($value, true) ?: []);
        $locale = app()->getLocale();
        
        if (!empty($description[$locale])) {
            return $description[$locale];
        }
        
        // Fallback to English if current locale not available
        if (!empty($description['en'])) {
            return $description['en'];
        }
        
        // If no translation found, return the first available or localized default
        if (!empty($description) && is_array($description)) {
            $first = reset($description);
            if (!empty($first)) {
                return $first;
            }
        }
        
        // Fall back to language file
        return __("quiz.default.description");
    }
    
    /**
     * Get the topics attribute with translation support.
     *
     * @param  mixed  $value
     * @return array
     */
    public function getTopicsAttribute($value)
    {
        $topics = is_array($value) ? $value : (json_decode($value, true) ?: []);
        $locale = app()->getLocale();
        
        if (!empty($topics[$locale]) && is_array($topics[$locale])) {
            return $topics[$locale];
        }
        
        // Fallback to English if current locale not available
        if (!empty($topics['en']) && is_array($topics['en'])) {
            return $topics['en'];
        }
        
        // If no translation found, return the first available array
        if (is_array($topics)) {
            foreach ($topics as $lang => $items) {
                if (is_array($items) && !empty($items)) {
                    return $items;
                }
            }
        }
        
        // Fall back to language file
        return __("quiz.default.topics");
    }

    /**
     * Get the passing score percentage
     */
    public function getPassingScore(): float
    {
        return $this->passing_score ?? 70.0; // Default to 70% if not set
    }

    /**
     * Get a translation for a given field
     */
    public function getTranslation(string $field, ?string $locale = null, bool $useFallback = true): ?string
    {
        $locale = $locale ?? app()->getLocale();
        $translations = $this->getAttribute($field);
        
        if (!is_array($translations)) {
            return $this->getAttribute($field);
        }
        
        if (isset($translations[$locale])) {
            return $translations[$locale];
        }
        
        if ($useFallback) {
            $fallbackLocale = config('app.fallback_locale', 'en');
            return $translations[$fallbackLocale] ?? $this->getAttribute($field);
        }
        
        return $this->getAttribute($field);
    }
}
