<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Answer extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'question_id',
        'text',
        'is_correct',
        'explanation',
        'order',
    ];

    protected $casts = [
        'text' => 'array',
        'explanation' => 'array',
        'is_correct' => 'boolean',
        'order' => 'integer',
    ];

    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }

    public function getTranslation(string $field, string $locale = null, bool $useFallback = true): ?string
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
