<?php

namespace App\Domain\Review\Services;

use App\Domain\Review\Models\Review;
use App\Domain\Review\Repositories\ReviewRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ReviewService
{
    public function __construct(
        private ReviewRepositoryInterface $reviewRepository,
    ) {}

    public function findById(int $id): ?Review
    {
        return $this->reviewRepository->findById($id);
    }

    public function paginate(array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        return $this->reviewRepository->paginate($filters, $perPage);
    }

    public function paginateByUser(int $userId, array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        return $this->reviewRepository->paginateByUser($userId, $filters, $perPage);
    }

    public function approve(int $id): Review
    {
        return $this->reviewRepository->update($id, ['is_approved' => true]);
    }

    public function create(int $userId, array $data): Review
    {
        $data['user_id'] = $userId;
        $data['is_approved'] = false;
        $data['is_verified_purchase'] = false;

        return $this->reviewRepository->create($data);
    }

    public function update(int $id, array $data): Review
    {
        return $this->reviewRepository->update($id, $data);
    }

    public function delete(int $id): bool
    {
        return $this->reviewRepository->delete($id);
    }
}
