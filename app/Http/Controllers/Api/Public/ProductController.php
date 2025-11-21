<?php

namespace App\Http\Controllers\Api\Public;

use App\Domain\Product\Services\ProductService;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Http\Resources\ReviewResource;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    use ApiResponse;

    public function __construct(
        private ProductService $productService,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $filters = $request->only([
            'search', 'brand_id', 'category_id', 'status',
            'availability_state', 'min_price', 'max_price',
            'sort_by', 'sort_order',
        ]);

        $perPage = (int) $request->input('limit', 20);
        $products = $this->productService->paginate($filters, $perPage);

        return $this->paginated($products, 'ProductResource', 'Products retrieved successfully');
    }

    public function show(int $id): JsonResponse
    {
        $product = $this->productService->findById($id);

        if (!$product) {
            return $this->notFound('Product not found');
        }

        return $this->success(new ProductResource($product), 'Product retrieved successfully');
    }

    public function showBySlug(string $slug): JsonResponse
    {
        $product = $this->productService->findBySlug($slug);

        if (!$product) {
            return $this->notFound('Product not found');
        }

        return $this->success(new ProductResource($product), 'Product retrieved successfully');
    }

    public function reviews(int $id, Request $request): JsonResponse
    {
        $product = $this->productService->findById($id);

        if (!$product) {
            return $this->notFound('Product not found');
        }

        $perPage = (int) $request->input('limit', 20);
        $reviews = $this->productService->getReviews($id, $perPage);

        return $this->paginated($reviews, 'ReviewResource', 'Reviews retrieved successfully');
    }

    public function related(int $id): JsonResponse
    {
        $product = $this->productService->findById($id);

        if (!$product) {
            return $this->notFound('Product not found');
        }

        $related = $this->productService->getRelated($id);

        return $this->success(
            ProductResource::collection($related),
            'Related products retrieved successfully',
        );
    }
}
