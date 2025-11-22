<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class News extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'title',
        'slug',
        'content',
        'images',
        'author_id',
        'is_published',
        'category'
    ];

    protected $casts = [
        'title' => 'array',
        'content' => 'array',
        'images' => 'array',
        'is_published' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected $appends = ['localized_title', 'localized_content', 'localized_meta_description'];
    
    /**
     * Get the localized meta description based on the current locale.
     *
     * @return string
     */
    public function getLocalizedMetaDescriptionAttribute()
    {
        $meta = $this->getRawOriginal('meta_description');
        $locale = app()->getLocale();
        
        if (empty($meta)) {
            return '';
        }
        
        // If it's a string that looks like JSON but has extra quotes and escaping
        if (is_string($meta)) {
            // Clean up the string by removing outer quotes and unescaping
            $cleanMeta = trim($meta, '\"\\');
            
            // Try to decode the cleaned string
            $decoded = json_decode($cleanMeta, true);
            
            // If that fails, try with one more level of unescaping
            if (json_last_error() !== JSON_ERROR_NONE) {
                $cleanMeta = stripslashes($cleanMeta);
                $decoded = json_decode($cleanMeta, true);
            }
            
            if (json_last_error() === JSON_ERROR_NONE) {
                return $decoded[$locale] ?? $decoded['en'] ?? $meta;
            }
            
            // If it's still not valid JSON, check if it's a simple string with the format we expect
            if (str_contains($meta, 'en') && str_contains($meta, 'rw')) {
                // Try to manually extract the meta for the current locale
                $langKey = '"' . $locale . '"';  // e.g. "en" or "rw"
                $startPos = strpos($meta, $langKey . ':');
                if ($startPos !== false) {
                    $startPos = strpos($meta, '"', $startPos + strlen($langKey) + 1) + 1;
                    $endPos = strpos($meta, '"', $startPos);
                    if ($endPos !== false) {
                        return substr($meta, $startPos, $endPos - $startPos);
                    }
                }
            }
            
            return $meta; // Return as is if we can't parse it
        }
        
        // If it's already an array
        if (is_array($meta)) {
            return $meta[$locale] ?? $meta['en'] ?? '';
        }
        
        // If it's a simple string
        return $meta;
    }

    public function getLocalizedTitleAttribute()
    {
        $title = $this->getRawOriginal('title');
        $locale = app()->getLocale();
        
        if (empty($title)) {
            return __('news.default_title');
        }
        
        // If it's a string that looks like JSON but has extra quotes and escaping
        if (is_string($title)) {
            // Clean up the string by removing outer quotes and unescaping
            $cleanTitle = trim($title, '"\\');
            
            // Try to decode the cleaned string
            $decoded = json_decode($cleanTitle, true);
            
            // If that fails, try with one more level of unescaping
            if (json_last_error() !== JSON_ERROR_NONE) {
                $cleanTitle = stripslashes($cleanTitle);
                $decoded = json_decode($cleanTitle, true);
            }
            
            if (json_last_error() === JSON_ERROR_NONE) {
                return $decoded[$locale] ?? $decoded['en'] ?? $title;
            }
            
            // If it's still not valid JSON, check if it's a simple string with the format we expect
            if (str_contains($title, 'en') && str_contains($title, 'rw')) {
                // Try to manually extract the title for the current locale
                $langKey = '"' . $locale . '"';  // e.g. "en" or "rw"
                $startPos = strpos($title, $langKey . ':');
                if ($startPos !== false) {
                    $startPos = strpos($title, '"', $startPos + strlen($langKey) + 1) + 1;
                    $endPos = strpos($title, '"', $startPos);
                    if ($endPos !== false) {
                        return substr($title, $startPos, $endPos - $startPos);
                    }
                }
            }
        }
        
        // If it's already an array
        if (is_array($title)) {
            return $title[$locale] ?? $title['en'] ?? $title;
        }
        
        // Fallback to the raw title if all else fails
        return $title;
    }

    public function getLocalizedContentAttribute()
    {
        $content = $this->getRawOriginal('content');
        $locale = app()->getLocale();
        
        if (empty($content)) {
            return '';
        }
        
        // If it's a string that looks like JSON but has extra quotes and escaping
        if (is_string($content)) {
            // Clean up the string by removing outer quotes and unescaping
            $cleanContent = trim($content, '"\\');
            
            // Try to decode the cleaned string
            $decoded = json_decode($cleanContent, true);
            
            // If that fails, try with one more level of unescaping
            if (json_last_error() !== JSON_ERROR_NONE) {
                $cleanContent = stripslashes($cleanContent);
                $decoded = json_decode($cleanContent, true);
            }
            
            if (json_last_error() === JSON_ERROR_NONE) {
                return $decoded[$locale] ?? $decoded['en'] ?? $content;
            }
            
            // If it's still not valid JSON, check if it's a simple string with the format we expect
            if (str_contains($content, 'en') && str_contains($content, 'rw')) {
                // Try to manually extract the content for the current locale
                $langKey = '"' . $locale . '"';  // e.g. "en" or "rw"
                $startPos = strpos($content, $langKey . ':');
                if ($startPos !== false) {
                    $startPos = strpos($content, '"', $startPos + strlen($langKey) + 1) + 1;
                    $endPos = strpos($content, '"', $startPos);
                    if ($endPos !== false) {
                        return substr($content, $startPos, $endPos - $startPos);
                    }
                }
            }
            
            return $content; // Return as is if we can't parse it
        }
        
        // If it's already an array
        if (is_array($content)) {
            return $content[$locale] ?? $content['en'] ?? '';
        }
        
        // If it's a simple string
        return $content;
    }

    public function scopeWhereLocalized($query, $column, $search)
    {
        return $query->where(function($q) use ($column, $search) {
            $q->where($column, 'like', "%$search%")
              ->orWhereJsonContains($column, ['en' => $search])
              ->orWhereJsonContains($column, ['rw' => $search]);
        });
    }

    /**
     * Get the title attribute with translation support.
     *
     * @param  mixed  $value
     * @return string
     */
    public function getTitleAttribute($value)
    {
        if (empty($value)) {
            return '';
        }
        
        // Handle array or JSON string
        $title = is_array($value) ? $value : (json_decode($value, true) ?: []);
        
        // If it's not an array after decoding, return the original value
        if (!is_array($title)) {
            return $value;
        }
        
        $locale = app()->getLocale();
        
        // Try current locale
        if (isset($title[$locale]) && !empty($title[$locale])) {
            return $title[$locale];
        }
        
        // Fallback to English
        if (isset($title['en']) && !empty($title['en'])) {
            return $title['en'];
        }
        
        // If no translation found, return a default message from the language file
        return __('news.default_title');
    }
    
    /**
     * Get the content attribute with translation support.
     *
     * @param  mixed  $value
     * @return string
     */
    public function getContentAttribute($value)
    {
        if (empty($value)) {
            return '';
        }
        
        // Handle array or JSON string
        $content = is_array($value) ? $value : (json_decode($value, true) ?: []);
        
        // If it's not an array after decoding, return the original value
        if (!is_array($content)) {
            return $value;
        }
        
        $locale = app()->getLocale();
        
        // Try current locale
        if (isset($content[$locale]) && !empty($content[$locale])) {
            return $content[$locale];
        }
        
        // Fallback to English
        if (isset($content['en']) && !empty($content['en'])) {
            return $content['en'];
        }
        
        // If no translation found, return a default message from the language file
        return __('news.default_content');
    }

    /**
     * Get the author that owns the news.
     */
    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    /**
     * Scope a query to only include published news.
     */
    public function scopePublished($query)
    {
        return $query->where('is_published', true)
                    ->where(function($q) {
                        $q->whereNull('published_at')
                          ->orWhere('published_at', '<=', now());
                    });
    }
}
