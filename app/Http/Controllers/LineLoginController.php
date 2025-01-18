<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Inertia\Inertia;
use Carbon\Carbon;

class LineLoginController extends Controller
{
    public function lineLogin()
    {
        $state = Str::random(32);
        $nonce = Str::random(32);
        
        session(['line_state' => $state]);
        session(['line_nonce' => $nonce]);
        
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

        return $response->json();
    }

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

    public function callback(Request $request)
    {
        if ($request->state !== session('line_state')) {
            return redirect()->route('login')->with('error', '不正なアクセスです。');
        }

        $tokenResponse = $this->getAccessToken($request->code);
        if (!$tokenResponse) {
            return redirect()->route('login')->with('error', 'LINEログインに失敗しました。');
        }

        $profile = $this->getProfile($tokenResponse['access_token']);
        if (!$profile) {
            return redirect()->route('login')->with('error', 'プロフィールの取得に失敗しました。');
        }

        // ユーザー取得または作成
        $user = User::updateOrCreate(
            ['line_user_id' => $profile['userId']],
            [
                'name' => $profile['displayName'],
                'line_access_token' => $tokenResponse['access_token'],
                'line_refresh_token' => $tokenResponse['refresh_token'] ?? null,
                'line_token_expires_at' => isset($tokenResponse['expires_in']) 
                    ? Carbon::now()->addSeconds($tokenResponse['expires_in'])
                    : null
            ]
        );

        Auth::login($user);

        return Inertia::location(redirect()->intended('/conditions')->getTargetUrl());
    }

    public function refreshToken(User $user)
    {
        if (!$user->line_refresh_token) {
            return false;
        }

        $response = Http::asForm()->post('https://api.line.me/oauth2/v2.1/token', [
            'grant_type' => 'refresh_token',
            'refresh_token' => $user->line_refresh_token,
            'client_id' => config('services.line.client_id'),
            'client_secret' => config('services.line.client_secret'),
        ]);

        if (!$response->successful()) {
            return false;
        }

        $tokenData = $response->json();

        $user->update([
            'line_access_token' => $tokenData['access_token'],
            'line_refresh_token' => $tokenData['refresh_token'] ?? $user->line_refresh_token,
            'line_token_expires_at' => isset($tokenData['expires_in']) 
                ? Carbon::now()->addSeconds($tokenData['expires_in'])
                : null
        ]);

        return true;
    }
}