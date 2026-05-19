<?php

declare(strict_types=1);

namespace App\NotificationChannels\Drivers;

use App\NotificationChannels\DTOs\MessageDTO;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class LogChannel extends Base
{
    private Logger $logger;

    public function __construct(array $config = [])
    {
        parent::__construct($config);

        $logPath = $config['path'] ?? storage_path('logs/notification.log');
        $logDir = dirname($logPath);

        if (!is_dir($logDir)) {
            mkdir($logDir, 0777, true);
        }

        $this->logger = new Logger($config['name'] ?? 'jarchi');
        $this->logger->pushHandler(new RotatingFileHandler($logPath, 7, Logger::DEBUG));
    }

    public function sendMessage(MessageDTO $dto): bool
    {
        try {
            $this->logger->info('Notification Sent', [
                'chat_id' => $dto->chatId,
                'text' => $dto->text,
            ]);
            return true;
        } catch (\Throwable $e) {
            $this->logger->error('Failed to log notification', ['error' => $e->getMessage()]);
            return false;
        }
    }
}
