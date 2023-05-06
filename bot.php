<?php

function sendToTelegramBot(string $botToken,string $chatId,string $message): void
{
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