<?php

namespace App\Http\Controllers;

use App\Http\Requests\PartnershipInvitationRequest;
use App\Exceptions\PartnershipException;
use App\Services\PartnershipService;

class PartnershipController extends Controller
{
    private $partnershipService;

    public function __construct(PartnershipService $partnershipService)
    {
        $this->partnershipService = $partnershipService;
    }

    /**
     * パートナーシップの招待を作成
     */
    public function createInvitation(PartnershipInvitationRequest $request)
    {
        try {
            // リクエストは自動的にバリデーションされる
            $user = auth()->user();

            // ユーザーが既にパートナーシップを持っているかチェック
            if (!$user->canInvitePartner()) {
                throw new PartnershipException('既にパートナーシップが存在します');
            }

            // パートナーシップを作成
            $partnership = $this->partnershipService->createInvitation(
                $user,
                $request->line_friend_id
            );

            return response()->json([
                'message' => 'パートナーシップの招待を送信しました',
                'partnership' => $partnership
            ]);

        } catch (PartnershipException $e) {
            // PartnershipExceptionの場合は、例外クラスのrenderメソッドが
            // 適切なレスポンスを生成する
            throw $e;
        } catch (\Exception $e) {
            // その他の例外の場合
            return response()->json([
                'message' => '予期せぬエラーが発生しました'
            ], 500);
        }
    }
}