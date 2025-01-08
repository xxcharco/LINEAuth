<?php

namespace App\Services;

use App\Models\User;
use LINE\Laravel\Facade\LINEBot;
use LINE\LINEBot\MessageBuilder\TextMessageBuilder;
use LINE\LINEBot\MessageBuilder\FlexMessageBuilder;
use LINE\LINEBot\MessageBuilder\RawMessageBuilder;

class LineMessageService
{
    private $bot;

    public function __construct(LINEBot $bot)
    {
        $this->bot = $bot;
    }

    /**
     * パートナー招待メッセージを送信
     */
    public function sendInvitation(User $user, string $invitationUrl): bool
    {
        $message = new FlexMessageBuilder(
            'パートナー招待',
            [
                'type' => 'bubble',
                'body' => [
                    'type' => 'box',
                    'layout' => 'vertical',
                    'contents' => [
                        [
                            'type' => 'text',
                            'text' => 'パートナー招待が届いています',
                            'weight' => 'bold',
                            'size' => 'md'
                        ],
                        [
                            'type' => 'button',
                            'style' => 'primary',
                            'action' => [
                                'type' => 'uri',
                                'label' => '招待を確認する',
                                'uri' => $invitationUrl
                            ]
                        ]
                    ]
                ]
            ]
        );

        $response = $this->bot->pushMessage($user->line_user_id, $message);
        return $response->isSucceeded();
    }

    /**
     * マッチング完了通知を送信
     */
    public function sendMatchComplete(User $user): bool
    {
        $message = new TextMessageBuilder('パートナーシップが確立されました！');
        $response = $this->bot->pushMessage($user->line_user_id, $message);
        return $response->isSucceeded();
    }

    /**
     * システム通知を送信
     */
    public function sendSystemNotification(User $user, string $message): bool
    {
        $textMessage = new TextMessageBuilder($message);
        $response = $this->bot->pushMessage($user->line_user_id, $textMessage);
        return $response->isSucceeded();
    }
}