<?php

namespace App\Domain\Payment\Services;

use App\Domain\Order\Enums\PaymentStatus;
use App\Domain\Order\Models\Order;
use App\Domain\Order\Repositories\OrderRepositoryInterface;
use App\Domain\Payment\Enums\PaymentMethod;
use App\Domain\Payment\Enums\PaymentTransactionStatus;
use App\Domain\Payment\Events\PaymentFailed;
use App\Domain\Payment\Events\PaymentRefunded;
use App\Domain\Payment\Events\PaymentSucceeded;
use App\Domain\Payment\Models\Payment;
use App\Domain\Payment\Repositories\PaymentRepositoryInterface;
use App\Infrastructure\External\Stripe\StripeService;
use Illuminate\Support\Collection;

class PaymentService
{
    public function __construct(
        private PaymentRepositoryInterface $paymentRepository,
        private OrderRepositoryInterface $orderRepository,
        private StripeService $stripeService,
    ) {}

    public function initiatePayment(Order $order): Payment
    {
        $paymentMethod = PaymentMethod::from($order->payment_method);

        if ($paymentMethod === PaymentMethod::INVOICE) {
            return $this->createInvoicePayment($order);
        }

        return $this->createStripePayment($order, $paymentMethod);
    }

    public function simulatePayment(Order $order): Payment
    {
        // Find existing pending payment or create one
        $payments = $this->paymentRepository->findByOrder($order->id);
        $payment = $payments->first(fn (Payment $p) => $p->status === PaymentTransactionStatus::PENDING);

        if (!$payment) {
            $payment = $this->initiatePayment($order);
        }

        // Simulate success
        $payment = $this->paymentRepository->update($payment->id, [
            'status' => PaymentTransactionStatus::SUCCEEDED,
            'paid_at' => now(),
        ]);

        $this->orderRepository->update($order->id, [
            'payment_status' => PaymentStatus::PAID,
        ]);

        PaymentSucceeded::dispatch($payment);

        return $payment;
    }

    public function confirmInvoice(Order $order, string $transferNumber): Payment
    {
        $payments = $this->paymentRepository->findByOrder($order->id);
        $payment = $payments->first(fn (Payment $p) => $p->status === PaymentTransactionStatus::PENDING);

        if (!$payment) {
            throw new \InvalidArgumentException('No pending payment found for this order');
        }

        $payment = $this->paymentRepository->update($payment->id, [
            'status' => PaymentTransactionStatus::SUCCEEDED,
            'stripe_payment_intent_id' => 'invoice_' . $transferNumber,
            'paid_at' => now(),
        ]);

        $this->orderRepository->update($order->id, [
            'payment_status' => PaymentStatus::PAID,
        ]);

        PaymentSucceeded::dispatch($payment);

        return $payment;
    }

    public function getPaymentStatus(Order $order): Collection
    {
        return $this->paymentRepository->findByOrder($order->id);
    }

    public function handleStripeWebhook(array $event): void
    {
        $type = $event['type'] ?? null;
        $data = $event['data']['object'] ?? [];

        match ($type) {
            'payment_intent.succeeded' => $this->handlePaymentIntentSucceeded($data),
            'payment_intent.payment_failed' => $this->handlePaymentIntentFailed($data),
            'charge.refunded' => $this->handleChargeRefunded($data),
            default => null,
        };
    }

    public function refundPayment(Order $order): void
    {
        $payments = $this->paymentRepository->findByOrder($order->id);
        $payment = $payments->first(fn (Payment $p) => $p->status === PaymentTransactionStatus::SUCCEEDED);

        if (!$payment) {
            return;
        }

        if ($payment->stripe_payment_intent_id && !str_starts_with($payment->stripe_payment_intent_id, 'invoice_')) {
            $this->stripeService->createRefund($payment->stripe_payment_intent_id);
        }

        $payment = $this->paymentRepository->update($payment->id, [
            'status' => PaymentTransactionStatus::REFUNDED,
            'refunded_at' => now(),
        ]);

        $this->orderRepository->update($order->id, [
            'payment_status' => PaymentStatus::REFUNDED,
        ]);

        PaymentRefunded::dispatch($payment);
    }

    private function createStripePayment(Order $order, PaymentMethod $paymentMethod): Payment
    {
        $intent = $this->stripeService->createPaymentIntent(
            (float) $order->total,
            $order->currency,
            $paymentMethod->value,
            ['order_id' => $order->id, 'order_number' => $order->order_number],
        );

        $payment = $this->paymentRepository->create([
            'order_id' => $order->id,
            'user_id' => $order->user_id,
            'amount' => $order->total,
            'currency' => $order->currency,
            'payment_method' => $paymentMethod,
            'status' => PaymentTransactionStatus::PENDING,
            'stripe_payment_intent_id' => $intent['id'],
            'stripe_client_secret' => $intent['client_secret'],
        ]);

        $this->orderRepository->update($order->id, [
            'payment_status' => PaymentStatus::PROCESSING,
        ]);

        return $payment;
    }

    private function createInvoicePayment(Order $order): Payment
    {
        $payment = $this->paymentRepository->create([
            'order_id' => $order->id,
            'user_id' => $order->user_id,
            'amount' => $order->total,
            'currency' => $order->currency,
            'payment_method' => PaymentMethod::INVOICE,
            'status' => PaymentTransactionStatus::PENDING,
        ]);

        $this->orderRepository->update($order->id, [
            'payment_status' => PaymentStatus::PROCESSING,
        ]);

        return $payment;
    }

    private function handlePaymentIntentSucceeded(array $data): void
    {
        $paymentIntentId = $data['id'] ?? null;
        if (!$paymentIntentId) {
            return;
        }

        $payment = $this->paymentRepository->findByStripePaymentIntent($paymentIntentId);
        if (!$payment || $payment->status === PaymentTransactionStatus::SUCCEEDED) {
            return;
        }

        $payment = $this->paymentRepository->update($payment->id, [
            'status' => PaymentTransactionStatus::SUCCEEDED,
            'paid_at' => now(),
        ]);

        $this->orderRepository->update($payment->order_id, [
            'payment_status' => PaymentStatus::PAID,
        ]);

        PaymentSucceeded::dispatch($payment);
    }

    private function handlePaymentIntentFailed(array $data): void
    {
        $paymentIntentId = $data['id'] ?? null;
        if (!$paymentIntentId) {
            return;
        }

        $payment = $this->paymentRepository->findByStripePaymentIntent($paymentIntentId);
        if (!$payment) {
            return;
        }

        $failureReason = $data['last_payment_error']['message'] ?? 'Payment failed';

        $payment = $this->paymentRepository->update($payment->id, [
            'status' => PaymentTransactionStatus::FAILED,
            'failure_reason' => $failureReason,
        ]);

        $this->orderRepository->update($payment->order_id, [
            'payment_status' => PaymentStatus::FAILED,
        ]);

        PaymentFailed::dispatch($payment);
    }

    private function handleChargeRefunded(array $data): void
    {
        $paymentIntentId = $data['payment_intent'] ?? null;
        if (!$paymentIntentId) {
            return;
        }

        $payment = $this->paymentRepository->findByStripePaymentIntent($paymentIntentId);
        if (!$payment || $payment->status === PaymentTransactionStatus::REFUNDED) {
            return;
        }

        $payment = $this->paymentRepository->update($payment->id, [
            'status' => PaymentTransactionStatus::REFUNDED,
            'refunded_at' => now(),
        ]);

        $this->orderRepository->update($payment->order_id, [
            'payment_status' => PaymentStatus::REFUNDED,
        ]);

        PaymentRefunded::dispatch($payment);
    }
}
