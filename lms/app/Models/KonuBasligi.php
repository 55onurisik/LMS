<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KonuBasligi extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'sinif', 'unite_id'];

    public function unite()
    {
        return $this->belongsTo(Unite::class, 'unite_id');
    }
}
