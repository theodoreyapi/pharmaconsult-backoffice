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

    'orange_sms' => [
        'client_id'       => env('ORANGE_SMS_CLIENT_ID'),
        'client_secret'   => env('ORANGE_SMS_CLIENT_SECRET'),
        'country_sender'  => env('ORANGE_SMS_COUNTRY_SENDER', 'tel:+2250000'),
        'sender_name'     => env('ORANGE_SMS_SENDER_NAME'),
        'token_url'       => 'https://api.orange.com/oauth/v3/token',
        'base_url'        => 'https://api.orange.com/smsmessaging/v1',
    ],

    'whatsapp' => [
        'token'           => env('WHATSAPP_TOKEN'),
        'phone_number_id' => env('WHATSAPP_PHONE_NUMBER_ID'),
        'api_version'     => env('WHATSAPP_API_VERSION', 'v25.0'),
    ],

];
