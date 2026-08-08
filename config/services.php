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

    'meeting' => [
        'url' => env('MEETING_API_URL'),
        'login_path' => env('MEETING_API_LOGIN_PATH', '/api/login'),
        'path' => env('MEETING_API_PATH', '/api/meetings'),
        'username' => env('MEETING_API_USERNAME'),
        'password' => env('MEETING_API_PASSWORD'),
        'verify_ssl' => env('MEETING_API_VERIFY_SSL', true),
    ],

    'vehicle_api' => [
        'url' => env('KENDARAAN_API_URL'),
        'path' => env('VEHICLE_API_PATH', '/api/vehicles'),
        'token' => env('KENDARAAN_API_TOKEN'),
        'verify_ssl' => env('KENDARAAN_API_VERIFY_SSL', true),
    ],

    'digital_asset_api' => [
        'url' => env('KENDARAAN_API_URL'),
        'path' => env('DIGITAL_API_PATH', '/api/digital-assets'),
        'token' => env('KENDARAAN_API_TOKEN'),
        'verify_ssl' => env('KENDARAAN_API_VERIFY_SSL', true),
    ],

    'sim_card_api' => [
        'url' => env('KENDARAAN_API_URL'),
        'path' => env('SIM_CARD_API_PATH', '/api/sim-cards'),
        'token' => env('KENDARAAN_API_TOKEN'),
        'verify_ssl' => env('KENDARAAN_API_VERIFY_SSL', true),
    ],

    'peralatan_kantor_api' => [
        'url' => env('KENDARAAN_API_URL'),
        'path' => env('KENDARAAN_API_PATH', '/api/peralatan-kantor'),
        'token' => env('KENDARAAN_API_TOKEN'),
        'verify_ssl' => env('KENDARAAN_API_VERIFY_SSL', true),
    ],

    'aset_ruko_api' => [
        'url' => env('KENDARAAN_API_URL'),
        'path' => env('ASET_RUKO_API_PATH', '/api/aset-ruko'),
        'token' => env('KENDARAAN_API_TOKEN'),
        'verify_ssl' => env('KENDARAAN_API_VERIFY_SSL', true),
    ],

];
