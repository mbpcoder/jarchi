<?php

declare(strict_types=1);

require 'vendor/autoload.php';

$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Test that TelegramMessage from package works
$msg = \NotificationChannels\Telegram\TelegramMessage::create('Test message')
    ->parseMode('html')
    ->linkPreviewOptions(['is_disabled' => true])
    ->button('Test Button', 'https://example.com');

echo "SUCCESS: TelegramMessage instantiated with all methods\n";
echo "Message content: " . json_encode($msg->toArray(), JSON_PRETTY_PRINT) . "\n";
