<?php
declare(strict_types=1);

namespace App\Webhooks;

class Sentry
{
    private object $data;

    public function __construct(object $data)
    {
        $this->data = $data;
    }

    public function parseMessage(): string
    {
        if (!isset($this->data->event_name) && !isset($this->data->event)) {
            return '';
        }

        $eventName = $this->data->event_name ?? $this->data->event ?? 'sentry_event';
        $project = $this->data->project->slug ?? $this->data->project_name ?? 'sentry';
        $message = '<b>' . $project . '</b> - ' . $eventName . PHP_EOL;

        if (!empty($this->data->message)) {
            $message .= strip_tags($this->data->message) . PHP_EOL;
        }

        if (!empty($this->data->issue_url)) {
            $message .= '<a href="' . $this->data->issue_url . '">Issue</a>' . PHP_EOL;
        }

        if (!empty($this->data->title)) {
            $message .= '<b>Title:</b> ' . strip_tags($this->data->title) . PHP_EOL;
        }

        return $message;
    }
}
