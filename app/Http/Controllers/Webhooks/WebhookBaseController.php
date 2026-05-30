<?php

declare(strict_types=1);

namespace App\Http\Controllers\Webhooks;

use App\Http\Controllers\Controller;
use InvalidArgumentException;

abstract class WebhookBaseController extends Controller
{
    protected function normalizeChannel(string $channel): string
    {
        return match ($channel) {
            'email' => 'mail',
            'mail', 'log', 'telegram', 'bale', 'rocketchat' => $channel,
            default => throw new InvalidArgumentException("Unsupported channel: {$channel}"),
        };
    }

    protected function getDefaultTarget(string $provider, string $channel): string
    {
        if ($channel === 'log') {
            return (string) config('logging.default', 'stack');
        }

        if ($channel === 'mail') {
            return (string) (config("webhooks.{$provider}.chat_id") ?? config('mail.from.address', ''));
        }

        return (string) (config("webhooks.{$provider}.chat_id") ?? '');
    }

    protected function getParserActionUrl(object $parser): string
    {
        return method_exists($parser, 'getActionUrl')
            ? (string) $parser->getActionUrl()
            : '';
    }

    protected function getParserActionLabel(object $parser): string
    {
        return method_exists($parser, 'getActionLabel')
            ? (string) $parser->getActionLabel()
            : '';
    }
}
