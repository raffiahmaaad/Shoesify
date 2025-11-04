<?php

use App\Http\Controllers\Webhooks\MidtransWebhookController;
use Illuminate\Support\Facades\Route;

Route::post('/webhooks/midtrans', MidtransWebhookController::class)
    ->name('api.webhooks.midtrans');
