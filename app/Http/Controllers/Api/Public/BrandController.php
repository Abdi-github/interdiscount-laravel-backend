<?php

namespace App\Http\Controllers\Api\Public;

use App\Domain\Brand\Services\BrandService;
use App\Http\Controllers\Controller;
use App\Http\Resources\BrandResource;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;

class BrandController extends Controller
{
    use ApiResponse;

    public function __construct(
        private BrandService $brandService,
    ) {}

    public function index(): JsonResponse
    {
        $brands = $this->brandService->all();

        return $this->success(
            BrandResource::collection($brands),
            'Brands retrieved successfully',
        );
    }
}
