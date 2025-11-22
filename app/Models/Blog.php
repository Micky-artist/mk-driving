<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Blog extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Scope a query to only include published blogs.
     */
    public function scopePublished($query)
    {
        return $query->where('is_published', true)
                    ->where('published_at', '<=', now());
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'slug',
        'content',
        'excerpt',
        'featured_image',
        'is_published',
        'published_at',
        'author_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'title' => 'array',
        'content' => 'array',
        'excerpt' => 'array',
        'is_published' => 'boolean',
        'published_at' => 'datetime',
    ];

    /**
     * Get the title attribute with translation support.
     *
     * @param  mixed  $value
     * @return string
     */
    public function getTitleAttribute($value)
    {
        $title = is_array($value) ? $value : (json_decode($value, true) ?: []);
        return $title;
    }

    /**
     * Get the content attribute with translation support.
     *
     * @param  mixed  $value
     * @return string
     */
    public function getContentAttribute($value)
    {
        $content = is_array($value) ? $value : (json_decode($value, true) ?: []);
        return $content;
    }

    /**
     * Get the excerpt attribute with translation support.
     *
     * @param  mixed  $value
     * @return string
     */
    public function getExcerptAttribute($value)
    {
        if (is_null($value)) {
            return null;
        }
        $excerpt = is_array($value) ? $value : (json_decode($value, true) ?: []);
        return $excerpt;
    }

    /**
     * Get the author that owns the blog post.
     */
    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    /**
     * Scope a query to only include draft blog posts.
     */
    public function scopeDraft($query)
    {
        return $query->where('is_published', false)
                    ->orWhereNull('published_at')
                    ->orWhere('published_at', '>', now());
    }
}
