<?php
declare(strict_types=1);

namespace App\NotificationChannels\Drivers;

use App\NotificationChannels\DTOs\MessageDTO;

class RocketChat extends Base
{
    public function sendMessage(MessageDTO $dto): bool
    {
        $url = rtrim($this->config['base_url'] ?? '', '/') . '/api/v1/chat.postMessage';
        $data = [
            'channel' => $dto->chatId,
            'text' => $dto->text,
            'token' => $this->config['token'] ?? null,
            'userId' => $this->config['user_id'] ?? null,
        ];

        if ($dto->topicId !== null) {
            $data['tmid'] = $dto->topicId;
        }

        $response = $this->postRequest($url, $data);
        return isset($response['success']) && $response['success'];
    }
}
