<?php

use App\Http\Controllers\Api\Webhook\StripeWebhookController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Webhook Routes
|--------------------------------------------------------------------------
|
| Webhook endpoints (e.g., Stripe). No auth middleware — verified via signatures.
|
*/

Route::post('/stripe', [StripeWebhookController::class, 'handle']);
