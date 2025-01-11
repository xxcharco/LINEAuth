<?php

namespace App\Services;

use App\Models\User;
use App\Models\Partnership;
use App\Exceptions\PartnershipException;
use Illuminate\Support\Str;

class PartnershipService
{
    /**
     * パートナーシップの招待を作成
     */
    public function createInvitation(User $user): Partnership
    {
        // 既存のアクティブなパートナーシップをチェック
        if ($user->activePartnership()) {
            throw new PartnershipException('既にパートナーシップが存在します');
        }

        // 招待トークンを生成
        return Partnership::create([
            'user1_id' => $user->id,
            'invitation_token' => Str::random(32),
            'invitation_sent_at' => now(),
            'expires_at' => now()->addDays(7)
        ]);
    }

    /**
     * 招待用のURLを生成
     */
    public function generateInvitationUrl(Partnership $partnership): string
    {
        return config('app.url') . '/partnerships/join/' . $partnership->invitation_token;
    }

    /**
     * LINEでシェアするためのURLを生成
     */
    public function generateLineShareUrl(Partnership $partnership): string
    {
        $invitationUrl = $this->generateInvitationUrl($partnership);
        $message = "パートナー招待が届いています\n詳細はこちら：" . $invitationUrl;

        return 'https://line.me/R/msg/text/?' . urlencode($message);
    }

    /**
     * パートナーシップのマッチングを処理
     */
    public function processMatch(string $token, User $user2): Partnership
    {
        // 招待トークンが有効か確認
        $partnership = Partnership::where('invitation_token', $token)
            ->where('expires_at', '>', now())
            ->whereNull('user2_id')
            ->firstOrFail();

        // 自分自身との組み合わせをチェック
        if ($partnership->user1_id === $user2->id) {
            throw new PartnershipException('自分自身とマッチングすることはできません');
        }

        // user2の既存パートナーシップをチェック
        if ($user2->activePartnership()) {
            throw new PartnershipException('既に他のパートナーシップが存在します');
        }

        // マッチングを確定
        $partnership->update([
            'user2_id' => $user2->id,
            'matched_at' => now(),
            'invitation_token' => null
        ]);

        return $partnership;
    }
}