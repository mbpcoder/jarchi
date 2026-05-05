<?php
declare(strict_types=1);

return [
    'default_driver' => env('DEFAULT_BOT', 'telegram'),
    'drivers' => [
        'telegram' => [
            'token' => env('TELEGRAM_TOKEN'),
        ],
        'bale' => [
            'token' => env('BALE_TOKEN'),
        ],
        'rocketchat' => [
            'base_url' => env('ROCKETCHAT_BASE_URL'),
            'token' => env('ROCKETCHAT_TOKEN'),
            'user_id' => env('ROCKETCHAT_USER_ID'),
        ],
    ],
];
