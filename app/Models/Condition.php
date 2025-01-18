<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Condition extends Model
{
    use HasFactory;

    protected $fillable = [
        'recorded_date',
        'desire_level',
        'condition'
    ];

    protected $casts = [
        'recorded_date' => 'date',
        'desire_level' => 'integer'
    ];
}
