<?php

namespace App\Domain\Coupon\Repositories;

use App\Domain\Coupon\Models\Coupon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface CouponRepositoryInterface
{
    public function findById(int $id): ?Coupon;
    public function findByCode(string $code): ?Coupon;
    public function paginate(array $filters = [], int $perPage = 20): LengthAwarePaginator;
    public function getAvailable(): Collection;
    public function create(array $data): Coupon;
    public function update(int $id, array $data): Coupon;
    public function delete(int $id): bool;
    public function incrementUsage(int $id): void;
}
