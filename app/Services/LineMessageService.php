<?php

namespace App\Services;

use App\Models\User;
use \GuzzleHttp\Client;
use \LINE\Parser\EventRequestParser;
use \LINE\Webhook\EventRequestParser as WebhookEventRequestParser;

class LineMessageService
{
    private $channelToken;
    private $channelSecret;
    private $client;

    public function __construct()
    {
        $this->channelToken = config('services.line.token');
        $this->channelSecret = config('services.line.secret');
        $this->client = new Client([
            'base_uri' => 'https://api.line.me',
            'headers' => [
                'Authorization' => 'Bearer ' . $this->channelToken,
                'Content-Type' => 'application/json',
            ],
        ]);
    }

    /**
     * 招待作成通知を送信
     */
    public function sendInvitationCreated(User $user): bool
    {
        try {
            $response = $this->client->post('/v2/bot/message/push', [
                'json' => [
                    'to' => $user->line_user_id,
                    'messages' => [
                        [
                            'type' => 'text',
                            'text' => '招待を送りました！パートナーの登録が完了したらお知らせします'
                        ]
                    ]
                ]
            ]);

            return $response->getStatusCode() === 200;
        } catch (\Exception $e) {
            \Log::error('LINE message sending failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * マッチング完了通知を送信
     */
    public function sendMatchComplete(User $user, User $partner): bool
    {
        try {
            $response = $this->client->post('/v2/bot/message/push', [
                'json' => [
                    'to' => $user->line_user_id,
                    'messages' => [
                        [
                            'type' => 'text',
                            'text' => "{$partner->name}さんとのパートナーシップが確立されました！\n特別な機能が利用可能になりました。"
                        ]
                    ]
                ]
            ]);

            return $response->getStatusCode() === 200;
        } catch (\Exception $e) {
            \Log::error('LINE message sending failed: ' . $e->getMessage());
            return false;
        }
    }
}