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

            // 環境変数から直接URLを取得
            $invitationUrl = config('services.line.add_friend_share_url');
            Log::info('URL取得:', ['url' => $invitationUrl]);

            // 直接レンダリング
            return Inertia::render('Partnership/InvitationLink', [
                'invitationUrl' => $invitationUrl
            ]);
            
            // $invitationUrl = config('services.line.add_friend_share_url');
            // Log::info('LINE URL設定値:', [
            //     'url' => $invitationUrl
            // ]);

            // if (empty($invitationUrl)) {
            //     throw new \Exception('URLの設定が見つかりません');
            // }

            // $user = auth()->user();
            // if (!$user->canInvitePartner()) {
            //     return back()->with('error', '既にパートナーシップが存在するか、有効な招待があります');
            // }

            // // パートナーシップを作成
            // $partnership = $this->partnershipService->createInvitation($user);

            // // フラッシュデータとしてURLを保存
            // return redirect()
            //     ->route('partnerships.invitation')
            //     ->with('invitation_url', $invitationUrl);

        } catch (\Exception $e) {
            Log::error('エラー発生:', ['error' => $e->getMessage()]);
            return back()->with('error', $e->getMessage());
        }
    }    

    /**
     * 招待リンク表示
     */
    public function show(Request $request)
    {
        // $url = session('invitation_url');
        
        // Log::info('show メソッド', [
        //     'flash_url' => session('invitation_url'),
        //     'final_url' => $url
        // ]);

        // 環境変数から直接URLを取得（showメソッドでも同じURLを返す）
        $invitationUrl = config('services.line.add_friend_share_url');
        Log::info('show メソッド', [
            'url' => $invitationUrl
        ]);

        return Inertia::render('Partnership/InvitationLink', [
            'invitationUrl' => $invitationUrl
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
}
