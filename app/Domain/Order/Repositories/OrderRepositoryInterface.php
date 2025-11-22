<?php

namespace App\Domain\Order\Repositories;

use App\Domain\Order\Models\Order;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface OrderRepositoryInterface
{
    public function findById(int $id): ?Order;
    public function findByOrderNumber(string $orderNumber): ?Order;
    public function paginate(array $filters = [], int $perPage = 20): LengthAwarePaginator;
    public function paginateForUser(int $userId, array $filters = [], int $perPage = 20): LengthAwarePaginator;
    public function create(array $data): Order;
    public function update(int $id, array $data): Order;
}
