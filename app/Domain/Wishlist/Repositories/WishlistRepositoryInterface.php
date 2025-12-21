<?php

namespace App\Domain\Wishlist\Repositories;

use App\Domain\Wishlist\Models\Wishlist;
use Illuminate\Support\Collection;

interface WishlistRepositoryInterface
{
    public function findByUser(int $userId): Collection;
    public function add(int $userId, int $productId): Wishlist;
    public function remove(int $userId, int $productId): bool;
    public function exists(int $userId, int $productId): bool;
}
