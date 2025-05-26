<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentAnswer extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'question_id',
        'answer',
        'is_correct',
        'created_at',
        'updated_at'
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function question()
    {
        return $this->belongsTo(Question::class);
    }

    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    public function answer()
    {
        return $this->belongsTo(Answer::class, 'answer_id');
    }

    // Cevaplanan sorunun metnini getiren ilişki
    public function selectedAnswer()
    {
        return $this->belongsTo(Answer::class, 'answer_id');
    }
}
