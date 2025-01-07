<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Partnership extends Model
{
    protected $fillable = [
        'user1_id',
        'user2_id',
        'invitation_token',
        'invitation_sent_at',
        'matched_at',
        'expires_at'
    ];

    protected $casts = [
        'invitation_sent_at' => 'datetime',
        'matched_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function user1(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user1_id');
    }

    public function user2(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user2_id');
    }

    public function isActive(): bool
    {
        return !is_null($this->matched_at);
    }

    public function isPending(): bool
    {
        return is_null($this->matched_at) && 
               !is_null($this->invitation_token) && 
               $this->expires_at->isFuture();
    }

    public function isExpired(): bool
    {
        return !is_null($this->expires_at) && $this->expires_at->isPast();
    }
}