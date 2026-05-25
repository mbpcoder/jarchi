<?php
declare(strict_types=1);

namespace App\Notifications\Messages;

class TelegramMessage
{
    protected string $content = '';
    protected string $parseMode = 'HTML';
    protected bool $disableWebPagePreview = true;
    protected array $replyMarkup = [];

    public static function create(string $content = ''): self
    {
        $message = new self();
        $message->content = $content;

        return $message;
    }

    public function content(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function parseMode(string $mode): self
    {
        $this->parseMode = $mode;

        return $this;
    }

    public function disableWebPagePreview(bool $disabled = true): self
    {
        $this->disableWebPagePreview = $disabled;

        return $this;
    }

    public function button(string $text, string $url): self
    {
        $this->replyMarkup = [
            'inline_keyboard' => [[
                [
                    'text' => $text,
                    'url' => $url,
                ],
            ]],
        ];

        return $this;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function getReplyMarkup(): array
    {
        return $this->replyMarkup;
    }

    public function toArray(): array
    {
        return [
            'text' => $this->content,
            'parse_mode' => $this->parseMode,
            'disable_web_page_preview' => $this->disableWebPagePreview,
            'reply_markup' => $this->replyMarkup,
        ];
    }
}
