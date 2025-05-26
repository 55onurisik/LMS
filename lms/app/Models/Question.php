<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    protected $fillable = [
        'question_number',
        'topic_id',
        'question_text',
        'correct_answer',
        'created_at',
        'updated_at'
    ];

    public function topic()
    {
        return $this->belongsTo(Topic::class);
    }

    public function studentAnswers()
    {
        return $this->hasMany(StudentAnswer::class);
    }
} 