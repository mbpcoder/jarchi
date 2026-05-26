<?php
declare(strict_types=1);

namespace App\Notifications;

use App\Notifications\Channels\BaleChannel;
use App\Notifications\Channels\RocketChatChannel;
use App\Notifications\Messages\BaleMessage;
use App\Notifications\Messages\RocketChatMessage;
use Illuminate\Notifications\Messages\LogMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use InvalidArgumentException;
use NotificationChannels\Telegram\TelegramMessage;

abstract class BaseWebhookNotification extends Notification
{
    protected string $subject;

    public function __construct(
        protected string $message,
        protected string $channel,
        protected array $metadata = []
    ) {
        $this->subject = $metadata['subject'] ?? $this->defaultSubject();
    }

    abstract protected function defaultSubject(): string;

    public function via($notifiable): array
    {
        return [$this->getChannelClass()];
    }

    protected function getChannelClass(): string
    {
        return match ($this->channel) {
            'telegram' => 'telegram',
            'bale' => BaleChannel::class,
            'rocketchat' => RocketChatChannel::class,
            'mail' => 'mail',
            'log' => 'log',
            default => throw new InvalidArgumentException('Unsupported notification channel: ' . $this->channel),
        };
    }

    public function toTelegram($notifiable): TelegramMessage
    {
        $telegramMessage = TelegramMessage::create($this->message)
            ->parseMode('html')
            ->linkPreviewOptions(['is_disabled' => true]);

        if (!empty($this->metadata['action_url'])) {
            $telegramMessage->button(
                $this->metadata['action_label'] ?: 'Open details',
                $this->metadata['action_url']
            );
        }

        return $telegramMessage;
    }

    public function toBale($notifiable): BaleMessage
    {
        $baleMessage = BaleMessage::create($this->message)
            ->parseMode('HTML')
            ->disableWebPagePreview(true);

        if (!empty($this->metadata['action_url'])) {
            $baleMessage->button(
                $this->metadata['action_label'] ?: 'Open details',
                $this->metadata['action_url']
            );
        }

        return $baleMessage;
    }

    public function toRocketChat($notifiable): RocketChatMessage
    {
        $rocketMessage = RocketChatMessage::create($this->message);

        if (!empty($this->metadata['action_url'])) {
            $rocketMessage->button(
                $this->metadata['action_label'] ?: 'Open details',
                $this->metadata['action_url']
            );
        }

        return $rocketMessage;
    }

    public function toMail($notifiable): MailMessage
    {
        $mailMessage = (new MailMessage)
            ->subject($this->subject)
            ->line(strip_tags($this->message));

        if (!empty($this->metadata['action_url'])) {
            $mailMessage->action(
                $this->metadata['action_label'] ?: 'View details',
                $this->metadata['action_url']
            );
        }

        return $mailMessage;
    }

    public function toLog($notifiable): LogMessage
    {
        return new LogMessage(strip_tags($this->message));
    }
}
