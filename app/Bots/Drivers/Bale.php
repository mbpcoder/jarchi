<?php
declare(strict_types=1);

namespace App\Bots\Drivers;

use App\Bots\DTOs\MessageDTO;

class Bale extends Base
{
    const MAXIMUM_CHAR_IN_MESSAGE = 4096;

    public function sendMessage(MessageDTO $dto): bool
    {
        $message = $dto->text;

        if (mb_strlen($message) > self::MAXIMUM_CHAR_IN_MESSAGE) {
            $message = mb_substr($message, 0, self::MAXIMUM_CHAR_IN_MESSAGE - 50) . '...';
        }

        $url = 'https://tapi.bale.ai/bot' . $this->config['token'] . '/sendMessage';
        $data = [
            'chat_id' => $dto->chatId,
            'text' => $message,
            'disable_web_page_preview' => true,
            'parse_mode' => 'html',
        ];

        if ($dto->topicId !== null) {
            $data['message_thread_id'] = $dto->topicId;
        }

        $response = $this->postRequest($url, $data);
        return isset($response['ok']) && $response['ok'];
    }
}
