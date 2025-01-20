<?php

namespace App\Http\Controllers;

use App\Http\Requests\PartnershipInvitationRequest;
use App\Services\PartnershipService;
use App\Services\LineMessageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class PartnershipController extends Controller
{
    private $partnershipService;
    private $lineMessageService;

    public function __construct(
        PartnershipService $partnershipService,
        LineMessageService $lineMessageService
    ) {
        $this->partnershipService = $partnershipService;
        $this->lineMessageService = $lineMessageService;
    }

    /**
     * パートナーシップの現在の状態を表示
     */
    public function show()
    {
        $user = auth()->user();
        $partnership = $user->activePartnership();
        $canInvite = $user->canInvitePartner();

        // デバッグログを追加
        Log::info('Partnership Show状態', [
            'user_id' => $user->id,
            'has_partnership' => !!$partnership,
            'can_invite' => $canInvite,
            'partner_name' => $partnership ? $user->partner()?->name : null
        ]);


        return Inertia::render('Partnership/Show', [
            'partnership' => $partnership ? [
                'matched_at' => $partnership->matched_at,
                'partner' => [
                    'name' => $user->partner()->name,
                ]
            ] : null,
            'canInvite' => $user->canInvitePartner()
        ]);
    }

    /**
     * パートナーシップのマッチングを処理
     */
    public function processMatch(Request $request, string $token)
    {
        try {
            $user = auth()->user();
            $partnership = $this->partnershipService->processMatch($token, $user);

            // マッチング完了通知を両ユーザーに送信
            $this->lineMessageService->sendMatchComplete($partnership->user1, $partnership->user2);
            $this->lineMessageService->sendMatchComplete($partnership->user2, $partnership->user1);

            return redirect()->route('partnerships.show')
                ->with('message', 'パートナーシップが確立されました');

        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}