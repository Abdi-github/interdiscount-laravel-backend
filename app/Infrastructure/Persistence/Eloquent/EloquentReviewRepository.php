<?php

namespace App\Infrastructure\Persistence\Eloquent;

use App\Domain\Review\Models\Review;
use App\Domain\Review\Repositories\ReviewRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class EloquentReviewRepository implements ReviewRepositoryInterface
{
    public function __construct(private Review $model) {}

    public function findById(int $id): ?Review
    {
        return $this->model->with(['user', 'product'])->find($id);
    }

    public function paginateByProduct(int $productId, array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        $query = $this->model->with(['user'])
            ->where('product_id', $productId)
            ->approved();

        $sortBy = $filters['sort_by'] ?? 'created_at';
        $sortOrder = $filters['sort_order'] ?? 'desc';
        $allowedSorts = ['created_at', 'rating', 'helpful_count'];
        if (in_array($sortBy, $allowedSorts)) {
            $query->orderBy($sortBy, $sortOrder);
        }

        return $query->paginate($perPage);
    }

    public function paginateByUser(int $userId, array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        return $this->model->with(['product'])
            ->where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    public function paginate(array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        $query = $this->model->with(['user', 'product']);

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('comment', 'like', "%{$search}%")
                  ->orWhereHas('user', fn ($uq) => $uq->where('first_name', 'like', "%{$search}%")->orWhere('last_name', 'like', "%{$search}%"))
                  ->orWhereHas('product', fn ($pq) => $pq->where('name', 'like', "%{$search}%"));
            });
        }

        if (isset($filters['is_approved'])) {
            $query->where('is_approved', $filters['is_approved']);
        }

        if (!empty($filters['product_id'])) {
            $query->where('product_id', $filters['product_id']);
        }

        if (!empty($filters['rating'])) {
            $query->where('rating', $filters['rating']);
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    public function create(array $data): Review
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): Review
    {
        $review = $this->model->findOrFail($id);
        $review->update($data);
        return $review->fresh(['user', 'product']);
    }

    public function delete(int $id): bool
    {
        return $this->model->findOrFail($id)->delete();
    }
}
