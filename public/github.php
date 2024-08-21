<?php

require __DIR__ . '/../vendor/autoload.php';

$botToken = config('GITHUB_BOT_TOKEN');
$chatId = config('GITHUB_CHAT_ID');
$debugMode = config('DEBUG', false);

if (isset($_REQUEST['chat_id'])) {
    $chatId = $_REQUEST['chat_id'];
}

$data = json_decode(file_get_contents('php://input'));

if ($debugMode) {
    file_put_contents(getRandomName(), json_encode($data));
}

if (is_object($data) && isset($data->commits) && $data->commits > 0) {

    // send to telegram
    $message = '<b>' . $data->head_commit->author->name . '</b>' . ' pushed to ' . $data->repository->full_name . ' ' . getBranchName($data) . PHP_EOL;
    foreach ($data->commits as $_commit) {
        if ($data->head_commit->author->name !== $_commit->author->name) {
            $message .= '<b>' . $_commit->author->name . '</b>' . ': ';
        }
        $message .= '<a href="' . $_commit->url . '">' . $_commit->message . '</a>';
    }

    sendToTelegramBot($message, $botToken, $chatId);
}

echo 'success';

function getBranchName($data)
{
    return substr($data->ref, strrpos($data->ref, '/') + 1);
}


function getRandomName()
{
    return __DIR__ . '/../tmp/' . 'github_' . substr(str_shuffle('abcdefghijklmnopqrstuvwxyz'), 0, 8) . '.json';
}
