<?php
declare(strict_types=1);

namespace App\Notifications\Channels;

use App\NotificationChannels\DTOs\MessageDTO;
use App\NotificationChannels\Drivers\RocketChat;
use App\Notifications\Messages\RocketChatMessage;
use Illuminate\Notifications\Notification;

class RocketChatChannel
{
    public function send($notifiable, Notification $notification): void
    {
        $room = $notifiable->routeNotificationFor('rocketchat', $notification);

        if (empty($room)) {
            return;
        }

        $message = $notification->toRocketChat($notifiable);
        if (!$message instanceof RocketChatMessage) {
            return;
        }

        $driver = new RocketChat(config('bot.drivers.rocketchat', []));
        $dto = new MessageDTO(
            $room,
            $message->getContent()
        );

        $driver->sendMessage($dto);
    }
}
