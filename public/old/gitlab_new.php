<?php

require __DIR__ . '/../vendor/autoload.php';

use app\NotificationChannels\BotManager;
use app\NotificationChannels\DTOs\MessageDTO;
use App\Webhooks\Gitlab;

$botManager = new BotManager();
$debugMode = config('DEBUG', false);

$data = json_decode(file_get_contents('php://input'));

if ($debugMode) {
    file_put_contents(str_random(), json_encode($data));
}

if (is_object($data) && isset($data->event_name) && $data->event_name === 'push' && $data->total_commits_count > 0) {
    $gitlab = new Gitlab($data);
    $message = $gitlab->parseMessage();

    $chatId = config('GITLAB_CHAT_ID');
    if (isset($_REQUEST['chat_id'])) {
        $chatId = $_REQUEST['chat_id'];
    }

    $dto = new MessageDTO(
        chatId: $chatId,
        text: $message
    );

    $botName = $botManager->getDefaultBot(['bot' => config('DEFAULT_BOT', 'telegram')]);
    $driver = $botManager->resolveDriver($botName);
    $driver->sendMessage($dto);
}

echo 'success';