<?php

namespace App\Http\Controllers\Api\Public;

use App\Domain\Category\Services\CategoryService;
use App\Domain\Product\Services\ProductService;
use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    use ApiResponse;

    public function __construct(
        private CategoryService $categoryService,
        private ProductService $productService,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $filters = $request->only(['level', 'parent_id']);
        $categories = $this->categoryService->all($filters);

        return $this->success(
            CategoryResource::collection($categories),
            'Categories retrieved successfully',
        );
    }

    public function show(int $id): JsonResponse
    {
        $category = $this->categoryService->findById($id);

        if (!$category) {
            return $this->notFound('Category not found');
        }

        return $this->success(new CategoryResource($category), 'Category retrieved successfully');
    }

    public function showBySlug(string $slug): JsonResponse
    {
        $category = $this->categoryService->findBySlug($slug);

        if (!$category) {
            return $this->notFound('Category not found');
        }

        return $this->success(new CategoryResource($category), 'Category retrieved successfully');
    }

    public function children(int $id): JsonResponse
    {
        $category = $this->categoryService->findById($id);

        if (!$category) {
            return $this->notFound('Category not found');
        }

        $children = $this->categoryService->getChildren($id);

        return $this->success(
            CategoryResource::collection($children),
            'Category children retrieved successfully',
        );
    }

    public function breadcrumb(int $id): JsonResponse
    {
        $category = $this->categoryService->findById($id);

        if (!$category) {
            return $this->notFound('Category not found');
        }

        $breadcrumb = $this->categoryService->getBreadcrumb($id);

        return $this->success(
            CategoryResource::collection($breadcrumb),
            'Category breadcrumb retrieved successfully',
        );
    }

    public function productCounts(): JsonResponse
    {
        $categories = $this->categoryService->getWithProductCounts();

        return $this->success(
            CategoryResource::collection($categories),
            'Categories with product counts retrieved successfully',
        );
    }

    public function products(int $id, Request $request): JsonResponse
    {
        $category = $this->categoryService->findById($id);

        if (!$category) {
            return $this->notFound('Category not found');
        }

        // Collect this category + all descendant category IDs
        $categoryIds = $this->categoryService->getDescendantIds($id);
        $categoryIds[] = $id;

        $filters = $request->only([
            'search', 'brand_id', 'status',
            'availability_state', 'min_price', 'max_price',
            'sort_by', 'sort_order',
        ]);
        $filters['category_ids'] = $categoryIds;

        $perPage = (int) $request->input('limit', 20);
        $products = $this->productService->paginate($filters, $perPage);

        return $this->paginated($products, 'ProductResource', 'Category products retrieved successfully');
    }
}
