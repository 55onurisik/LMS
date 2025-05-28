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
        'question_id',
        'answer_id',
        'students_answer',
        'is_correct',
    ];

    /**
     * İlişki: Bu cevabı veren öğrenci
     */
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * İlişki: Bu cevabın ait olduğu soru
     */
    public function question()
    {
        return $this->belongsTo(Question::class);
    }

    /**
     * İlişki: Bu cevabın ait olduğu sınav
     */
    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    /**
     * İlişki: Öğrencinin seçtiği cevap metni
     */
    public function answer()
    {
        return $this->belongsTo(Answer::class, 'answer_id');
    }

    /**
     * Alias: seçilen cevabın ilişkisi
     */
    public function selectedAnswer()
    {
        return $this->belongsTo(Answer::class, 'answer_id');
    }
}
