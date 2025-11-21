<?php

namespace App\Http\Controllers\Api\Customer;

use App\Domain\Coupon\Services\CouponService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Coupon\ValidateCouponRequest;
use App\Http\Resources\CouponResource;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;

class CouponController extends Controller
{
    use ApiResponse;

    public function __construct(
        private CouponService $couponService,
    ) {}

    public function validate(ValidateCouponRequest $request): JsonResponse
    {
        $result = $this->couponService->validate(
            $request->validated('code'),
            $request->validated('order_total'),
        );

        if (!$result['valid']) {
            return $this->error($result['message'], 422);
        }

        return $this->success(
            new CouponResource($result['coupon']),
            $result['message'],
        );
    }

    public function available(): JsonResponse
    {
        $coupons = $this->couponService->getAvailable();

        return $this->success(
            CouponResource::collection($coupons),
            'Available coupons retrieved successfully',
        );
    }
}
