<?php

require __DIR__ . '/../vendor/autoload.php';

$botToken = config('CRISP_BOT_TOKEN');
$chatId = config('CRISP_CHAT_ID');
$topicId = config('CRISP_TOPIC_ID');
$debugMode = config('DEBUG', false);

$data = json_decode(file_get_contents('php://input'));

if ($debugMode) {
    file_put_contents(getRandomName(), json_encode($data));
}

if (is_object($data) && isset($data->data)) {

    $message = '';

    $chatUrl = "https://app.crisp.chat/website/{$data->data->website_id}/inbox/{$data->data->session_id}/";
    $chatUrlTitle = 'Unknown user';

    if (isset($data->data->user->nickname)) {
        $chatUrlTitle = $data->data->user->nickname;
    }
    $message .= '<b><a href="' . $chatUrl . '">' . $chatUrlTitle . '</a></b>' . PHP_EOL;

    switch ($data->event) {
        case 'session:set_phone':
            $message .= 'Register phone number in Crisp ' . '<code>' . $data->data->phone . '</code>' . PHP_EOL;
            break;
    }

    if (isset($data->data->content)) {
        $message .= $data->data->content . PHP_EOL;
    }

    sendToTelegramBot($message, $botToken, $chatId, $topicId);
}

echo 'success';


function getRandomName()
{
    return __DIR__ . '/../tmp/' . 'crisp_' . substr(str_shuffle('abcdefghijklmnopqrstuvwxyz'), 0, 8) . '.json';
}
