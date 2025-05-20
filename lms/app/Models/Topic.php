<?php

namespace App\Models;


use App\Models\Exam;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Topic extends Model
{
    use HasFactory;

    protected $fillable = ['topic_name', 'unit_id'];

    // Konunun ait olduğu sınav
    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    // Konunun sahip olduğu sorular
    public function answers()
    {
        return $this->hasMany(\App\Models\Answer::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }
}
