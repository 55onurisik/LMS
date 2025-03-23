<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    protected $fillable = ['exam_id','question_number', 'answer_text', 'topic_id'];

    public function topic()
    {
        return $this->belongsTo(Topic::class);
    }

    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    public function questions()
    {
        return $this->belongsTo(Question::class); // veya `hasOne` olabilir, veritabanı ilişkisine göre
    }

    public function studentAnswers()
    {
        return $this->hasMany(StudentAnswer::class, 'answer_id', 'id');
    }

}
