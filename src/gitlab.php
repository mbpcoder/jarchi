<?php

require __DIR__ . '/../vendor/autoload.php';

$botToken = config('GITLAB_BOT_TOKEN');
$chatId = config('GITLAB_CHAT_ID');
$debugMode = config('DEBUG', false);

$data = json_decode(file_get_contents('php://input'));

if ($debugMode) {
    file_put_contents(getRandomName(), json_encode($data));
}

if (is_object($data) && isset($data->event_name) && $data->event_name === 'push' && $data->total_commits_count > 0) {

    // send to telegram
    $message = '<b>' . $data->user_name . '</b>' . ' pushed to ' . $data->project->path_with_namespace . ' ' . getBranchName($data) . PHP_EOL;
    foreach ($data->commits as $_commit) {
        if ($data->user_name !== $_commit->author->name) {
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
    return __DIR__ . '/../tmp/' . 'push_' . substr(str_shuffle('abcdefghijklmnopqrstuvwxyz'), 0, 8) . '.json';
}
