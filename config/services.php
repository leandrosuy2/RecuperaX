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
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
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

    'cora' => [
        'oauth_url' => env('CORA_OAUTH_URL', 'https://api.cora.com.br/oauth/token'),
        'client_id' => env('CORA_CLIENT_ID'),
        'client_secret' => env('CORA_CLIENT_SECRET'),
        'api_base' => env('CORA_API_BASE', 'https://api.cora.com.br/v1'),
        'scope' => env('CORA_SCOPE', 'charges.read charges.write'),
    ],

    'picpay' => [
        'client_id' => env('PICPAY_CLIENT_ID'),
        'client_secret' => env('PICPAY_CLIENT_SECRET'),
        'api_url' => env('PICPAY_API_URL', 'https://api.picpay.com'),
        'sandbox' => env('PICPAY_SANDBOX', false),
    ],

    'whatsapp' => [
        'api_key' => env('WHATSAPP_API_KEY'),
        'api_url' => env('WHATSAPP_API_URL', 'https://recuperax-evolution-api.npfp58.easypanel.host'),
        'instance_name' => env('WHATSAPP_INSTANCE_NAME'),
        'webhook_url' => env('WHATSAPP_WEBHOOK_URL'),
        'enabled' => env('WHATSAPP_ENABLED', false),
    ],

];
