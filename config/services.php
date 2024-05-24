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

    'zapi' => [
        'base_url' => env('ZAPI_BASE_URL') . '/instances/' . env('ZAPI_INSTANCE') . '/token/' . env('ZAPI_TOKEN'),
        'token_secure' => env('ZAPI_TOKEN_SECURE'),
    ],

    'apibrasil' => [
        'base_url' => env('APIBRASIL_BASE_URL'),
        'token' => env('APIBRASIL_TOKEN'),
    ],

    'cnpj_ws' => [
        'base_url' => env('CNPJWS_BASE_URL'),
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

];
