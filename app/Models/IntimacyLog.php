<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IntimacyLog extends Model
{
    use HasFactory;

    // タイプの定数定義
    const TYPE_SEX = 'sex';
    const TYPE_MASTURBATION = 'masturbation';

    protected $fillable = [
        'user_id',
        'type',
        'date',
        'count',
        'memo'
    ];

    protected $casts = [
        'date' => 'date',
        'count' => 'integer'  // countを整数型にキャスト
    ];

    // スコープ定義
    public function scopeSex($query)
    {
        return $query->where('type', self::TYPE_SEX);
    }

    public function scopeMasturbation($query)
    {
        return $query->where('type', self::TYPE_MASTURBATION);
    }

    // ユーザーとのリレーション
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}