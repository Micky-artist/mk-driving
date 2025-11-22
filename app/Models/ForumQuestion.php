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
        'user_id'
    ];

    protected $casts = [
        'title' => 'array',
        'content' => 'array',
        'topics' => 'array',
        'is_approved' => 'boolean',
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
  }