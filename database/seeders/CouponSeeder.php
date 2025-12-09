<?php

namespace Database\Seeders;

use App\Domain\Coupon\Models\Coupon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class CouponSeeder extends Seeder
{
    public function run(): void
    {
        $coupons = json_decode(file_get_contents(base_path('data/coupons.json')), true);

        foreach ($coupons as $coupon) {
            Coupon::create([
                'code' => $coupon['code'],
                'description' => $coupon['description'],
                'discount_type' => $coupon['discount_type'],
                'discount_value' => $coupon['discount_value'],
                'minimum_order' => $coupon['minimum_order'] ?? null,
                'max_uses' => $coupon['max_uses'] ?? null,
                'used_count' => $coupon['used_count'] ?? 0,
                'valid_from' => isset($coupon['valid_from']) ? Carbon::parse($coupon['valid_from']) : null,
                'valid_until' => isset($coupon['valid_until']) ? Carbon::parse($coupon['valid_until']) : null,
                'is_active' => $coupon['is_active'] ?? true,
                'created_at' => isset($coupon['created_at']) ? Carbon::parse($coupon['created_at']) : now(),
                'updated_at' => isset($coupon['updated_at']) ? Carbon::parse($coupon['updated_at']) : now(),
            ]);
        }
    }
}
