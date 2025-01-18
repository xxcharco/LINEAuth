<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'line_user_id',
        'line_access_token',
        'line_refresh_token',
        'line_token_expires_at'
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'line_access_token',
        'line_refresh_token'
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'line_token_expires_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * ユーザーが開始したパートナーシップを取得
     */
    public function initiatedPartnerships(): HasMany
    {
        return $this->hasMany(Partnership::class, 'user1_id');
    }

    /**
     * ユーザーが受け取ったパートナーシップを取得
     */
    public function receivedPartnerships(): HasMany
    {
        return $this->hasMany(Partnership::class, 'user2_id');
    }

    /**
     * アクティブなパートナーシップを取得
     */
    public function activePartnership()
    {
        return $this->initiatedPartnerships()
            ->active()
            ->first() ?? 
            $this->receivedPartnerships()
            ->active()
            ->first();
    }

    /**
     * 現在のパートナーを取得
     */
    public function partner()
    {
        $partnership = $this->activePartnership();
        if (!$partnership) {
            return null;
        }

        return $partnership->user1_id === $this->id 
            ? $partnership->user2 
            : $partnership->user1;
    }

    /**
     * パートナーシップの招待が可能かどうか
     */
    public function canInvitePartner(): bool
    {
        return !$this->activePartnership() && 
               !$this->initiatedPartnerships()
                    ->pending()
                    ->exists();
    }

    /**
     * LINEトークンが有効かどうか
     */
    public function hasValidLineToken(): bool
    {
        return $this->line_token_expires_at && 
               $this->line_token_expires_at->isFuture() &&
               $this->line_access_token;
    }
}