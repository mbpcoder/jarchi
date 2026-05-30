<?php

declare(strict_types=1);

namespace App\Http\Controllers\Webhooks;

use App\Notifications\CrispNotification;
use App\Webhooks\Crisp;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use InvalidArgumentException;
use Throwable;

class CrispController extends WebhookBaseController
{
    public function handle(Request $request): JsonResponse
    {
        $channel = strtolower(trim((string) ($request->input('channel') ?? $request->input('bot', ''))));
        $target = $request->input('target') ?? $request->input('chat_id');

        try {
            $channel = $this->normalizeChannel($channel ?: config('bot.default_driver', 'telegram'));
            $parser = new Crisp(json_decode($request->getContent(), false));
            $message = $parser->parseMessage();

            if ($message !== '') {
                $target = $target ?: $this->getDefaultTarget('crisp', $channel);

                if ($target === '') {
                    throw new InvalidArgumentException('target is required either by request or provider default config.');
                }

                $metadata = [
                    'action_url' => $this->getParserActionUrl($parser),
                    'action_label' => $this->getParserActionLabel($parser),
                ];

                $notification = new CrispNotification($message, $channel, $metadata);
                Notification::route($channel, $target)->notify($notification);
            }

            return response()->json(['status' => 'success']);
        } catch (Throwable $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}
