<?php

namespace App\Domain\Search\Services;

use App\Domain\Product\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class SearchService
{
    public function search(string $query, array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        $search = Product::search($query);

        if (!empty($filters['category_id'])) {
            $search->where('category_id', $filters['category_id']);
        }

        if (!empty($filters['brand_id'])) {
            $search->where('brand_id', $filters['brand_id']);
        }

        if (isset($filters['min_price'])) {
            $search->where('price', '>=', (float) $filters['min_price']);
        }

        if (isset($filters['max_price'])) {
            $search->where('price', '<=', (float) $filters['max_price']);
        }

        if (!empty($filters['availability_state'])) {
            $search->where('availability_state', $filters['availability_state']);
        }

        // Only return active, published products
        $search->where('is_active', true);
        $search->where('status', 'PUBLISHED');

        return $search->paginate($perPage);
    }
}
