<?php

namespace Database\Factories;

use App\Models\Partnership;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Partnership>
 */
class PartnershipFactory extends Factory
{
    protected $model = Partnership::class;

    public function definition(): array
    {
        return [
            'user1_id' => User::factory(),
            'user2_id' => null,
            'invitation_token' => Str::random(32),
            'invitation_sent_at' => now(),
            'matched_at' => null,
            'expires_at' => now()->addDays(7)
        ];
    }

    /**
     * マッチング済みの状態のパートナーシップを作成
     */
    public function matched(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'user2_id' => User::factory(),
                'matched_at' => now(),
                'invitation_token' => null
            ];
        });
    }

    /**
     * 期限切れの状態のパートナーシップを作成
     */
    public function expired(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'expires_at' => now()->subDay()
            ];
        });
    }
}