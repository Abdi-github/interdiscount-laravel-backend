<?php

namespace App\Infrastructure\Persistence\Eloquent;

use App\Domain\Product\Models\Product;
use App\Domain\Product\Repositories\ProductRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class EloquentProductRepository implements ProductRepositoryInterface
{
    public function __construct(private Product $model) {}

    public function findById(int $id): ?Product
    {
        return $this->model->with(['brand', 'category'])->find($id);
    }

    public function findBySlug(string $slug): ?Product
    {
        return $this->model->with(['brand', 'category'])->where('slug', $slug)->first();
    }

    public function findByCode(string $code): ?Product
    {
        return $this->model->where('code', $code)->first();
    }

    public function paginate(array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        $query = $this->model->with(['brand', 'category']);

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('name_short', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        if (!empty($filters['brand_id'])) {
            $query->where('brand_id', $filters['brand_id']);
        }

        if (!empty($filters['category_ids'])) {
            $query->whereIn('category_id', $filters['category_ids']);
        } elseif (!empty($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['availability_state'])) {
            $query->where('availability_state', $filters['availability_state']);
        }

        if (isset($filters['min_price'])) {
            $query->where('price', '>=', $filters['min_price']);
        }

        if (isset($filters['max_price'])) {
            $query->where('price', '<=', $filters['max_price']);
        }

        $sortBy = $filters['sort_by'] ?? 'created_at';
        $sortOrder = $filters['sort_order'] ?? 'desc';
        $query->orderBy($sortBy, $sortOrder);

        return $query->paginate($perPage);
    }

    public function create(array $data): Product
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): Product
    {
        $product = $this->model->findOrFail($id);
        $product->update($data);
        return $product->fresh(['brand', 'category']);
    }

    public function delete(int $id): bool
    {
        return $this->model->findOrFail($id)->delete();
    }

    public function updateStatus(int $id, string $status): Product
    {
        $product = $this->model->findOrFail($id);
        $product->update(['status' => $status]);
        return $product->fresh();
    }

    public function getRelated(int $productId, int $limit = 8): Collection
    {
        $product = $this->model->findOrFail($productId);

        return $this->model->where('id', '!=', $productId)
            ->where('category_id', $product->category_id)
            ->active()
            ->limit($limit)
            ->get();
    }
}
