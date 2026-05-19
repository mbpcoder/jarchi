<?php

declare(strict_types=1);

namespace Tests;

use PHPUnit\Framework\TestCase;
use App\NotificationChannels\Drivers\LogChannel;
use App\NotificationChannels\DTOs\MessageDTO;

class LogChannelTest extends TestCase
{
    public function testSendMessageWritesToLogFile(): void
    {
        $logFileBase = storage_path('logs/test_notification.log');
        $directory = dirname($logFileBase);

        if (!is_dir($directory)) {
            mkdir($directory, 0777, true);
        }

        $logFile = sprintf(
            '%s/%s-%s.%s',
            $directory,
            pathinfo($logFileBase, PATHINFO_FILENAME),
            date('Y-m-d'),
            pathinfo($logFileBase, PATHINFO_EXTENSION)
        );

        if (file_exists($logFile)) {
            unlink($logFile);
        }

        $driver = new LogChannel(['path' => $logFileBase]);
        $messageDTO = new MessageDTO(chatId: '12345', text: 'Test log channel message');

        $this->assertTrue($driver->sendMessage($messageDTO));
        $this->assertFileExists($logFile);

        $contents = file_get_contents($logFile);
        $this->assertStringContainsString('Test log channel message', $contents);
    }
}
