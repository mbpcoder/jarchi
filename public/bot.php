<?php

const MAXIMUM_CHAR_IN_MESSAGE = 4096;

function sendToTelegramBot(string $message, string $botToken, string $chatId, string|null $topicId = null): void
{
    if (mb_strlen($message) > MAXIMUM_CHAR_IN_MESSAGE) {
        $message = mb_substr($message, 0, MAXIMUM_CHAR_IN_MESSAGE - 50) . '...';
    }

    $url = 'https://api.telegram.org/bot' . $botToken . '/sendMessage';
    $data = [
        'chat_id' => $chatId,
        'text' => $message,
        'disable_web_page_preview' => true,
        'parse_mode' => 'html',
    ];

    if ($topicId !== null) {
        $data['message_thread_id'] = $topicId;
    }

    // use key 'http' even if you send the request to https:
    $options = [
        'http' => [
            'header' => "Content-type: application/x-www-form-urlencoded",
            'method' => 'POST',
            'content' => http_build_query($data),
        ],
    ];

    $context = stream_context_create($options);
    $response = file_get_contents($url, false, $context);
}

function sendToTelegramBotNew(string $message, string $botToken, string $chatId, string|null $topicId = null): void
{

    if (mb_strlen($message) > MAXIMUM_CHAR_IN_MESSAGE) {
        $message = mb_substr($message, 0, MAXIMUM_CHAR_IN_MESSAGE - 50) . '...';
    }

    $url = 'https://api.telegram.org/bot' . $botToken . '/sendMessage';

    $data = [
        'chat_id' => $chatId,
        'text' => $message,
        'disable_web_page_preview' => true,
        'parse_mode' => 'html',
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Do not verify SSL
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); // Do not verify SSL
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/x-www-form-urlencoded'
    ));
    $response = curl_exec($ch);
    curl_close($ch);
}