<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuizDraft extends Model
{
    protected $fillable = [
        'user_id',
        'quiz_data'
    ];

    protected $casts = [
        'quiz_data' => 'array'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
