<?php
declare(strict_types=1);

namespace App\Notifications;

class JiraNotification extends BaseWebhookNotification
{
    protected function defaultSubject(): string
    {
        return 'Jira Notification';
    }
}
