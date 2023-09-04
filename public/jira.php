<?php

require __DIR__ . '/../vendor/autoload.php';

$botToken = config('JIRA_BOT_TOKEN');
$chatId = config('JIRA_CHAT_ID');
$debugMode = config('DEBUG', false);

$data = json_decode(file_get_contents('php://input'));

if ($debugMode) {
    file_put_contents(getRandomName(), json_encode($data));
}

if (is_object($data)) {
    $domain = parse_url($data?->issue?->self, PHP_URL_SCHEME) . "://" . parse_url($data?->issue?->self, PHP_URL_HOST);
    switch ($data->webhookEvent) {
        case 'comment_created':
            $message = getCommentMessage($domain, $data);
            break;
        default:
            $message = getTaskMessage($domain, $data);
            break;
    }
    sendToTelegramBot($botToken, $chatId, $message);
}

echo 'success';


function getRandomName(): string
{
    return __DIR__ . '/../tmp/' . 'jira_' . substr(str_shuffle('abcdefghijklmnopqrstuvwxyz'), 0, 8) . '.json';
}

function getTaskMessage(string $domain, object $data): string
{
    $message = $data->webhookEvent . PHP_EOL;

    $userUrl = $domain . '/secure/ViewProfile.jspa?name=' . $data?->user?->name;
    $message .= '<b><a href="' . $userUrl . '">' . $data?->user?->displayName . '</a></b> => ';

    $projectUrl = $domain . '/projects/' . $data?->issue?->fields?->project?->key;
    $message .= '<a href="' . $projectUrl . '">' . $data?->issue?->fields?->project?->name . '</a>' . PHP_EOL;

    $message .= $data?->issue?->fields?->project?->name . PHP_EOL;

    if (!empty($data?->issue?->fields?->status?->name)) {
        $message .= 'List: ' . $data?->issue?->fields?->status?->name . PHP_EOL;
    }

    if (!empty($data?->issue?->fields?->assignee->displayName)) {
        $message .= 'Assign: ' . $data?->issue?->fields?->assignee?->displayName . PHP_EOL;
    }

    if (!empty($data?->issue?->fields?->summary)) {
        $cardUrl = $domain . '/browse/' . $data?->issue?->key;
        $message .= '<b><a href="' . $cardUrl . '">Summery: </a></b>' . strip_tags($data?->issue?->fields?->summary) . PHP_EOL;
    }

    if (!empty($data?->issue?->fields?->description)) {
        $message .= '<b>Description: </b>' . strip_tags($data?->issue?->fields?->description) . PHP_EOL;
    }

    return $message;

}

function getCommentMessage(string $domain, object $data): string
{
    $message = $data->webhookEvent . PHP_EOL;

    $userUrl = $domain . '/secure/ViewProfile.jspa?name=' . $data?->comment?->author?->name;
    $message .= '<b><a href="' . $userUrl . '">' . $data?->comment?->author?->displayName . '</a></b>' . PHP_EOL;

    if (!empty($data?->comment?->body)) {
        $message .= '<b>Description: </b>' . strip_tags($data?->comment?->body) . PHP_EOL;
    }

    return $message;
}