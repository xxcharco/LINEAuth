<?php

namespace App\Services;

use App\Models\User;
use App\Models\Partnership;
use App\Exceptions\PartnershipException;
use Illuminate\Support\Str;
use Carbon\Carbon;

class PartnershipService
{
    private $lineMessageService;

    public function __construct(LineMessageService $lineMessageService)
    {
        $this->lineMessageService = $lineMessageService;
    }

    /**
     * パートナーシップの招待を作成
     */
    public function createInvitation(User $user): Partnership
    {
        if (!$user->canInvitePartner()) {
            throw new PartnershipException('既にパートナーシップが存在するか、有効な招待があります');
        }

        return Partnership::create([
            'user1_id' => $user->id,
            'invitation_token' => $this->generateUniqueToken(),
            'invitation_sent_at' => now(),
            'expires_at' => now()->addDays(config('line.partnership.invitation_expires_days', 7))
        ]);
    }

    /**
     * パートナーシップのマッチングを処理
     */
    public function processMatch(string $token, User $user2): Partnership
    {
        $partnership = Partnership::where('invitation_token', $token)
            ->where('expires_at', '>', now())
            ->whereNull('user2_id')
            ->firstOrFail();

        if ($partnership->user1_id === $user2->id) {
            throw new PartnershipException('自分自身とマッチングすることはできません');
        }

        if (!$user2->canInvitePartner()) {
            throw new PartnershipException('既に他のパートナーシップが存在します');
        }

        $partnership->update([
            'user2_id' => $user2->id,
            'matched_at' => now(),
            'invitation_token' => null
        ]);

        // マッチング完了通知を送信
        $this->sendMatchCompleteNotifications($partnership);

        return $partnership;
    }

    /**
     * 招待URLを生成
     */
    public function generateInvitationUrl(Partnership $partnership): string
    {
        return config('app.url') . '/partnerships/join/' . $partnership->invitation_token;
    }

    /**
     * パートナーシップの招待メッセージを送信
     */
    public function sendInvitation(Partnership $partnership): void
    {
        $invitationUrl = $this->generateInvitationUrl($partnership);
        $this->lineMessageService->sendInvitation($partnership->user1, $invitationUrl);
    }

    /**
     * マッチング完了通知を送信
     */
    private function sendMatchCompleteNotifications(Partnership $partnership): void
    {
        $this->lineMessageService->sendMatchComplete($partnership->user1);
        $this->lineMessageService->sendMatchComplete($partnership->user2);
    }

    /**
     * ユニークな招待トークンを生成
     */
    private function generateUniqueToken(): string
    {
        do {
            $token = Str::random(32);
        } while (Partnership::where('invitation_token', $token)->exists());

        return $token;
    }
}