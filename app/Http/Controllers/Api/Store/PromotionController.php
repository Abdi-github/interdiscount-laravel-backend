<?php

namespace App\Http\Controllers\Api\Store;

use App\Domain\Promotion\Services\PromotionService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Promotion\StorePromotionRequest;
use App\Http\Resources\StorePromotionResource;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PromotionController extends Controller
{
    use ApiResponse;

    public function __construct(
        private PromotionService $promotionService,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $admin = request()->user();
        $perPage = (int) $request->input('limit', 20);

        $promotions = $this->promotionService->paginateByStore(
            $admin->store_id,
            $request->only(['search']),
            $perPage,
        );

        return $this->paginated($promotions, 'StorePromotionResource', 'Promotions retrieved successfully');
    }

    public function store(StorePromotionRequest $request): JsonResponse
    {
        $admin = request()->user();

        $data = $request->validated();
        $data['store_id'] = $admin->store_id;
        $data['created_by'] = $admin->id;

        $promotion = $this->promotionService->create($data);

        return $this->created(
            new StorePromotionResource($promotion),
            'Promotion created successfully',
        );
    }

    public function show(int $id): JsonResponse
    {
        $admin = request()->user();
        $promotion = $this->promotionService->findById($id);

        if (!$promotion || $promotion->store_id !== $admin->store_id) {
            return $this->notFound('Promotion not found');
        }

        return $this->success(
            new StorePromotionResource($promotion),
            'Promotion retrieved successfully',
        );
    }

    public function update(StorePromotionRequest $request, int $id): JsonResponse
    {
        $admin = request()->user();
        $promotion = $this->promotionService->findById($id);

        if (!$promotion || $promotion->store_id !== $admin->store_id) {
            return $this->notFound('Promotion not found');
        }

        $updated = $this->promotionService->update($id, $request->validated());

        return $this->success(
            new StorePromotionResource($updated),
            'Promotion updated successfully',
        );
    }

    public function destroy(int $id): JsonResponse
    {
        $admin = request()->user();
        $promotion = $this->promotionService->findById($id);

        if (!$promotion || $promotion->store_id !== $admin->store_id) {
            return $this->notFound('Promotion not found');
        }

        $this->promotionService->delete($id);

        return $this->success(null, 'Promotion deleted successfully');
    }
}
