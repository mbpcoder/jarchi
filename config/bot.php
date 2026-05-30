<?php
declare(strict_types=1);

return [
    'default_driver' => env('DEFAULT_BOT', 'telegram'),
    'drivers' => [
        'telegram' => [
            'token' => env('TELEGRAM_TOKEN'),
            'base_url' => env('TELEGRAM_BASE_URL', 'https://api.telegram.org'),
        ],
        'bale' => [
            'token' => env('BALE_TOKEN'),
            'base_url' => env('BALE_BASE_URL', 'https://tapi.bale.ai'),
        ],
        'rocketchat' => [
            'base_url' => env('ROCKETCHAT_BASE_URL'),
            'token' => env('ROCKETCHAT_TOKEN'),
            'user_id' => env('ROCKETCHAT_USER_ID'),
        ],
        'log' => [
            'path' => env('NOTIFICATION_LOG_PATH', storage_path('logs/notification.log')),
            'name' => env('NOTIFICATION_LOG_NAME', 'jarchi'),
        ],
    ],
];
