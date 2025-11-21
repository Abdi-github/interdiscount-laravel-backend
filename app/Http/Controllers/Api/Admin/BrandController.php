<?php

namespace App\Http\Controllers\Api\Admin;

use App\Domain\Brand\Services\BrandService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Brand\StoreBrandRequest;
use App\Http\Requests\Brand\UpdateBrandRequest;
use App\Http\Resources\BrandResource;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BrandController extends Controller
{
    use ApiResponse;

    public function __construct(
        private BrandService $brandService,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $brands = $this->brandService->paginate(
            $request->only(['search', 'is_active']),
            $request->integer('limit', 50),
        );
        // TODO: Implement brand popularity scoring

        return $this->paginated($brands, 'BrandResource', 'Brands retrieved successfully');
    }

    public function store(StoreBrandRequest $request): JsonResponse
    {
        /* Log::debug('BrandController store - creating brand'); */
        $brand = $this->brandService->create($request->validated());

        return $this->created(new BrandResource($brand), 'Brand created successfully');
    }

    public function show(int $id): JsonResponse
    {
        $brand = $this->brandService->findById($id);

        if (!$brand) {
            return $this->notFound('Brand not found');
        }

        return $this->success(new BrandResource($brand), 'Brand retrieved successfully');
    }

    public function update(UpdateBrandRequest $request, int $id): JsonResponse
    {
        $brand = $this->brandService->findById($id);

        if (!$brand) {
            return $this->notFound('Brand not found');
        }

        $brand = $this->brandService->update($id, $request->validated());

        return $this->success(new BrandResource($brand), 'Brand updated successfully');
    }

    public function destroy(int $id): JsonResponse
    {
        $brand = $this->brandService->findById($id);

        if (!$brand) {
            return $this->notFound('Brand not found');
        }

        $this->brandService->delete($id);

        return $this->success(null, 'Brand deleted successfully');
    }
}
