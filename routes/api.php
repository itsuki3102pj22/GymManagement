<?php

use App\Http\Controllers\LineWebhookController;
use Illuminate\Support\Facades\Route;

// LINE Webhook（セッション・CSRF・認証不要）
Route::post('/webhook/line', [LineWebhookController::class, 'handle'])
    ->name('webhook.line');