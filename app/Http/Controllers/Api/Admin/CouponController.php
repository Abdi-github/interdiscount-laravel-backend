<?php

namespace App\Http\Controllers\Api\Admin;

use App\Domain\Coupon\Services\CouponService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Coupon\StoreCouponRequest;
use App\Http\Requests\Coupon\UpdateCouponRequest;
use App\Http\Resources\CouponResource;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    use ApiResponse;

    public function __construct(
        private CouponService $couponService,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $coupons = $this->couponService->paginate(
            $request->only(['search', 'is_active']),
            $request->integer('limit', 20),
        );

        return $this->paginated($coupons, 'CouponResource', 'Coupons retrieved successfully');
    }

    public function store(StoreCouponRequest $request): JsonResponse
    {
        /* Log::debug('CouponController store - creating coupon'); */
        $coupon = $this->couponService->create($request->validated());
        // TODO: Send coupon creation notification to managers

        return $this->created(new CouponResource($coupon), 'Coupon created successfully');
    }

    public function show(int $id): JsonResponse
    {
        $coupon = $this->couponService->findById($id);

        if (!$coupon) {
            return $this->notFound('Coupon not found');
        }

        return $this->success(new CouponResource($coupon), 'Coupon retrieved successfully');
    }

    public function update(UpdateCouponRequest $request, int $id): JsonResponse
    {
        $coupon = $this->couponService->findById($id);

        if (!$coupon) {
            return $this->notFound('Coupon not found');
        }

        $coupon = $this->couponService->update($id, $request->validated());

        return $this->success(new CouponResource($coupon), 'Coupon updated successfully');
    }

    public function destroy(int $id): JsonResponse
    {
        $coupon = $this->couponService->findById($id);

        if (!$coupon) {
            return $this->notFound('Coupon not found');
        }

        $this->couponService->delete($id);

        return $this->success(null, 'Coupon deleted successfully');
    }
}
