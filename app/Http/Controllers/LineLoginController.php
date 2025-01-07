<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Inertia\Inertia;

class LineLoginController extends Controller
{
    // LINEログイン画面へのリダイレクト
    public function lineLogin()
    {
        $state = Str::random(32);
        $nonce = Str::random(32);
        
        // セッションにstateを保存（CSRF対策）
        session(['line_state' => $state]);
        
        $queryParams = http_build_query([
            'response_type' => 'code',
            'client_id' => config('services.line.client_id'),
            'redirect_uri' => config('services.line.redirect'),
            'state' => $state,
            'scope' => 'openid profile',
            'prompt' => 'consent',
            'nonce' => $nonce
        ]);

        return redirect('https://access.line.me/oauth2/v2.1/authorize?' . $queryParams);
    }

    // アクセストークン取得
    private function getAccessToken($code)
    {
        $response = Http::asForm()->post('https://api.line.me/oauth2/v2.1/token', [
            'grant_type' => 'authorization_code',
            'code' => $code,
            'redirect_uri' => config('services.line.redirect'),
            'client_id' => config('services.line.client_id'),
            'client_secret' => config('services.line.client_secret'),
        ]);

        if (!$response->successful()) {
            return null;
        }

        return $response->json()['access_token'];
    }

    // プロフィール取得
    private function getProfile($accessToken)
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $accessToken
        ])->get('https://api.line.me/v2/profile');

        if (!$response->successful()) {
            return null;
        }

        return $response->json();
    }

    // コールバック処理
    public function callback(Request $request)
    {
        // CSRF対策のstate検証
        if ($request->state !== session('line_state')) {
            return redirect()->route('login')->with('error', '不正なアクセスです。');
        }

        // アクセストークン取得
        $accessToken = $this->getAccessToken($request->code);
        if (!$accessToken) {
            return redirect()->route('login')->with('error', 'LINEログインに失敗しました。');
        }

        // プロフィール取得
        $profile = $this->getProfile($accessToken);
        if (!$profile) {
            return redirect()->route('login')->with('error', 'プロフィールの取得に失敗しました。');
        }

        // ユーザー取得または作成
        $user = User::updateOrCreate(
            ['line_id' => $profile['userId']],
            [
                'name' => $profile['displayName'],
                'provider' => 'line'
            ]
        );

        Auth::login($user);

        return redirect()->intended('/dashboard');
    }
}