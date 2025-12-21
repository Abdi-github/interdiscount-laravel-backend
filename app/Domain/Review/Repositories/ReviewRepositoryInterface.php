<?php

namespace App\Domain\Review\Repositories;

use App\Domain\Review\Models\Review;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface ReviewRepositoryInterface
{
    public function findById(int $id): ?Review;
    public function paginateByProduct(int $productId, array $filters = [], int $perPage = 20): LengthAwarePaginator;
    public function paginateByUser(int $userId, array $filters = [], int $perPage = 20): LengthAwarePaginator;
    public function paginate(array $filters = [], int $perPage = 20): LengthAwarePaginator;
    public function create(array $data): Review;
    public function update(int $id, array $data): Review;
    public function delete(int $id): bool;
}
