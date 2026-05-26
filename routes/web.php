<?php

declare(strict_types=1);

use App\Http\Controllers\WebhookController;
use App\Http\Controllers\Webhooks\CrispController;
use App\Http\Controllers\Webhooks\GithubController;
use App\Http\Controllers\Webhooks\GitlabController;
use App\Http\Controllers\Webhooks\JiraController;
use App\Http\Controllers\Webhooks\SentryController;
use App\Http\Controllers\Webhooks\TrelloController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json(['message' => 'Hello, I am Jarchi!']);
});

// Provider-specific webhook routes
Route::post('/webhooks/gitlab', [GitlabController::class, 'handle']);
Route::post('/webhooks/github', [GithubController::class, 'handle']);
Route::post('/webhooks/jira', [JiraController::class, 'handle']);
Route::post('/webhooks/crisp', [CrispController::class, 'handle']);
Route::post('/webhooks/sentry', [SentryController::class, 'handle']);
Route::post('/webhooks/trello', [TrelloController::class, 'handle']);

// Legacy webhook route (backward compatibility)
Route::post('/webhook', [WebhookController::class, 'handle']);

