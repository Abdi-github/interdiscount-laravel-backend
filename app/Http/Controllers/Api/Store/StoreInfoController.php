<?php

namespace App\Http\Controllers\Api\Store;

use App\Domain\Store\Services\StoreService;
use App\Http\Controllers\Controller;
use App\Http\Resources\StoreResource;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StoreInfoController extends Controller
{
    use ApiResponse;

    public function __construct(
        private StoreService $storeService,
    ) {}

    public function show(): JsonResponse
    {
        $admin = request()->user();
        $store = $this->storeService->findById($admin->store_id);

        if (!$store) {
            return $this->notFound('Store not found');
        }

        return $this->success(
            new StoreResource($store),
            'Store info retrieved successfully',
        );
    }

    public function update(Request $request): JsonResponse
    {
        $admin = request()->user();

        $validated = $request->validate([
            'phone' => ['sometimes', 'string', 'max:50'],
            'email' => ['sometimes', 'email', 'max:255'],
            'remarks' => ['sometimes', 'nullable', 'string', 'max:1000'],
            'opening_hours' => ['sometimes', 'array'],
            'opening_hours.*.day' => ['required_with:opening_hours', 'array'],
            'opening_hours.*.open' => ['required_with:opening_hours', 'string'],
            'opening_hours.*.close' => ['required_with:opening_hours', 'string'],
            'opening_hours.*.is_closed' => ['required_with:opening_hours', 'boolean'],
        ]);

        $store = $this->storeService->update($admin->store_id, $validated);

        return $this->success(
            new StoreResource($store),
            'Store info updated successfully',
        );
    }
}
