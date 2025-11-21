<?php

namespace App\Http\Controllers\Api\Public;

use App\Domain\Store\Services\StoreService;
use App\Http\Controllers\Controller;
use App\Http\Resources\StoreResource;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StoreController extends Controller
{
    use ApiResponse;

    public function __construct(
        private StoreService $storeService,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $filters = $request->only(['search', 'canton_id', 'city_id', 'format', 'is_active']);
        $perPage = (int) $request->input('limit', 20);

        $stores = $this->storeService->paginate($filters, $perPage);

        return $this->paginated($stores, 'StoreResource', 'Stores retrieved successfully');
    }

    public function show(int $id): JsonResponse
    {
        $store = $this->storeService->findById($id);

        if (!$store) {
            return $this->notFound('Store not found');
        }

        return $this->success(new StoreResource($store), 'Store retrieved successfully');
    }

    public function showBySlug(string $slug): JsonResponse
    {
        $store = $this->storeService->findBySlug($slug);

        if (!$store) {
            return $this->notFound('Store not found');
        }

        return $this->success(new StoreResource($store), 'Store retrieved successfully');
    }
}
