<?php

declare(strict_types=1);

namespace Tests;

use App\NotificationChannels\Drivers\Telegram;
use App\NotificationChannels\DTOs\MessageDTO;

class TelegramDriverTest extends TestCase
{
    public function testSendMessage(): void
    {
        $mockConfig = ['token' => 'test_token'];
        $telegramDriver = $this->getMockBuilder(Telegram::class)
            ->setConstructorArgs([$mockConfig])
            ->onlyMethods(['postRequest'])
            ->getMock();

        $telegramDriver->expects($this->once())
            ->method('postRequest')
            ->with(
                $this->stringContains('https://api.telegram.org/bot'),
                $this->arrayHasKey('chat_id')
            )
            ->willReturn(['ok' => true]);

        $messageDTO = new MessageDTO(chatId: '12345', text: 'Test Message');
        $result = $telegramDriver->sendMessage($messageDTO);

        $this->assertTrue($result);
    }
}
