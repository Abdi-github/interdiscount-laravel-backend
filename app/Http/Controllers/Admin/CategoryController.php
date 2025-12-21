<?php

namespace App\Http\Controllers\Admin;

use App\Domain\Category\Services\CategoryService;
use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CategoryController extends Controller
{
    public function __construct(private CategoryService $categoryService) {}

    public function index(Request $request): Response
    {
        $filters = $request->only(['search', 'level', 'parent_id']);
        $categories = $this->categoryService->paginate(
            $filters,
            (int) $request->input('per_page', 50)
        );

        return Inertia::render('Categories/Index', [
            'categories' => CategoryResource::collection($categories),
            'filters' => $request->only(['search', 'level', 'parent_id', 'per_page']),
        ]);
    }
}
