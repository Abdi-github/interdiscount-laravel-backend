<?php

namespace App\Http\Controllers\Api\Admin;

use App\Domain\Location\Services\LocationService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Location\StoreCantonRequest;
use App\Http\Requests\Location\StoreCityRequest;
use App\Http\Resources\CantonResource;
use App\Http\Resources\CityResource;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    use ApiResponse;

    public function __construct(
        private LocationService $locationService,
    ) {}

    // Cantons

    public function cantons(): JsonResponse
    {
        $cantons = $this->locationService->getAllCantons();

        return $this->success(CantonResource::collection($cantons), 'Cantons retrieved successfully');
    }

    public function storeCanton(StoreCantonRequest $request): JsonResponse
    {
        /* Log::debug('LocationController storeCanton - creating canton'); */
        $canton = $this->locationService->createCanton($request->validated());
        // TODO: Implement location geocoding for mapping

        return $this->created(new CantonResource($canton), 'Canton created successfully');
    }

    public function updateCanton(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'name' => ['sometimes', 'array'],
            'name.de' => ['sometimes', 'string', 'max:100'],
            'name.en' => ['nullable', 'string', 'max:100'],
            'name.fr' => ['nullable', 'string', 'max:100'],
            'name.it' => ['nullable', 'string', 'max:100'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $canton = $this->locationService->updateCanton($id, $request->validated());

        if (!$canton) {
            return $this->notFound('Canton not found');
        }

        return $this->success(new CantonResource($canton), 'Canton updated successfully');
    }

    public function destroyCanton(int $id): JsonResponse
    {
        $result = $this->locationService->deleteCanton($id);

        if (!$result) {
            return $this->notFound('Canton not found');
        }

        return $this->success(null, 'Canton deleted successfully');
    }

    // Cities

    public function cities(Request $request): JsonResponse
    {
        $cities = $this->locationService->getAllCities($request->only(['canton_id']));

        return $this->success(CityResource::collection($cities), 'Cities retrieved successfully');
    }

    public function storeCity(StoreCityRequest $request): JsonResponse
    {
        $city = $this->locationService->createCity($request->validated());

        return $this->created(new CityResource($city), 'City created successfully');
    }

    public function updateCity(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'name' => ['sometimes', 'array'],
            'name.de' => ['sometimes', 'string', 'max:100'],
            'name.en' => ['nullable', 'string', 'max:100'],
            'name.fr' => ['nullable', 'string', 'max:100'],
            'name.it' => ['nullable', 'string', 'max:100'],
            'canton_id' => ['sometimes', 'exists:cantons,id'],
            'postal_codes' => ['sometimes', 'array', 'min:1'],
            'postal_codes.*' => ['string', 'max:10'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $city = $this->locationService->updateCity($id, $request->validated());

        if (!$city) {
            return $this->notFound('City not found');
        }

        return $this->success(new CityResource($city), 'City updated successfully');
    }

    public function destroyCity(int $id): JsonResponse
    {
        $result = $this->locationService->deleteCity($id);

        if (!$result) {
            return $this->notFound('City not found');
        }

        return $this->success(null, 'City deleted successfully');
    }
}
