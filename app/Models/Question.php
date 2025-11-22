<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Question extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'quiz_id',
        'text',
        'image_url',
        'type',
        'points',
        'is_active',
        'correct_option_id'
    ];

    protected $casts = [
        'text' => 'array',
    ];

    public function quiz(): BelongsTo
    {
        return $this->belongsTo(Quiz::class);
    }

    public function options()
    {
        return $this->hasMany(Option::class);
    }

    public function userAnswers()
    {
        return $this->hasMany(UserAnswer::class);
    }

    public function answers(): HasMany
    {
        return $this->hasMany(Answer::class);
    }

    public function getTranslation(string $field, ?string $locale = null, bool $useFallback = true): ?string
    {
        $locale = $locale ?: app()->getLocale();
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
