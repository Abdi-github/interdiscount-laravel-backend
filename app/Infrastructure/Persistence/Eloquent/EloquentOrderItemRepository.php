<?php

namespace App\Infrastructure\Persistence\Eloquent;

use App\Domain\Order\Models\OrderItem;
use App\Domain\Order\Repositories\OrderItemRepositoryInterface;
use Illuminate\Support\Collection;

class EloquentOrderItemRepository implements OrderItemRepositoryInterface
{
    public function __construct(private OrderItem $model) {}

    public function findById(int $id): ?OrderItem
    {
        return $this->model->with(['product'])->find($id);
    }

    public function findByOrder(int $orderId): Collection
    {
        return $this->model->with(['product'])->where('order_id', $orderId)->get();
    }

    public function create(array $data): OrderItem
    {
        return $this->model->create($data);
    }

    public function createMany(array $items): Collection
    {
        $created = collect();
        foreach ($items as $item) {
            $created->push($this->model->create($item));
        }
        return $created;
    }
}
