<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ForumQuestion extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'content',
        'topics',
        'is_approved',
        'views',
        'user_id',
        'is_news_discussion'
    ];

    protected $casts = [
        'content' => 'array',
        'topics' => 'array',
        'is_approved' => 'boolean',
        'is_news_discussion' => 'boolean',
        'views' => 'integer'
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function answers(): HasMany
    {
        return $this->hasMany(ForumAnswer::class, 'question_id');
    }

    public function news(): BelongsTo
    {
        return $this->belongsTo(News::class);
    }

    /**
     * Scope for news discussions
     */
    public function scopeNewsDiscussion($query)
    {
        return $query->where('is_news_discussion', true);
    }

    /**
     * Scope for regular forum questions
     */
    public function scopeRegularQuestions($query)
    {
        return $query->where('is_news_discussion', false);
    }

    /**
     * Get localized question (content)
     */
    public function getLocalizedQuestionAttribute()
    {
        $content = $this->getRawOriginal('content');
        $locale = app()->getLocale();
        
        if (empty($content)) {
            return '';
        }
        
        // Handle array or JSON string
        $contentArray = is_array($content) ? $content : (json_decode($content, true) ?: []);
        
        // If it's not an array after decoding, return the original value
        if (!is_array($contentArray)) {
            return $content;
        }
        
        // Try current locale
        if (isset($contentArray[$locale]) && !empty($contentArray[$locale])) {
            return $contentArray[$locale];
        }
        
        // Fallback to English
        if (isset($contentArray['en']) && !empty($contentArray['en'])) {
            return $contentArray['en'];
        }
        
        return '';
    }

    /**
     * Get localized content (alias for question)
     */
    public function getLocalizedContentAttribute()
    {
        return $this->getLocalizedQuestionAttribute();
    }
}