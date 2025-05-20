<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Answer extends Model
{
    use HasFactory;

    protected $fillable = ['exam_id','question_number', 'answer_text', 'topic_id'];

    // Topic modeliyle ilişki
    public function topic()
    {
        return $this->belongsTo(Topic::class);
    }

    // Exam modeliyle ilişki
    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    // Question modeliyle ilişki (bu ilişkiyi tanımlamanız gerekecek)
    public function questions()
    {
        return $this->belongsTo(Answer::class); // veya `hasOne` olabilir, veritabanı ilişkisine göre
    }

    public function studentAnswers()
    {
        return $this->hasMany(StudentAnswer::class, 'answer_id', 'id');
    }

}
