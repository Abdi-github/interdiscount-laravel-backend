<?php

namespace App\Domain\Payment\Repositories;

use App\Domain\Payment\Models\Payment;
use Illuminate\Support\Collection;

interface PaymentRepositoryInterface
{
    public function findById(int $id): ?Payment;
    public function findByOrder(int $orderId): Collection;
    public function findByStripePaymentIntent(string $paymentIntentId): ?Payment;
    public function create(array $data): Payment;
    public function update(int $id, array $data): Payment;
}
