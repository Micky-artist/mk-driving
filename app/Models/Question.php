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
        $value = $this->getAttribute($field);
        
        // If the value is not an array, return it directly
        if (!is_array($value)) {
            return $value;
        }
        
        // If the value is an array, try to get the translation for the current locale
        if (isset($value[$locale]) && is_string($value[$locale])) {
            return $value[$locale];
        }
        
        // If no translation found and fallback is enabled, try the fallback locale
        if ($useFallback) {
            $fallbackLocale = config('app.fallback_locale', 'en');
            if (isset($value[$fallbackLocale]) && is_string($value[$fallbackLocale])) {
                return $value[$fallbackLocale];
            }
            
            // If no fallback translation, return the first available string value
            foreach ($value as $translation) {
                if (is_string($translation)) {
                    return $translation;
                }
            }
        }
        
        // If all else fails, return null
        return null;
    }
}
