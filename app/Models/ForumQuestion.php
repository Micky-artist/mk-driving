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
        'title',
        'content',
        'topics',
        'is_approved',
        'views',
        'user_id',
        'is_news_discussion'
    ];

    protected $casts = [
        'title' => 'array',
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
  }