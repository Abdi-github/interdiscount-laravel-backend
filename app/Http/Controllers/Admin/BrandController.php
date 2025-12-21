<?php

namespace App\Http\Controllers\Admin;

use App\Domain\Brand\Services\BrandService;
use App\Http\Controllers\Controller;
use App\Http\Resources\BrandResource;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class BrandController extends Controller
{
    public function __construct(private BrandService $brandService) {}

    public function index(Request $request): Response
    {
        $brands = $this->brandService->paginate(
            $request->only(['search']),
            (int) $request->input('per_page', 50)
        );

        return Inertia::render('Brands/Index', [
            'brands' => BrandResource::collection($brands),
            'filters' => $request->only(['search', 'per_page']),
        ]);
    }
}
