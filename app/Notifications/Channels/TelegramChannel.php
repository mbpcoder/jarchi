<?php
declare(strict_types=1);

namespace App\Notifications\Channels;

use App\NotificationChannels\DTOs\MessageDTO;
use App\NotificationChannels\Drivers\Telegram;
use App\Notifications\Messages\TelegramMessage;
use Illuminate\Notifications\Notification;

class TelegramChannel
{
    public function send($notifiable, Notification $notification): void
    {
        $chatId = $notifiable->routeNotificationFor('telegram', $notification);

        if (empty($chatId)) {
            return;
        }

        $message = $notification->toTelegram($notifiable);
        if (!$message instanceof TelegramMessage) {
            return;
        }

        $driver = new Telegram(config('bot.drivers.telegram', []));
        $dto = new MessageDTO(
            $chatId,
            $message->getContent(),
            null,
            $message->getReplyMarkup()
        );

        $driver->sendMessage($dto);
    }
}
