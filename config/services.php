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
    'openai' => [
        'api_key' => env('OPENAI_API_KEY'),
        'model' => env('OPENAI_MODEL', 'gpt-5-mini'),
        'base_url' => env('OPENAI_BASE_URL', 'https://api.openai.com/v1'),
        'timeout' => env('OPENAI_TIMEOUT', 300),
        'connect_timeout' => env('OPENAI_CONNECT_TIMEOUT', 60),
        'max_tokens' => env('OPENAI_MAX_TOKENS', 800), // Для обратной совместимости
        'max_completion_tokens' => env('OPENAI_MAX_COMPLETION_TOKENS', 2000), // Для GPT-5 mini
        'temperature' => env('OPENAI_TEMPERATURE', 1.0), // GPT-5 mini поддерживает только 1.0
        'top_p' => env('OPENAI_TOP_P', 0.5),
        'frequency_penalty' => env('OPENAI_FREQUENCY_PENALTY', 0.0),
        'presence_penalty' => env('OPENAI_PRESENCE_PENALTY', 0.0),
        'verify_ssl' => env('OPENAI_VERIFY_SSL', true),
    ],

];
