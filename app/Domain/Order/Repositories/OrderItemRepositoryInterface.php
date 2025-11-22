<?php

namespace App\Domain\Order\Repositories;

use App\Domain\Order\Models\OrderItem;
use Illuminate\Support\Collection;

interface OrderItemRepositoryInterface
{
    public function findById(int $id): ?OrderItem;
    public function findByOrder(int $orderId): Collection;
    public function create(array $data): OrderItem;
    public function createMany(array $items): Collection;
}
