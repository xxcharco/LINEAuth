<?php

namespace App\Services;

use App\Models\Partnership;
use App\Models\User;
use Illuminate\Support\Str;
use App\Exceptions\PartnershipException;
use LINE\Laravel\Facades\LINEBot;

class PartnershipService
{
    private $lineBot;
    
    public function __construct(LINEBot $lineBot)
    {
        $this->lineBot = $lineBot;
    }

    public function createInvitation(User $user)
    {
        // Check if user already has an active partnership
        if ($this->hasActivePartnership($user)) {
            throw new PartnershipException('既にパートナーシップが存在します');
        }

        // Create new partnership with invitation token
        return Partnership::create([
            'user1_id' => $user->id,
            'invitation_token' => $this->generateUniqueToken(),
            'invitation_sent_at' => now(),
            'expires_at' => now()->addDays(7), // 7日間有効
        ]);
    }

    public function processMatch(string $token, User $user2)
    {
        $partnership = Partnership::where('invitation_token', $token)
            ->where('expires_at', '>', now())
            ->whereNull('user2_id')
            ->firstOrFail();

        // Prevent self-matching
        if ($partnership->user1_id === $user2->id) {
            throw new PartnershipException('自分自身とマッチングすることはできません');
        }

        // Check if user2 already has an active partnership
        if ($this->hasActivePartnership($user2)) {
            throw new PartnershipException('既に他のパートナーシップが存在します');
        }

        // Update partnership with user2 details
        $partnership->update([
            'user2_id' => $user2->id,
            'matched_at' => now(),
            'invitation_token' => null // Invalidate token after matching
        ]);

        // Send LINE notification to both users
        $this->sendMatchNotification($partnership);

        return $partnership;
    }

    private function hasActivePartnership(User $user)
    {
        return Partnership::where(function ($query) use ($user) {
            $query->where('user1_id', $user->id)
                  ->orWhere('user2_id', $user->id);
        })
        ->whereNotNull('matched_at')
        ->exists();
    }

    private function generateUniqueToken()
    {
        do {
            $token = Str::random(32);
        } while (Partnership::where('invitation_token', $token)->exists());

        return $token;
    }

    private function sendMatchNotification(Partnership $partnership)
    {
        $messageTemplate = [
            'type' => 'flex',
            'altText' => 'パートナーシップが完了しました',
            'contents' => [
                'type' => 'bubble',
                'body' => [
                    'type' => 'box',
                    'layout' => 'vertical',
                    'contents' => [
                        [
                            'type' => 'text',
                            'text' => 'マッチングが完了しました！',
                            'weight' => 'bold',
                            'size' => 'xl'
                        ],
                        [
                            'type' => 'text',
                            'text' => '新しい機能が利用可能になりました',
                            'margin' => 'md'
                        ]
                    ]
                ]
            ]
        ];

        // Send to both users
        $this->lineBot->pushMessage($partnership->user1->line_user_id, $messageTemplate);
        $this->lineBot->pushMessage($partnership->user2->line_user_id, $messageTemplate);
    }
}