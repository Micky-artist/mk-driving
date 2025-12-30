<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NewsVersion extends Model
{
    protected $fillable = [
        'news_id',
        'title',
        'content',
        'excerpt',
        'change_summary',
        'edited_by',
    ];

    protected $casts = [
        'title' => 'array',
        'content' => 'array',
        'excerpt' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $appends = ['localized_title', 'localized_content', 'localized_excerpt'];

    /**
     * Get the news item this version belongs to
     */
    public function news(): BelongsTo
    {
        return $this->belongsTo(News::class);
    }

    /**
     * Get the user who made this edit
     */
    public function editor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'edited_by');
    }

    /**
     * Get localized title
     */
    public function getLocalizedTitleAttribute()
    {
        $title = $this->getRawOriginal('title');
        $locale = app()->getLocale();
        
        if (empty($title)) {
            return '';
        }
        
        // Handle array or JSON string
        $titleArray = is_array($title) ? $title : (json_decode($title, true) ?: []);
        
        // If it's not an array after decoding, return the original value
        if (!is_array($titleArray)) {
            return $title;
        }
        
        // Try current locale
        if (isset($titleArray[$locale]) && !empty($titleArray[$locale])) {
            return $titleArray[$locale];
        }
        
        // Fallback to English
        if (isset($titleArray['en']) && !empty($titleArray['en'])) {
            return $titleArray['en'];
        }
        
        return '';
    }

    /**
     * Get localized content
     */
    public function getLocalizedContentAttribute()
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
     * Get localized excerpt
     */
    public function getLocalizedExcerptAttribute()
    {
        $excerpt = $this->getRawOriginal('excerpt');
        $locale = app()->getLocale();
        
        if (empty($excerpt)) {
            return '';
        }
        
        // Handle array or JSON string
        $excerptArray = is_array($excerpt) ? $excerpt : (json_decode($excerpt, true) ?: []);
        
        // If it's not an array after decoding, return the original value
        if (!is_array($excerptArray)) {
            return $excerpt;
        }
        
        // Try current locale
        if (isset($excerptArray[$locale]) && !empty($excerptArray[$locale])) {
            return $excerptArray[$locale];
        }
        
        // Fallback to English
        if (isset($excerptArray['en']) && !empty($excerptArray['en'])) {
            return $excerptArray['en'];
        }
        
        return '';
    }
}
