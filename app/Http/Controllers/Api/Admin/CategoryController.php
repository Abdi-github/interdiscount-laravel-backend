<?php

namespace App\Http\Controllers\Api\Admin;

use App\Domain\Category\Services\CategoryService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Category\StoreCategoryRequest;
use App\Http\Requests\Category\UpdateCategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    use ApiResponse;

    public function __construct(
        private CategoryService $categoryService,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $categories = $this->categoryService->paginate(
            $request->only(['search', 'level', 'parent_id', 'is_active']),
            $request->integer('limit', 50),
        );

        /* Log::debug('Categories retrieved - count: ' . count($categories->items())); */
        return $this->paginated($categories, 'CategoryResource', 'Categories retrieved successfully');
    }

    public function store(StoreCategoryRequest $request): JsonResponse
    {
        $category = $this->categoryService->create($request->validated());
        // TODO: Invalidate category tree cache on creation

        return $this->created(new CategoryResource($category), 'Category created successfully');
    }

    public function update(UpdateCategoryRequest $request, int $id): JsonResponse
    {
        $category = $this->categoryService->findById($id);

        if (!$category) {
            return $this->notFound('Category not found');
        }

        $category = $this->categoryService->update($id, $request->validated());
        /* Log::info('Category updated successfully', ['id' => $id]); */

        return $this->success(new CategoryResource($category), 'Category updated successfully');
    }

    public function destroy(int $id): JsonResponse
    {
        $category = $this->categoryService->findById($id);

        if (!$category) {
            return $this->notFound('Category not found');
        }

        if ($category->children()->exists()) {
            return $this->error('Cannot delete category with children', 422);
        }

        $this->categoryService->delete($id);

        return $this->success(null, 'Category deleted successfully');
    }

    public function reorder(Request $request): JsonResponse
    {
        $request->validate([
            'items' => ['required', 'array'],
            'items.*.id' => ['required', 'exists:categories,id'],
            'items.*.sort_order' => ['required', 'integer', 'min:0'],
        ]);

        foreach ($request->input('items') as $item) {
            $this->categoryService->update($item['id'], ['sort_order' => $item['sort_order']]);
        }

        /* Log::info('Categories reordered successfully', ['count' => count($request->input('items'))]); */
        return $this->success(null, 'Categories reordered successfully');
    }
}
