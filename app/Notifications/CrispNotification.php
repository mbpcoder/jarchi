<?php
declare(strict_types=1);

namespace App\Notifications;

class CrispNotification extends BaseWebhookNotification
{
    protected function defaultSubject(): string
    {
        return 'Crisp Notification';
    }
}
