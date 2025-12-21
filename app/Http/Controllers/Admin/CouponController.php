<?php

namespace App\Http\Controllers\Admin;

use App\Domain\Coupon\Services\CouponService;
use App\Http\Controllers\Controller;
use App\Http\Resources\CouponResource;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CouponController extends Controller
{
    public function __construct(private CouponService $couponService) {}

    public function index(Request $request): Response
    {
        $coupons = $this->couponService->paginate(
            $request->only(['search', 'is_active', 'discount_type']),
            (int) $request->input('per_page', 20)
        );

        return Inertia::render('Coupons/Index', [
            'coupons' => CouponResource::collection($coupons),
            'filters' => $request->only(['search', 'is_active', 'discount_type', 'per_page']),
        ]);
    }
}
