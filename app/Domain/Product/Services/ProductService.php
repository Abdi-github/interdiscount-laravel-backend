<?php

namespace App\Domain\Product\Services;

use App\Domain\Product\Models\Product;
use App\Domain\Product\Repositories\ProductRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class ProductService
{
    public function __construct(
        private ProductRepositoryInterface $productRepository,
    ) {}

    public function findById(int $id): ?Product
    {
        return $this->productRepository->findById($id);
    }

    public function findBySlug(string $slug): ?Product
    {
        return $this->productRepository->findBySlug($slug);
    }

    public function paginate(array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        return $this->productRepository->paginate($filters, $perPage);
    }

    public function create(array $data): Product
    {
        return $this->productRepository->create($data);
    }

    public function update(int $id, array $data): Product
    {
        return $this->productRepository->update($id, $data);
    }

    public function delete(int $id): bool
    {
        return $this->productRepository->delete($id);
    }

    public function updateStatus(int $id, string $status): Product
    {
        return $this->productRepository->updateStatus($id, $status);
    }

    public function getRelated(int $productId, int $limit = 8): Collection
    {
        return $this->productRepository->getRelated($productId, $limit);
    }

    public function getReviews(int $productId, int $perPage = 20): LengthAwarePaginator
    {
        $product = $this->productRepository->findById($productId);

        if (!$product) {
            return new \Illuminate\Pagination\LengthAwarePaginator([], 0, $perPage);
        }

        return $product->reviews()
            ->where('is_approved', true)
            ->with('user')
            ->orderByDesc('created_at')
            ->paginate($perPage);
    }
}
