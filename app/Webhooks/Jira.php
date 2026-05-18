<?php
declare(strict_types=1);

namespace App\Webhooks;

class Jira
{
    public function __construct(private readonly object $data)
    {
    }

    public function parseMessage(): string
    {
        if (!isset($this->data->webhookEvent)) {
            return '';
        }

        $domain = $this->getDomain();

        return match ($this->data->webhookEvent) {
            'comment_created' => $this->getCommentMessage($domain),
            default => $this->getTaskMessage($domain),
        };
    }

    private function getDomain(): string
    {
        $self = $this->data?->issue?->self ?? '';
        $scheme = parse_url($self, PHP_URL_SCHEME);
        $host = parse_url($self, PHP_URL_HOST);

        return trim("{$scheme}://{$host}", '/');
    }

    private function getTaskMessage(string $domain): string
    {
        $message = $this->data->webhookEvent . PHP_EOL;

        $userUrl = $domain . '/secure/ViewProfile.jspa?name=' . ($this->data?->user?->name ?? '');
        $message .= '<b><a href="' . $userUrl . '">' . ($this->data?->user?->displayName ?? '') . '</a></b> => ';

        $projectUrl = $domain . '/projects/' . ($this->data?->issue?->fields?->project?->key ?? '');
        $message .= '<a href="' . $projectUrl . '">' . ($this->data?->issue?->fields?->project?->name ?? '') . '</a>' . PHP_EOL;

        $message .= ($this->data?->issue?->fields?->project?->name ?? '') . PHP_EOL;

        if (!empty($this->data?->issue?->fields?->status?->name)) {
            $message .= 'List: ' . $this->data->issue->fields->status->name . PHP_EOL;
        }

        if (!empty($this->data?->issue?->fields?->assignee?->displayName)) {
            $message .= 'Assign: ' . $this->data->issue->fields->assignee->displayName . PHP_EOL;
        }

        if (!empty($this->data?->issue?->fields?->summary)) {
            $cardUrl = $domain . '/browse/' . ($this->data?->issue?->key ?? '');
            $message .= '<b><a href="' . $cardUrl . '">Summery: </a></b>' . strip_tags((string) $this->data->issue->fields->summary) . PHP_EOL;
        }

        if (!empty($this->data?->issue?->fields?->description)) {
            $message .= '<b>Description: </b>' . strip_tags((string) $this->data->issue->fields->description) . PHP_EOL;
        }

        return $message;
    }

    private function getCommentMessage(string $domain): string
    {
        $message = $this->data->webhookEvent . PHP_EOL;

        $userUrl = $domain . '/secure/ViewProfile.jspa?name=' . ($this->data?->comment?->author?->name ?? '');
        $message .= '<b><a href="' . $userUrl . '">' . ($this->data?->comment?->author?->displayName ?? '') . '</a></b>' . PHP_EOL;

        if (!empty($this->data?->comment?->body)) {
            $message .= '<b>Description: </b>' . strip_tags((string) $this->data->comment->body) . PHP_EOL;
        }

        return $message;
    }
}