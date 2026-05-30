<?php
declare(strict_types=1);

namespace App\Notifications\Messages;

class RocketChatMessage
{
    protected string $content = '';
    protected ?string $buttonText = null;
    protected ?string $buttonUrl = null;

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

    public function button(string $text, string $url): self
    {
        $this->buttonText = $text;
        $this->buttonUrl = $url;

        return $this;
    }

    public function getContent(): string
    {
        $content = $this->content;

        if ($this->buttonText !== null && $this->buttonUrl !== null) {
            $content .= PHP_EOL . PHP_EOL . sprintf('%s: %s', $this->buttonText, $this->buttonUrl);
        }

        return $content;
    }
}
