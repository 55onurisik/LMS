<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExamReview extends Model
{
    protected $fillable = [
        'exam_id',
        'student_id',
        'question_id',
        'review_text',
        'review_media', // Yeni eklenen alan
    ];

    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function question()
    {
        return $this->belongsTo(Answer::class);
    }
}
