<?php
declare(strict_types=1);

namespace App\Notifications;

class SentryNotification extends BaseWebhookNotification
{
    protected function defaultSubject(): string
    {
        return 'Sentry Notification';
    }
}
