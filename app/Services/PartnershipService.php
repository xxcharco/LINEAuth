<?php

namespace App\Services;

use App\Models\User;
use App\Models\Partnership;
use App\Exceptions\PartnershipException;
use Illuminate\Support\Str;

class PartnershipService
{
    private const LINE_FRIEND_ADD_URL = "https://lin.ee/lC8wTS1";

    /**
     * LINE友達追加URLを生成
     */
    public function generateLineAddUrl(Partnership $partnership): string
    {
        if (!$partnership->invitation_token) {
            throw new PartnershipException('招待トークンが設定されていません');
        }

        // 認証コードを生成
        $authCode = $this->generateAuthCode($partnership->invitation_token, $partnership->user1_id);
        
        // パートナーシップレコードを更新
        $partnership->update(['auth_code' => $authCode]);

        // URLパラメータを作成
        $params = http_build_query([
            'ref' => 'partnership',
            'ic' => $partnership->invitation_token,
            'ac' => $authCode
        ]);

        // LINE友達追加URLにパラメータを追加
        return self::LINE_FRIEND_ADD_URL . 
            (str_contains(self::LINE_FRIEND_ADD_URL, '?') ? '&' : '?') . 
            $params;
    }

    /**
     * 認証コードを生成
     */
    private function generateAuthCode(string $invitationToken, int $userId): string
    {
        $data = $invitationToken . $userId . config('app.key');
        return hash('sha256', $data);
    }

    /**
     * 招待を作成（既存のメソッドを修正）
     */
    public function createInvitation(User $user): Partnership
    {
        // 既存のアクティブなパートナーシップをチェック
        if ($user->activePartnership()) {
            throw new PartnershipException('既にパートナーシップが存在します');
        }

        // 6桁の招待トークンを生成
        $invitationToken = strtoupper(Str::random(6));
        while (Partnership::where('invitation_token', $invitationToken)->exists()) {
            $invitationToken = strtoupper(Str::random(6));
        }

        // パートナーシップを作成
        return Partnership::create([
            'user1_id' => $user->id,
            'invitation_token' => $invitationToken,
            'invitation_sent_at' => now(),
            'expires_at' => now()->addDays(7)
        ]);
    }

    /**
     * パートナーシップのマッチングを処理
     */
    public function processMatch(string $invitationToken, string $authCode, User $user2): Partnership
    {
        // 招待トークンと認証コードが有効か確認
        $partnership = Partnership::where('invitation_token', $invitationToken)
            ->where('auth_code', $authCode)
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
            'invitation_token' => null,
            'auth_code' => null  // 認証コードもクリア
        ]);

        return $partnership;
    }
}