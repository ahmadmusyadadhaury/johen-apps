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

    'attendance_machine' => [
        'host' => env('ATTENDANCE_MACHINE_HOST', '192.168.0.209'),
        'port' => env('ATTENDANCE_MACHINE_PORT', 4370),
        'comm_key' => env('ATTENDANCE_MACHINE_COMM_KEY', 0),
        'timeout' => env('ATTENDANCE_MACHINE_TIMEOUT', 5),
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
        'payments_path' => env('DIGITAL_API_PAYMENTS_PATH', '/api/pembayaran/digital-asset-payments'),
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

    'sosial_media_api' => [
        'url' => env('KENDARAAN_API_URL'),
        'path' => env('SOSIAL_MEDIA_API_PATH', '/api/social-media'),
        'token' => env('KENDARAAN_API_TOKEN'),
        'verify_ssl' => env('KENDARAAN_API_VERIFY_SSL', true),
    ],

    'aset_mes_api' => [
        'url' => env('KENDARAAN_API_URL'),
        'path' => env('ASET_MES_API_PATH', '/api/aset-mes'),
        'token' => env('KENDARAAN_API_TOKEN'),
        'verify_ssl' => env('KENDARAAN_API_VERIFY_SSL', true),
    ],

    'aset_tim_api' => [
        'url' => env('KENDARAAN_API_URL'),
        'path' => env('ASET_TIM_API_PATH', '/api/aset-tim'),
        'token' => env('KENDARAAN_API_TOKEN'),
        'verify_ssl' => env('KENDARAAN_API_VERIFY_SSL', true),
    ],

    'electricity_api' => [
        'url' => env('ELECTRICITY_API_URL', env('KENDARAAN_API_URL')),
        'topups_path' => env('ELECTRICITY_API_TOPUPS_PATH', '/api/pembayaran/token-topups'),
        'readings_path' => env('ELECTRICITY_API_READINGS_PATH', '/api/pembayaran/token-readings'),
        'token' => env('ELECTRICITY_API_TOKEN', env('KENDARAAN_API_TOKEN')),
        'verify_ssl' => env('ELECTRICITY_API_VERIFY_SSL', env('KENDARAAN_API_VERIFY_SSL', true)),
    ],

    'internet_api' => [
        'url' => env('INTERNET_API_URL', env('KENDARAAN_API_URL')),
        'payments_path' => env('INTERNET_API_PAYMENTS_PATH', '/api/pembayaran/internet-payments'),
        'checks_path' => env('INTERNET_API_CHECKS_PATH', '/api/pembayaran/internet-checks'),
        'token' => env('INTERNET_API_TOKEN', env('KENDARAAN_API_TOKEN')),
        'verify_ssl' => env('INTERNET_API_VERIFY_SSL', env('KENDARAAN_API_VERIFY_SSL', true)),
    ],

    'ipl_ruko_api' => [
        'url' => env('IPL_RUKO_API_URL', env('KENDARAAN_API_URL')),
        'payments_path' => env('IPL_RUKO_API_PAYMENTS_PATH', '/api/pembayaran/ipl-ruko-payments'),
        'token' => env('IPL_RUKO_API_TOKEN', env('KENDARAAN_API_TOKEN')),
        'verify_ssl' => env('IPL_RUKO_API_VERIFY_SSL', env('KENDARAAN_API_VERIFY_SSL', true)),
    ],

];
