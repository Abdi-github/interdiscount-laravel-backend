<?php

namespace App\Http\Controllers\Api\Customer;

use App\Domain\Review\Services\ReviewService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Review\StoreReviewRequest;
use App\Http\Requests\Review\UpdateReviewRequest;
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
        $perPage = (int) $request->input('limit', 20);
        $reviews = $this->reviewService->paginateByUser(
            $request->user()->id,
            $request->only(['sort_by', 'sort_order']),
            $perPage,
        );

        return $this->paginated($reviews, 'ReviewResource', 'Reviews retrieved successfully');
    }

    public function store(StoreReviewRequest $request): JsonResponse
    {
        $review = $this->reviewService->create(
            $request->user()->id,
            $request->validated(),
        );

        return $this->created(
            new ReviewResource($review->load('product')),
            'Review created successfully',
        );
    }

    public function update(UpdateReviewRequest $request, int $id): JsonResponse
    {
        $review = $this->reviewService->findById($id);

        if (!$review || $review->user_id !== $request->user()->id) {
            return $this->notFound('Review not found');
        }

        $updated = $this->reviewService->update($id, $request->validated());

        return $this->success(
            new ReviewResource($updated),
            'Review updated successfully',
        );
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        $review = $this->reviewService->findById($id);

        if (!$review || $review->user_id !== $request->user()->id) {
            return $this->notFound('Review not found');
        }

        $this->reviewService->delete($id);

        return $this->success(null, 'Review deleted successfully');
    }
}
