<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Exam extends Model
{
    use HasFactory;

    protected $fillable = [
        'exam_code',
        'question_count',
        'exam_title',
        //'exam_type',
        //'score',
        //'user_id',
        'review_visibility',
    ];
    public function studentAnswers()
    {
        return $this->hasMany(StudentAnswer::class);
    }
    public function answers()
    {
        return $this->hasMany(Answer::class, 'exam_id', 'id');
    }

}
