<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Student extends Authenticatable
{
    use HasFactory, Notifiable ,HasApiTokens, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'class_level',
        'status',          // pending, approved, rejected
        'approved_at',
        'schedule_day',
        'schedule_time',
        'last_analysis_at'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_analysis_at' => 'datetime',
    ];

    public function answers()
    {
        return $this->hasMany(StudentAnswer::class);
    }

    public function sentMessages()
    {
        return $this->morphMany(Message::class, 'sender');
    }

    public function receivedMessages()
    {
        return $this->morphMany(Message::class, 'receiver');
    }


    public function getUnreadMessagesCountAttribute()
    {
        return $this->chatMessages()
            ->where('is_from_teacher', false)
            ->whereNull('read_at')
            ->count();
    }
}
