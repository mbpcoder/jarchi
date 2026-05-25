<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Notifications\CrispNotification;
use App\Notifications\GithubNotification;
use App\Notifications\GitlabNotification;
use App\Notifications\JiraNotification;
use App\Notifications\SentryNotification;
use App\Webhooks\Crisp;
use App\Webhooks\Gitlab;
use App\Webhooks\Github;
use App\Webhooks\Jira;
use App\Webhooks\Sentry;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use InvalidArgumentException;
use Throwable;

class WebhookController extends Controller
{
    public function handle(Request $request): JsonResponse
    {
        $provider = strtolower(trim((string) $request->input('provider', '')));
        $channel = strtolower(trim((string) ($request->input('channel') ?? $request->input('bot', ''))));
        $target = $request->input('target') ?? $request->input('chat_id');

        if ($provider === '') {
            return response()->json(['message' => 'Hello, I am Jarchi!']);
        }

        try {
            $channel = $this->normalizeChannel($channel ?: config('bot.default_driver', 'telegram'));
            $parser = $this->resolveParser($provider, json_decode($request->getContent(), false));
            $message = $this->resolveMessage($provider, $parser);

            if ($message !== '') {
                $target = $target ?: $this->getDefaultTarget($provider, $channel);

                if ($target === '') {
                    throw new InvalidArgumentException('target is required either by request or provider default config.');
                }

                $notification = $this->createNotification($provider, $channel, $message, $parser);
                Notification::route($channel, $target)->notify($notification);
            }

            return response()->json(['status' => 'success']);
        } catch (Throwable $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    private function normalizeChannel(string $channel): string
    {
        return match ($channel) {
            'email' => 'mail',
            'mail', 'log', 'telegram', 'bale', 'rocketchat' => $channel,
            default => throw new InvalidArgumentException("Unsupported channel: {$channel}"),
        };
    }

    private function resolveParser(string $provider, mixed $data): object
    {
        return match ($provider) {
            'gitlab' => new Gitlab($data),
            'github' => new Github($data),
            'jira' => new Jira($data),
            'crisp' => new Crisp($data),
            'sentry' => new Sentry($data),
            default => throw new InvalidArgumentException("Unknown provider: {$provider}"),
        };
    }

    private function resolveMessage(string $provider, object $parser): string
    {
        return $provider === 'sentry'
            ? $parser->formatSentryEventForTelegram()
            : $parser->parseMessage();
    }

    private function getDefaultTarget(string $provider, string $channel): string
    {
        if ($channel === 'log') {
            return (string) config('logging.default', 'stack');
        }

        if ($channel === 'mail') {
            return (string) (config("webhooks.{$provider}.chat_id") ?? config('mail.from.address', ''));
        }

        return (string) (config("webhooks.{$provider}.chat_id") ?? '');
    }

    private function createNotification(string $provider, string $channel, string $message, object $parser): \Illuminate\Notifications\Notification
    {
        $metadata = [
            'action_url' => $this->getParserActionUrl($parser),
            'action_label' => $this->getParserActionLabel($parser),
        ];

        return match ($provider) {
            'gitlab' => new GitlabNotification($message, $channel, $metadata),
            'github' => new GithubNotification($message, $channel, $metadata),
            'jira' => new JiraNotification($message, $channel, $metadata),
            'crisp' => new CrispNotification($message, $channel, $metadata),
            'sentry' => new SentryNotification($message, $channel, $metadata),
            default => throw new InvalidArgumentException("Unknown provider: {$provider}"),
        };
    }

    private function getParserActionUrl(object $parser): string
    {
        return method_exists($parser, 'getActionUrl')
            ? (string) $parser->getActionUrl()
            : '';
    }

    private function getParserActionLabel(object $parser): string
    {
        return method_exists($parser, 'getActionLabel')
            ? (string) $parser->getActionLabel()
            : '';
    }
}
