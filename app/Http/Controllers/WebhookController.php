<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\NotificationChannels\BotManager;
use App\NotificationChannels\DTOs\MessageDTO;
use App\Webhooks\Gitlab;
use App\Webhooks\Github;
use App\Webhooks\Jira;
use App\Webhooks\Crisp;
use App\Webhooks\Sentry;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use InvalidArgumentException;
use Throwable;

class WebhookController extends Controller
{
    public function __construct(
        private BotManager $botManager
    ) {}

    public function handle(Request $request): JsonResponse
    {
        $provider = strtolower(trim($request->input('provider', '')));
        $bot = strtolower(trim($request->input('bot', '')));
        $chatId = $request->input('chat_id');

        if ($provider === '') {
            return response()->json(['message' => 'Hello, I am Jarchi!']);
        }

        try {
            $data = $request->getContent();
            $data = json_decode($data, false);

            $message = match ($provider) {
                'gitlab' => $this->handleGitlab($data),
                'github' => $this->handleGithub($data),
                'jira' => $this->handleJira($data),
                'crisp' => $this->handleCrisp($data),
                'sentry' => $this->handleSentry($data),
                default => throw new InvalidArgumentException('Unknown provider: ' . $provider),
            };

            if ($message !== '') {
                $this->sendMessage(
                    chatId: $chatId ?? $this->getDefaultChatId($provider),
                    message: $message,
                    botName: $bot ?: $this->botManager->getDefaultDriver()
                );
            }

            return response()->json(['status' => 'success']);
        } catch (Throwable $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    private function handleGitlab(mixed $data): string
    {
        if (!is_object($data) || !isset($data->event_name) || $data->event_name !== 'push' || empty($data->commits)) {
            return '';
        }

        $parser = new Gitlab($data);
        return $parser->parseMessage();
    }

    private function handleGithub(mixed $data): string
    {
        if (!is_object($data) || !isset($data->commits) || empty($data->commits)) {
            return '';
        }

        $parser = new Github($data);
        return $parser->parseMessage();
    }

    private function handleJira(mixed $data): string
    {
        if (!is_object($data) || !isset($data->webhookEvent)) {
            return '';
        }

        $parser = new Jira($data);
        return $parser->parseMessage();
    }

    private function handleCrisp(mixed $data): string
    {
        if (!is_object($data) || !isset($data->data)) {
            return '';
        }

        $parser = new Crisp($data);
        return $parser->parseMessage();
    }

    private function handleSentry(mixed $data): string
    {
        $parser = new Sentry($data);
        return $parser->formatSentryEventForTelegram();
    }

    private function getDefaultChatId(string $provider): string
    {
        return (string) (config("webhooks.{$provider}.chat_id") ?? '');
    }

    private function sendMessage(string $chatId, string $message, string $botName): void
    {
        if ($chatId === '') {
            throw new InvalidArgumentException('chat_id is required either by request or provider default config.');
        }

        $dto = new MessageDTO(
            chatId: $chatId,
            text: $message
        );

        $driver = $this->botManager->resolveDriver($botName);
        $driver->sendMessage($dto);
    }
}
