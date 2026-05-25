<?php
declare(strict_types=1);

namespace App\Notifications\Channels;

use App\NotificationChannels\DTOs\MessageDTO;
use App\NotificationChannels\Drivers\Bale;
use App\Notifications\Messages\BaleMessage;
use Illuminate\Notifications\Notification;

class BaleChannel
{
    public function send($notifiable, Notification $notification): void
    {
        $chatId = $notifiable->routeNotificationFor('bale', $notification);

        if (empty($chatId)) {
            return;
        }

        $message = $notification->toBale($notifiable);
        if (!$message instanceof BaleMessage) {
            return;
        }

        $driver = new Bale(config('bot.drivers.bale', []));
        $dto = new MessageDTO(
            $chatId,
            $message->getContent(),
            null,
            $message->getReplyMarkup()
        );

        $driver->sendMessage($dto);
    }
}
