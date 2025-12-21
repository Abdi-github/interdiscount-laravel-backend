<?php

namespace App\Infrastructure\Persistence\Eloquent;

use App\Domain\Payment\Models\Payment;
use App\Domain\Payment\Repositories\PaymentRepositoryInterface;
use Illuminate\Support\Collection;

class EloquentPaymentRepository implements PaymentRepositoryInterface
{
    public function __construct(private Payment $model) {}

    public function findById(int $id): ?Payment
    {
        return $this->model->with(['order', 'user'])->find($id);
    }

    public function findByOrder(int $orderId): Collection
    {
        return $this->model->where('order_id', $orderId)->orderBy('created_at', 'desc')->get();
    }

    public function findByStripePaymentIntent(string $paymentIntentId): ?Payment
    {
        return $this->model->where('stripe_payment_intent_id', $paymentIntentId)->first();
    }

    public function create(array $data): Payment
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): Payment
    {
        $payment = $this->model->findOrFail($id);
        $payment->update($data);
        return $payment->fresh(['order', 'user']);
    }
}
