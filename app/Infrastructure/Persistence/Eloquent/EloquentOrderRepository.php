<?php

namespace App\Infrastructure\Persistence\Eloquent;

use App\Domain\Order\Models\Order;
use App\Domain\Order\Repositories\OrderRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class EloquentOrderRepository implements OrderRepositoryInterface
{
    public function __construct(private Order $model) {}

    public function findById(int $id): ?Order
    {
        return $this->model->with(['user', 'items.product', 'payments', 'shippingAddress', 'billingAddress'])->find($id);
    }

    public function findByOrderNumber(string $orderNumber): ?Order
    {
        return $this->model->with(['user', 'items.product', 'payments', 'shippingAddress', 'billingAddress'])
            ->where('order_number', $orderNumber)
            ->first();
    }

    public function paginate(array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        $query = $this->model->with(['user', 'items']);

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['payment_status'])) {
            $query->where('payment_status', $filters['payment_status']);
        }

        if (!empty($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        if (!empty($filters['store_pickup_id'])) {
            $query->where('store_pickup_id', $filters['store_pickup_id']);
        }

        if (isset($filters['is_store_pickup'])) {
            $query->where('is_store_pickup', $filters['is_store_pickup']);
        }

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('first_name', 'like', "%{$search}%")
                         ->orWhere('last_name', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        $sortBy = $filters['sort_by'] ?? 'created_at';
        $sortOrder = $filters['sort_order'] ?? 'desc';
        $allowedSorts = ['created_at', 'total', 'order_number', 'status'];
        if (in_array($sortBy, $allowedSorts)) {
            $query->orderBy($sortBy, $sortOrder);
        }

        return $query->paginate($perPage);
    }

    public function paginateForUser(int $userId, array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        $filters['user_id'] = $userId;
        return $this->paginate($filters, $perPage);
    }

    public function create(array $data): Order
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): Order
    {
        $order = $this->model->findOrFail($id);
        $order->update($data);
        return $order->fresh(['user', 'items.product', 'payments', 'shippingAddress', 'billingAddress']);
    }
}
