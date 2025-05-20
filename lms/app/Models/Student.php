<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Student extends Authenticatable
{
    use HasFactory, Notifiable;

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
    ];

    protected $hidden = [
        'password',
    ];
}
