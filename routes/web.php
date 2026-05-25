<?php

declare(strict_types=1);

use App\Http\Controllers\WebhookController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json(['message' => 'Hello, I am Jarchi!']);
});

Route::post('/webhook', [WebhookController::class, 'handle']);
