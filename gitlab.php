<?php

$data = json_decode(file_get_contents('php://input'));

file_put_contents(getRandomName(), json_encode($data));

if (is_object($data) && isset($data->event_name) && $data->event_name === 'push' && $data->total_commits_count > 0) {

    // send to telegram
    $message = '<b>' . $data->user_name . '</b>' . ' pushed to ' . $data->project->path_with_namespace . ' ' . getBranchName($data) . PHP_EOL;
    foreach ($data->commits as $_commit) {
        if ($data->user_name !== $_commit->author->name) {
            $message .= '<b>' . $_commit->author->name . '</b>' . ': ';
        }
        $message .= '<a href="' . $_commit->url . '">' . $_commit->message . '</a>';
    }

    sendToBot($message);

}

echo 'success';

function getBranchName($data)
{
    return substr($data->ref, strrpos($data->ref, '/') + 1);
}

function sendToBot($message)
{
    $chatId = '';
    $botToken = '';

    $url = 'https://api.telegram.org/bot' . $botToken . '/sendMessage';
    $data = [
        'chat_id' => $chatId,
        'text' => $message,
        'disable_web_page_preview' => true,
        'parse_mode' => 'html',
    ];

    // use key 'http' even if you send the request to https:
    $options = [
        'http' => [
            'header' => "Content-type: application/x-www-form-urlencoded\r\n",
            'method' => 'POST',
            'content' => http_build_query($data),
        ],
    ];

    $context = stream_context_create($options);
    $response = file_get_contents($url, false, $context);
}

function getRandomName()
{
    return 'push_' . substr(str_shuffle('abcdefghijklmnopqrstuvwxyz'), 0, 8) . '.json';
}