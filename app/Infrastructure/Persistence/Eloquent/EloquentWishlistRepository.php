<?php

namespace App\Infrastructure\Persistence\Eloquent;

use App\Domain\Wishlist\Models\Wishlist;
use App\Domain\Wishlist\Repositories\WishlistRepositoryInterface;
use Illuminate\Support\Collection;

class EloquentWishlistRepository implements WishlistRepositoryInterface
{
    public function __construct(private Wishlist $model) {}

    public function findByUser(int $userId): Collection
    {
        return $this->model->with(['product.brand', 'product.category'])
            ->where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function add(int $userId, int $productId): Wishlist
    {
        return $this->model->firstOrCreate([
            'user_id' => $userId,
            'product_id' => $productId,
        ]);
    }

    public function remove(int $userId, int $productId): bool
    {
        return (bool) $this->model
            ->where('user_id', $userId)
            ->where('product_id', $productId)
            ->delete();
    }

    public function exists(int $userId, int $productId): bool
    {
        return $this->model
            ->where('user_id', $userId)
            ->where('product_id', $productId)
            ->exists();
    }
}
