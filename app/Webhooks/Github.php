<?php
declare(strict_types=1);

namespace App\Webhooks;

class Github
{
    private object $data;

    public function __construct(object $data)
    {
        $this->data = $data;
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

    private function getBranchName(): string
    {
        $ref = $this->data->ref ?? '';
        return str_contains($ref, '/') ? substr($ref, strrpos($ref, '/') + 1) : $ref;
    }
}
