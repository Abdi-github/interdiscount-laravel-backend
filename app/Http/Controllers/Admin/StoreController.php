<?php

namespace App\Http\Controllers\Admin;

use App\Domain\Location\Models\Canton;
use App\Domain\Store\Services\StoreService;
use App\Http\Controllers\Controller;
use App\Http\Resources\CantonResource;
use App\Http\Resources\StoreResource;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class StoreController extends Controller
{
    public function __construct(private StoreService $storeService) {}

    public function index(Request $request): Response
    {
        $stores = $this->storeService->paginate(
            $request->only(['search', 'canton_id', 'format', 'is_active']),
            (int) $request->input('per_page', 20)
        );

        return Inertia::render('Stores/Index', [
            'stores' => StoreResource::collection($stores),
            'filters' => $request->only(['search', 'canton_id', 'format', 'is_active', 'per_page']),
            'cantons' => fn () => CantonResource::collection(Canton::where('is_active', true)->orderBy('code')->get()),
        ]);
    }

    public function show(int $id): Response
    {
        $store = $this->storeService->findById($id);

        if (!$store) {
            abort(404);
        }

        $store->load(['city', 'canton']);

        return Inertia::render('Stores/Show', [
            'store' => new StoreResource($store),
        ]);
    }
}
