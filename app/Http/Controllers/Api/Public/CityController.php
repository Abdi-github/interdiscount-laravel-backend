<?php

namespace App\Http\Controllers\Api\Public;

use App\Domain\Location\Services\LocationService;
use App\Http\Controllers\Controller;
use App\Http\Resources\CityResource;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;

class CityController extends Controller
{
    use ApiResponse;

    public function __construct(
        private LocationService $locationService,
    ) {}

    public function index(): JsonResponse
    {
        $cities = $this->locationService->getAllCities();

        return $this->success(
            CityResource::collection($cities),
            'Cities retrieved successfully',
        );
    }
}
