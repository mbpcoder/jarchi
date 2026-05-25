<?php
declare(strict_types=1);

namespace App\Notifications;

class GitlabNotification extends BaseWebhookNotification
{
    protected function defaultSubject(): string
    {
        return 'GitLab Notification';
    }
}
