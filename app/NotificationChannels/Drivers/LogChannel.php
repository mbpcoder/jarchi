<?php

declare(strict_types=1);

namespace App\NotificationChannels\Drivers;

use App\Drivers\DTOs\MessageDTO;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class LogChannel
{
    private Logger $logger;

    public function __construct()
    {
        $logPath = __DIR__ . '/../../storage/logs/jarchilog.log';
        $this->logger = new Logger('jarchi');
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
        } catch (\Exception $e) {
            $this->logger->error('Failed to log notification', ['error' => $e->getMessage()]);
            return false;
        }
    }
}
