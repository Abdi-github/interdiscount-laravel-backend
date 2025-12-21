<?php

namespace App\Infrastructure\External\Stripe;

use Illuminate\Support\Str;

class StripeService
{
    /**
     * Create a PaymentIntent (simulated in dev, real Stripe in production).
     */
    public function createPaymentIntent(float $amount, string $currency, string $paymentMethod, array $metadata = []): array
    {
        if (app()->environment('production') && config('services.stripe.secret')) {
            return $this->createRealPaymentIntent($amount, $currency, $paymentMethod, $metadata);
        }

        return $this->createSimulatedPaymentIntent($amount, $currency, $paymentMethod, $metadata);
    }

    /**
     * Confirm a PaymentIntent (simulated in dev).
     */
    public function confirmPaymentIntent(string $paymentIntentId): array
    {
        if (app()->environment('production') && config('services.stripe.secret')) {
            return $this->confirmRealPaymentIntent($paymentIntentId);
        }

        return [
            'id' => $paymentIntentId,
            'status' => 'succeeded',
            'amount' => 0,
            'currency' => 'chf',
        ];
    }

    /**
     * Create a refund (simulated in dev).
     */
    public function createRefund(string $paymentIntentId, ?float $amount = null): array
    {
        if (app()->environment('production') && config('services.stripe.secret')) {
            return $this->createRealRefund($paymentIntentId, $amount);
        }

        return [
            'id' => 're_' . Str::random(24),
            'payment_intent' => $paymentIntentId,
            'amount' => $amount ? (int) ($amount * 100) : 0,
            'currency' => 'chf',
            'status' => 'succeeded',
        ];
    }

    /**
     * Verify a webhook signature.
     */
    public function verifyWebhookSignature(string $payload, string $signature): array
    {
        $webhookSecret = config('services.stripe.webhook_secret');

        if (app()->environment('production') && $webhookSecret) {
            $event = \Stripe\Webhook::constructEvent($payload, $signature, $webhookSecret);
            return json_decode(json_encode($event), true);
        }

        // Dev: trust the payload
        return json_decode($payload, true);
    }

    private function createSimulatedPaymentIntent(float $amount, string $currency, string $paymentMethod, array $metadata): array
    {
        $id = 'pi_simulated_' . Str::random(24);

        return [
            'id' => $id,
            'client_secret' => $id . '_secret_' . Str::random(16),
            'amount' => (int) ($amount * 100),
            'currency' => strtolower($currency),
            'status' => 'requires_confirmation',
            'payment_method_types' => [$paymentMethod],
            'metadata' => $metadata,
        ];
    }

    private function createRealPaymentIntent(float $amount, string $currency, string $paymentMethod, array $metadata): array
    {
        \Stripe\Stripe::setApiKey(config('services.stripe.secret'));

        $paymentMethodTypes = match ($paymentMethod) {
            'twint' => ['twint'],
            'postfinance' => ['postfinance_pay'],
            default => ['card'],
        };

        $intent = \Stripe\PaymentIntent::create([
            'amount' => (int) ($amount * 100),
            'currency' => strtolower($currency),
            'payment_method_types' => $paymentMethodTypes,
            'metadata' => $metadata,
        ]);

        return [
            'id' => $intent->id,
            'client_secret' => $intent->client_secret,
            'amount' => $intent->amount,
            'currency' => $intent->currency,
            'status' => $intent->status,
            'payment_method_types' => $intent->payment_method_types,
            'metadata' => $intent->metadata->toArray(),
        ];
    }

    private function confirmRealPaymentIntent(string $paymentIntentId): array
    {
        \Stripe\Stripe::setApiKey(config('services.stripe.secret'));

        $intent = \Stripe\PaymentIntent::retrieve($paymentIntentId);
        $intent->confirm();

        return [
            'id' => $intent->id,
            'status' => $intent->status,
            'amount' => $intent->amount,
            'currency' => $intent->currency,
        ];
    }

    private function createRealRefund(string $paymentIntentId, ?float $amount): array
    {
        \Stripe\Stripe::setApiKey(config('services.stripe.secret'));

        $params = ['payment_intent' => $paymentIntentId];
        if ($amount !== null) {
            $params['amount'] = (int) ($amount * 100);
        }

        $refund = \Stripe\Refund::create($params);

        return [
            'id' => $refund->id,
            'payment_intent' => $paymentIntentId,
            'amount' => $refund->amount,
            'currency' => $refund->currency,
            'status' => $refund->status,
        ];
    }
}
