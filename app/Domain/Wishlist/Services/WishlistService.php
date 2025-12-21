<?php

namespace App\Domain\Wishlist\Services;

use App\Domain\Wishlist\Models\Wishlist;
use App\Domain\Wishlist\Repositories\WishlistRepositoryInterface;
use Illuminate\Support\Collection;

class WishlistService
{
    public function __construct(
        private WishlistRepositoryInterface $wishlistRepository,
    ) {}

    public function getByUser(int $userId): Collection
    {
        return $this->wishlistRepository->findByUser($userId);
    }

    public function add(int $userId, int $productId): Wishlist
    {
        return $this->wishlistRepository->add($userId, $productId);
    }

    public function remove(int $userId, int $productId): bool
    {
        return $this->wishlistRepository->remove($userId, $productId);
    }

    public function exists(int $userId, int $productId): bool
    {
        return $this->wishlistRepository->exists($userId, $productId);
    }
}
