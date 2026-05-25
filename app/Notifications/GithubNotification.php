<?php
declare(strict_types=1);

namespace App\Notifications;

class GithubNotification extends BaseWebhookNotification
{
    protected function defaultSubject(): string
    {
        return 'GitHub Notification';
    }
}
