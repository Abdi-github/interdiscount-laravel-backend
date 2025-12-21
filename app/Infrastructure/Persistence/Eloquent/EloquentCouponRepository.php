<?php

namespace App\Infrastructure\Persistence\Eloquent;

use App\Domain\Coupon\Models\Coupon;
use App\Domain\Coupon\Repositories\CouponRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class EloquentCouponRepository implements CouponRepositoryInterface
{
    public function __construct(private Coupon $model) {}

    public function findById(int $id): ?Coupon
    {
        return $this->model->find($id);
    }

    public function findByCode(string $code): ?Coupon
    {
        return $this->model->where('code', $code)->first();
    }

    public function paginate(array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        $query = $this->model->query();

        if (!empty($filters['search'])) {
            $query->where('code', 'like', "%{$filters['search']}%");
        }

        if (isset($filters['is_active'])) {
            $query->where('is_active', $filters['is_active']);
        }

        if (!empty($filters['discount_type'])) {
            $query->where('discount_type', $filters['discount_type']);
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    public function getAvailable(): Collection
    {
        return $this->model->active()->valid()->get();
    }

    public function create(array $data): Coupon
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): Coupon
    {
        $coupon = $this->model->findOrFail($id);
        $coupon->update($data);
        return $coupon->fresh();
    }

    public function delete(int $id): bool
    {
        return $this->model->findOrFail($id)->delete();
    }

    public function incrementUsage(int $id): void
    {
        $this->model->where('id', $id)->increment('used_count');
    }
}
