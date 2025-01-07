<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'line_user_id',
        'line_access_token',
        'line_refresh_token',
        'line_token_expires_at'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'line_access_token',
        'line_refresh_token'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
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
            ->whereNotNull('matched_at')
            ->first() ?? 
            $this->receivedPartnerships()
            ->whereNotNull('matched_at')
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
     * LINEトークンが有効かどうかを確認
     */
    public function hasValidLineToken(): bool
    {
        return $this->line_token_expires_at 
            && $this->line_token_expires_at->isFuture()
            && $this->line_access_token;
    }
}