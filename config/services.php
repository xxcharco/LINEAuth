<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'line' => [
    'client_id' => env('LINE_CHANNEL_ID'),
    'client_secret' => env('LINE_CHANNEL_SECRET'),
    'redirect' => env('LINE_REDIRECT'),
    'id' => env('LINE_MESSAGE_CHANNEL_ID'),
    'secret' => env('LINE_MESSAGE_CHANNEL_SECRET'),
    'token' => env('LINE_MESSAGE_CHANNEL_ACCESS_TOKEN'),
    'official_account_url' => env('LINE_OFFICIAL_ACCOUNT_URL'),
    'add_friend_share_url' => env('LINE_ADD_FRIEND_SHARE_URL'),
    ],

];
