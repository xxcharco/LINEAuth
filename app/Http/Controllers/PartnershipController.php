<?php

namespace App\Http\Controllers;

use App\Http\Requests\PartnershipInvitationRequest;
use App\Services\PartnershipService;
use App\Services\LineMessageService;
use App\Models\Partnership;
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
     * 招待作成画面を表示
     */
    public function showInvitation()
    {
        $user = auth()->user();
        
        return Inertia::render('Partnership/Confirm', [
            'canInvite' => $user->canInvitePartner(),
            'hasActivePartnership' => !is_null($user->activePartnership()),
        ]);
    }

    /**
     * 招待を作成
     */
        public function createInvitation(PartnershipInvitationRequest $request)
    {
        try {
            Log::info('createInvitation開始');
            
            $user = auth()->user();
            Log::info('ユーザー取得:', ['user_id' => $user->id]);
            
            if (!$user->canInvitePartner()) {
                Log::info('招待不可');
                return back()->with('error', '既にパートナーシップが存在するか、有効な招待があります');
            }

            // パートナーシップを作成
            $partnership = $this->partnershipService->createInvitation($user);
            Log::info('パートナーシップ作成完了', ['partnership_id' => $partnership->id]);
            
            // 招待URLを生成
            $invitationUrl = route('partnerships.join', [
                'token' => $partnership->invitation_token
            ]);
            Log::info('招待URL生成完了');

            // 招待作成通知をLINEで送信
            $this->lineMessageService->sendInvitationCreated($user);
            Log::info('LINE通知送信完了');

            session(['invitation_url' => $invitationUrl]);
            Log::info('セッションに保存完了');

            return response()->json([
                'success' => true
            ]);

        } catch (\Exception $e) {
            Log::error('エラー発生:', ['error' => $e->getMessage()]);
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * 招待リンク表示画面を表示
     */
    public function showInvitationLink(Request $request)
    {
        return Inertia::render('Partnership/InvitationLink', [
            'invitationUrl' => $request->query('url')
        ]);
    }

    /**
     * 招待承認画面を表示
     */
    public function showJoin(string $token)
    {
        try {
            $partnership = Partnership::where('invitation_token', $token)
                ->where('expires_at', '>', now())
                ->whereNull('user2_id')
                ->firstOrFail();

            $user = auth()->user();

            return Inertia::render('Partnership/Join', [
                'token' => $token,
                'inviter' => [
                    'name' => $partnership->user1->name,
                ],
                'canAccept' => $user->canInvitePartner(),
                'isOwnInvitation' => $partnership->user1_id === $user->id
            ]);

        } catch (\Exception $e) {
            return Inertia::render('Partnership/Join', [
                'error' => '無効な招待リンクです'
            ]);
        }
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