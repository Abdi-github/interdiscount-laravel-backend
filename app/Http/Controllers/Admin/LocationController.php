<?php

namespace App\Http\Controllers\Admin;

use App\Domain\Location\Services\LocationService;
use App\Http\Controllers\Controller;
use App\Http\Resources\CantonResource;
use App\Http\Resources\CityResource;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class LocationController extends Controller
{
    public function __construct(private LocationService $locationService) {}

    public function index(Request $request): Response
    {
        $cantons = $this->locationService->getAllCantons();
        $cities = $this->locationService->getAllCities();

        return Inertia::render('Locations/Index', [
            'cantons' => CantonResource::collection($cantons),
            'cities' => CityResource::collection($cities),
            'filters' => $request->only(['tab', 'search']),
        ]);
    }
}
