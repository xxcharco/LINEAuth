<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Partnership extends Model
{
    use HasFactory;

    /**
     * 複数代入可能な属性
     */
    protected $fillable = [
        'user1_id',
        'user2_id',
        'invitation_token',
        'auth_code',
        'invitation_sent_at',
        'matched_at',
        'expires_at'
    ];

    /**
     * 日付として扱う属性
     */
    protected $casts = [
        'invitation_sent_at' => 'datetime',
        'matched_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    /**
     * 招待を送信したユーザーとのリレーション
     */
    public function user1(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user1_id');
    }

    /**
     * 招待を受けたユーザーとのリレーション
     */
    public function user2(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user2_id');
    }

    /**
     * パートナーシップがアクティブかどうか
     */
    public function isActive(): bool
    {
        return !is_null($this->matched_at);
    }

    /**
     * パートナーシップが保留中かどうか
     */
    public function isPending(): bool
    {
        return is_null($this->matched_at) && 
               !is_null($this->invitation_token) && 
               $this->expires_at->isFuture();
    }

    /**
     * パートナーシップが期限切れかどうか
     */
    public function isExpired(): bool
    {
        return !is_null($this->expires_at) && $this->expires_at->isPast();
    }

    /**
     * スコープ：アクティブなパートナーシップのみを取得
     */
    public function scopeActive($query)
    {
        return $query->whereNotNull('matched_at');
    }

    /**
     * スコープ：保留中のパートナーシップのみを取得
     */
    public function scopePending($query)
    {
        return $query->whereNull('matched_at')
                    ->whereNotNull('invitation_token')
                    ->where('expires_at', '>', now());
    }
}