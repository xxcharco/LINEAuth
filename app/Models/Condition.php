<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Condition extends Model
{
    use HasFactory;

    protected $fillable = [
        'recorded_date',
        'is_high',
        'condition'
    ];

    protected $casts = [
        'is_high' => 'boolean',
        'recorded_date' => 'date'
    ];
}
