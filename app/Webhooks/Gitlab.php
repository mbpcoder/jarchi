<?php
declare(strict_types=1);

namespace App\Webhooks;

class Gitlab
{
    public function __construct(private readonly object $data)
    {
    }

    public function parseMessage(): string
    {
        $message = '<b>' . $this->data->user_name . '</b>' . ' pushed to ' . $this->data->project->path_with_namespace . ' ' . $this->getBranchName() . PHP_EOL;

        foreach ($this->data->commits as $commit) {
            if ($this->data->user_name !== $commit->author->name) {
                $message .= '<b>' . $commit->author->name . '</b>' . ': ';
            }
            $message .= '<a href="' . $commit->url . '">' . $commit->message . '</a>' . PHP_EOL;
        }

        return $message;
    }

    public function getActionUrl(): string
    {
        return $this->data->project->web_url ?? ($this->data->commits[0]->url ?? '');
    }

    public function getActionLabel(): string
    {
        return 'Open GitLab';
    }

    private function getBranchName(): string
    {
        return substr($this->data->ref, strrpos($this->data->ref, '/') + 1);
    }
}