<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PredefinedQA extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'predefined_qa';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'question',
        'answer',
        'category',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'question' => 'array',
        'answer' => 'array',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
