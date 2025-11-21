<?php

namespace App\Http\Controllers\Api\Webhook;

use App\Domain\Payment\Services\PaymentService;
use App\Http\Controllers\Controller;
use App\Infrastructure\External\Stripe\StripeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class StripeWebhookController extends Controller
{
    public function __construct(
        private PaymentService $paymentService,
        private StripeService $stripeService,
    ) {}

    public function handle(Request $request): JsonResponse
    {
        $payload = $request->getContent();
        $signature = $request->header('Stripe-Signature', '');

        try {
            $event = $this->stripeService->verifyWebhookSignature($payload, $signature);
        } catch (\Exception $e) {
            Log::error('Stripe webhook signature verification failed', [
                'error' => $e->getMessage(),
            ]);
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        try {
            $this->paymentService->handleStripeWebhook($event);
        } catch (\Exception $e) {
            Log::error('Stripe webhook processing failed', [
                'type' => $event['type'] ?? 'unknown',
                'error' => $e->getMessage(),
            ]);
            return response()->json(['error' => 'Webhook processing failed'], 500);
        }

        return response()->json(['received' => true]);
    }
}
