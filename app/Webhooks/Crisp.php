<?php
declare(strict_types=1);

namespace App\Webhooks;

class Crisp
{
    private object $data;

    public function __construct(object $data)
    {
        $this->data = $data;
    }

    public function parseMessage(): string
    {
        if (!isset($this->data->data)) {
            return '';
        }

        $data = $this->data->data;
        $chatUrl = 'https://app.crisp.chat/website/' . ($data->website_id ?? '') . '/inbox/' . ($data->session_id ?? '') . '/';
        $chatUrlTitle = $data->user->nickname ?? 'Unknown user';

        $message = '<b><a href="' . $chatUrl . '">' . $chatUrlTitle . '</a></b>' . PHP_EOL;

        switch ($this->data->event ?? '') {
            case 'session:set_phone':
                $message .= 'Register phone number in Crisp <code>' . ($data->phone ?? '') . '</code>' . PHP_EOL;
                break;
            default:
                break;
        }

        if (!empty($data->content)) {
            $message .= $data->content . PHP_EOL;
        }

        return $message;
    }
}
