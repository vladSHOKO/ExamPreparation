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

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],
    'gigachat' => [
        'base_url_for_access_token' => env('GIGACHAT_BASE_URL_FOR_ACCESS_TOKEN'),
        'base_url_for_message_sending' => env('GIGACHAT_BASE_URL_FOR_MESSAGE_SENDING'),
        'client_id' => env('GIGACHAT_CLIENT_ID'),
        'client_secret' => env('GIGACHAT_CLIENT_SECRET'),
        'scope' => env('GIGACHAT_SCOPE', 'GIGACHAT_API_PERS'),
        'model' => env('GIGACHAT_MODEL', 'GigaChat-2'),
        'timeout' => env('GIGACHAT_TIMEOUT', 30),
        'token_path'    => env('GIGACHAT_TOKEN_PATH', '/api/v2/oauth'),
        'chat_path'     => env('GIGACHAT_CHAT_PATH', '/api/v1/chat/completions')
    ],

];
