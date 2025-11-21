<?php

namespace App\Http\Controllers\Api\Admin;

use App\Domain\Review\Services\ReviewService;
use App\Http\Controllers\Controller;
use App\Http\Resources\ReviewResource;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    use ApiResponse;

    public function __construct(
        private ReviewService $reviewService,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $reviews = $this->reviewService->paginate(
            $request->only(['search', 'product_id', 'is_approved', 'rating']),
            $request->integer('limit', 20),
        );
        // TODO: Implement spam detection for reviews queue

        return $this->paginated($reviews, 'ReviewResource', 'Reviews retrieved successfully');
    }

    public function show(int $id): JsonResponse
    {
        $review = $this->reviewService->findById($id);

        if (!$review) {
            return $this->notFound('Review not found');
        }

        $review->load(['product', 'user']);

        return $this->success(new ReviewResource($review), 'Review retrieved successfully');
    }

    public function approve(int $id): JsonResponse
    {
        $review = $this->reviewService->findById($id);

        if (!$review) {
            return $this->notFound('Review not found');
        }

        $review = $this->reviewService->approve($id);

        return $this->success(new ReviewResource($review), 'Review approved successfully');
    }

    public function destroy(int $id): JsonResponse
    {
        $review = $this->reviewService->findById($id);

        if (!$review) {
            return $this->notFound('Review not found');
        }

        $this->reviewService->delete($id);

        return $this->success(null, 'Review deleted successfully');
    }
}
