<?php
declare(strict_types=1);

namespace App\Bots\DTOs;

class MessageDTO
{
    public function __construct(
        public string $chatId,
        public string $text,
        public null|string $topicId = null
    ) {}
}
