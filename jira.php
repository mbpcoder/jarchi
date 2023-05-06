<?php

require_once 'bot.php';

$botToken = '';
$chatId = '';

$data = json_decode(file_get_contents('php://input'));

if (isset($_GET['debug'])) {
    file_put_contents(getRandomName(), json_encode($data));
}

$domain = parse_url($data?->issue?->self, PHP_URL_SCHEME) . "://" . parse_url($data?->issue?->self, PHP_URL_HOST);

$message = '';

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

echo 'success';

sendToTelegramBot($botToken, $chatId, $message);

function getRandomName()
{
    return 'jira_' . substr(str_shuffle('abcdefghijklmnopqrstuvwxyz'), 0, 8) . '.json';
}