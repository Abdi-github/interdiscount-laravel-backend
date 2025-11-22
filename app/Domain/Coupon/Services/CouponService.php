<?php

namespace App\Domain\Coupon\Services;

use App\Domain\Coupon\Models\Coupon;
use App\Domain\Coupon\Repositories\CouponRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class CouponService
{
    public function __construct(
        private CouponRepositoryInterface $couponRepository,
    ) {}

    public function findById(int $id): ?Coupon
    {
        return $this->couponRepository->findById($id);
    }

    public function paginate(array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        return $this->couponRepository->paginate($filters, $perPage);
    }

    public function create(array $data): Coupon
    {
        return $this->couponRepository->create($data);
    }

    public function update(int $id, array $data): Coupon
    {
        return $this->couponRepository->update($id, $data);
    }

    public function delete(int $id): bool
    {
        return $this->couponRepository->delete($id);
    }

    public function validate(string $code, ?float $orderTotal = null): array
    {
        $coupon = $this->couponRepository->findByCode($code);

        if (!$coupon) {
            return ['valid' => false, 'message' => 'Coupon not found'];
        }

        if (!$coupon->isValid()) {
            return ['valid' => false, 'message' => 'Coupon is not valid or has expired'];
        }

        if ($orderTotal !== null && $coupon->minimum_order && $orderTotal < (float) $coupon->minimum_order) {
            return [
                'valid' => false,
                'message' => "Minimum order amount of CHF {$coupon->minimum_order} required",
            ];
        }

        return [
            'valid' => true,
            'message' => 'Coupon is valid',
            'coupon' => $coupon,
        ];
    }

    public function getAvailable(): Collection
    {
        return $this->couponRepository->getAvailable();
    }
}
