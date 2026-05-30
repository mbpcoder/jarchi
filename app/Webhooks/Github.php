<?php
declare(strict_types=1);

namespace App\Webhooks;

class Github
{
    public function __construct(private readonly object $data)
    {
    }

    public function parseMessage(): string
    {
        $headAuthor = $this->data->head_commit->author->name ?? 'unknown';
        $repository = $this->data->repository->full_name ?? 'unknown repository';
        $message = '<b>' . $headAuthor . '</b>' . ' pushed to ' . $repository . ' ' . $this->getBranchName() . PHP_EOL;

        foreach ($this->data->commits as $commit) {
            if (($this->data->head_commit->author->name ?? '') !== ($commit->author->name ?? '')) {
                $message .= '<b>' . ($commit->author->name ?? '') . '</b>' . ': ';
            }
            $message .= '<a href="' . ($commit->url ?? '#') . '">' . ($commit->message ?? '') . '</a>' . PHP_EOL;
        }

        return $message;
    }

    public function getActionUrl(): string
    {
        return $this->data->repository->html_url ?? ($this->data->commits[0]->url ?? '');
    }

    public function getActionLabel(): string
    {
        return 'Open GitHub';
    }

    private function getBranchName(): string
    {
        $ref = $this->data->ref ?? '';
        return str_contains($ref, '/') ? substr($ref, strrpos($ref, '/') + 1) : $ref;
    }
}
