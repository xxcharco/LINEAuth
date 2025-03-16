<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Condition extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'recorded_date',
        'desire_level',
        'condition'
    ];

    protected $casts = [
        'recorded_date' => 'date',
        'desire_level' => 'integer'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
