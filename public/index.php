<?php
declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use App\Bots\BotManager;
use App\Bots\DTOs\MessageDTO;
use App\Webhooks\Gitlab;
use App\Webhooks\Github;
use App\Webhooks\Jira;
use App\Webhooks\Crisp;
use App\Webhooks\Sentry;

$provider = strtolower(trim($_REQUEST['provider'] ?? ''));
$bot = strtolower(trim($_REQUEST['bot'] ?? ''));
$chatId = $_REQUEST['chat_id'] ?? null;

if ($provider === '') {
    echo 'Hello, I am Jarchi!';
    exit;
}

$botManager = new BotManager();
$debugMode = config('app.debug', false);
$data = json_decode(file_get_contents('php://input'));

if ($debugMode) {
    file_put_contents(str_random($provider), json_encode($data));
}

try {
    $message = match ($provider) {
        'gitlab' => handleGitlab($data),
        'github' => handleGithub($data),
        'jira' => handleJira($data),
        'crisp' => handleCrisp($data),
        'sentry' => handleSentry($data),
        default => throw new InvalidArgumentException('Unknown provider: ' . $provider),
    };

    if ($message !== '') {
        sendMessage(
            chatId: $chatId ?? getDefaultChatId($provider),
            message: $message,
            botName: $bot ?: $botManager->getDefaultDriver(),
            botManager: $botManager
        );
    }

    echo 'success';
} catch (Throwable $e) {
    http_response_code(400);
    echo json_encode(['error' => $e->getMessage()]);
}

function handleGitlab(mixed $data): string
{
    if (!is_object($data) || !isset($data->event_name) || $data->event_name !== 'push' || empty($data->commits)) {
        return '';
    }

    $parser = new Gitlab($data);
    return $parser->parseMessage();
}

function handleGithub(mixed $data): string
{
    if (!is_object($data) || !isset($data->commits) || empty($data->commits)) {
        return '';
    }

    $parser = new Github($data);
    return $parser->parseMessage();
}

function handleJira(mixed $data): string
{
    if (!is_object($data) || !isset($data->webhookEvent)) {
        return '';
    }

    $parser = new Jira($data);
    return $parser->parseMessage();
}

function handleCrisp(mixed $data): string
{
    if (!is_object($data) || !isset($data->data)) {
        return '';
    }

    $parser = new Crisp($data);
    return $parser->parseMessage();
}

function handleSentry(mixed $data): string
{
    if (!is_object($data) || (!isset($data->event_name) && !isset($data->event))) {
        return '';
    }

    $parser = new Sentry($data);
    return $parser->parseMessage();
}

function getDefaultChatId(string $provider): string
{
    return (string) (config("webhooks.{$provider}.chat_id") ?? '');
}

function sendMessage(string $chatId, string $message, string $botName, BotManager $botManager): void
{
    if ($chatId === '') {
        throw new InvalidArgumentException('chat_id is required either by request or provider default config.');
    }

    $dto = new MessageDTO(
        chatId: $chatId,
        text: $message
    );

    $driver = $botManager->resolveDriver($botName);
    $driver->sendMessage($dto);
}
