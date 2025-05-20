<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    use HasFactory;
    protected $fillable = [
        'unit_name',
        'class_level',
    ];
    public function topics()
    {
        return $this->hasMany(Topic::class);
    }
}
