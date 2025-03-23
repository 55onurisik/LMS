<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentAnswer extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'exam_id',
        'answer_id',
        'is_correct',
        'students_answer'
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    public function answer()
    {
        return $this->belongsTo(Question::class, 'answer_id');
    }

    public function selectedAnswer()
    {
        return $this->belongsTo(Question::class, 'answer_id');
    }
}
