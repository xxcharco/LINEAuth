<?php

namespace App\Http\Controllers;

use App\Services\PartnershipService;
use App\Services\LineMessageService;
use App\Http\Requests\PartnershipInvitationRequest;
use App\Models\Partnership;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Log;

class PartnershipInvitationController extends Controller
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
     * 招待作成画面を表示
     */
    public function create()  
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
    public function store(PartnershipInvitationRequest $request)
    {
        try {
            Log::info('招待作成開始');

            $user = auth()->user();
            if (!$user->canInvitePartner()) {
                return back()->with('error', '既にパートナーシップが存在するか、有効な招待があります');
            }

            // パートナーシップを作成
            $partnership = $this->partnershipService->createInvitation($user);
            
            // LINE友達追加URLを生成
            $invitationUrl = $this->partnershipService->generateLineAddUrl($partnership);
            
            Log::info('招待URL生成完了', [
                'invitation_token' => $partnership->invitation_token,
                'expires_at' => $partnership->expires_at
            ]);

            return Inertia::render('Partnership/InvitationLink', [
                'invitationUrl' => $invitationUrl,
                'expiresAt' => $partnership->expires_at->toISOString()
            ]);

        } catch (\Exception $e) {
            Log::error('招待作成エラー:', ['error' => $e->getMessage()]);
            return back()->with('error', 'パートナー招待の作成に失敗しました');
        }
    }    

    /**
     * 招待リンク表示
     */
    public function show(Request $request)
    {
        try {
            $user = auth()->user();
            
            // ユーザーの保留中の招待を取得
            $partnership = Partnership::where('user1_id', $user->id)
                ->where('expires_at', '>', now())
                ->whereNull('user2_id')
                ->latest()
                ->first();

            if (!$partnership) {
                return redirect()->route('partnerships.invite')
                    ->with('error', '有効な招待が見つかりません');
            }

            $invitationUrl = $this->partnershipService->generateLineAddUrl($partnership);

            Log::info('招待URL表示', [
                'partnership_id' => $partnership->id,
                'expires_at' => $partnership->expires_at
            ]);

            return Inertia::render('Partnership/InvitationLink', [
                'invitationUrl' => $invitationUrl,
                'expiresAt' => $partnership->expires_at->toISOString()
            ]);

        } catch (\Exception $e) {
            Log::error('招待表示エラー:', ['error' => $e->getMessage()]);
            return redirect()->route('partnerships.invite')
                ->with('error', '招待情報の取得に失敗しました');
        }
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
                'isOwnInvitation' => $partnership->user1_id === $user->id,
                'expiresAt' => $partnership->expires_at->toISOString()
            ]);

        } catch (\Exception $e) {
            Log::error('招待承認画面表示エラー:', ['error' => $e->getMessage()]);
            return Inertia::render('Partnership/Join', [
                'error' => '無効な招待リンクです'
            ]);
        }
    }
}