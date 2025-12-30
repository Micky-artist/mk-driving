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
        'title',
        'slug',
        'excerpt',
        'content',
        'featured_image',
        'is_published',
        'published_at',
        'user_id',
        'views',
        'likes_count',
        'comments_count',
        'shares_count',
        'engagement_metrics',
        'forum_question_id',
        'category',
        'type',
        'status',
        'featured',
        'likes',
        'comments',
        'engagement_rate'
    ];

    protected $casts = [
        'title' => 'array',
        'content' => 'array',
        'excerpt' => 'array',
        'is_published' => 'boolean',
        'published_at' => 'datetime',
        'views' => 'integer',
        'likes_count' => 'integer',
        'comments_count' => 'integer',
        'shares_count' => 'integer',
        'engagement_metrics' => 'array',
        'featured' => 'boolean',
        'likes' => 'integer',
        'comments' => 'integer',
        'engagement_rate' => 'float',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected $appends = ['localized_title', 'localized_content', 'localized_meta_description', 'localized_excerpt'];
    
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
     * Get the user that owns the news.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get author that owns news (alias for user).
     */
    public function author()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get associated forum question for discussions
     */
    public function forumQuestion()
    {
        return $this->belongsTo(ForumQuestion::class);
    }

    /**
     * Get all versions of this news article
     */
    public function versions()
    {
        return $this->hasMany(NewsVersion::class)->orderBy('created_at', 'desc');
    }

    /**
     * Create a new version when updating content
     */
    public function createVersion(array $oldData, ?string $changeSummary = null): NewsVersion
    {
        return NewsVersion::create([
            'news_id' => $this->id,
            'title' => $oldData['title'] ?? $this->getRawOriginal('title'),
            'content' => $oldData['content'] ?? $this->getRawOriginal('content'),
            'excerpt' => $oldData['excerpt'] ?? $this->getRawOriginal('excerpt'),
            'change_summary' => $changeSummary,
            'edited_by' => auth()->id(),
        ]);
    }

    /**
     * Increment view count
     */
    public function incrementViews(): void
    {
        $this->increment('views');
        
        // Update engagement metrics
        $metrics = $this->engagement_metrics ?? [];
        $metrics['last_viewed_at'] = now()->toDateTimeString();
        $this->update(['engagement_metrics' => $metrics]);
    }

    /**
     * Increment likes count
     */
    public function incrementLikes(): void
    {
        $this->increment('likes_count');
        
        // Award points to author if threshold reached
        if ($this->likes_count % 10 === 0) {
            $this->author->awardPoints(5, UserPointsHistory::REASON_NEWS_ENGAGEMENT, [
                'news_id' => $this->id,
                'type' => 'likes_milestone'
            ]);
        }
    }

    /**
     * Increment comments count
     */
    public function incrementComments(): void
    {
        $this->increment('comments_count');
        
        // Award points to author for engagement
        $this->author->awardPoints(2, UserPointsHistory::REASON_NEWS_ENGAGEMENT, [
            'news_id' => $this->id,
            'type' => 'comment'
        ]);
    }

    /**
     * Get engagement rate
     */
    public function getEngagementRate(): float
    {
        if ($this->views === 0) {
            return 0;
        }

        $totalEngagement = $this->likes_count + $this->comments_count + $this->shares_count;
        return ($totalEngagement / $this->views) * 100;
    }

    /**
     * Create forum discussion for this news
     */
    public function createForumDiscussion(): ForumQuestion
    {
        $question = ForumQuestion::create([
            'title' => $this->title,
            'content' => $this->content,
            'user_id' => $this->user_id,
            'is_approved' => true,
            'topics' => ['announcement', 'news-discussion'],
            'is_news_discussion' => true
        ]);

        $this->update(['forum_question_id' => $question->id]);
        
        return $question;
    }

    /**
     * Share this news to forum for user discussions
     */
    public function shareToForum(): ForumQuestion
    {
        // Create a forum question for discussion
        $discussionTitle = [
            'en' => 'Discussion: ' . ($this->title['en'] ?? $this->getLocalizedTitleAttribute()),
            'rw' => 'Ibyifuzo: ' . ($this->title['rw'] ?? $this->getLocalizedTitleAttribute())
        ];

        $discussionContent = [
            'en' => "Let's discuss this news article:\n\n**Original Article:**\n" . ($this->content['en'] ?? $this->getLocalizedContentAttribute()) . "\n\nWhat are your thoughts on this? Share your opinions and questions below.",
            'rw' => "Tuvuge ku iyi nkuru:\n\n**Inkuru Yibanze:**\n" . ($this->content['rw'] ?? $this->getLocalizedContentAttribute()) . "\n\nNi ibihe uvuze kuri iyi nkuru? Seka ibitekerezo n'ibibazo ufitayo munsi y'iki."
        ];

        $question = ForumQuestion::create([
            'title' => $discussionTitle,
            'content' => $discussionContent,
            'user_id' => $this->user_id,
            'is_approved' => true,
            'topics' => ['news-discussion', 'community'],
            'is_news_discussion' => true
        ]);

        // Link the news to this forum discussion
        $this->update(['forum_question_id' => $question->id]);
        
        return $question;
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

    /**
     * Scope a query to get featured news.
     */
    public function scopeFeatured($query)
    {
        return $query->where('featured', true);
    }

    /**
     * Scope a query to get announcements.
     */
    public function scopeAnnouncements($query)
    {
        return $query->where('type', 'announcement');
    }

    /**
     * Scope a query to get articles.
     */
    public function scopeArticles($query)
    {
        return $query->where('type', 'article');
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
