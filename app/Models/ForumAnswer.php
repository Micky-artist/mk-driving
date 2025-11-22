<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ForumAnswer extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'content',
        'is_approved',
        'user_id',
        'question_id',
        'parent_id'
    ];

    protected $casts = [
        'content' => 'array',
        'is_approved' => 'boolean'
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(ForumQuestion::class, 'question_id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(ForumAnswer::class, 'parent_id');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(ForumAnswer::class, 'parent_id');
    }
  }