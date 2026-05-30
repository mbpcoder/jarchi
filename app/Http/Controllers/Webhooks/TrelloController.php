<?php

declare(strict_types=1);

namespace App\Http\Controllers\Webhooks;

use App\Webhooks\Trello;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use InvalidArgumentException;
use Throwable;

class TrelloController extends WebhookBaseController
{
    public function handle(Request $request): JsonResponse
    {
        try {
            $parser = new Trello(json_decode($request->getContent(), false));
            $message = $parser->parseMessage();

            if ($message === '') {
                return response()->json(['status' => 'success']);
            }

            return response()->json(['status' => 'success']);
        } catch (Throwable $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}
