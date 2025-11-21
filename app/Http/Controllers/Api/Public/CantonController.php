<?php

namespace App\Http\Controllers\Api\Public;

use App\Domain\Location\Services\LocationService;
use App\Http\Controllers\Controller;
use App\Http\Resources\CantonResource;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;

class CantonController extends Controller
{
    use ApiResponse;

    public function __construct(
        private LocationService $locationService,
    ) {}

    public function index(): JsonResponse
    {
        $cantons = $this->locationService->getAllCantons();

        return $this->success(
            CantonResource::collection($cantons),
            'Cantons retrieved successfully',
        );
    }
}
