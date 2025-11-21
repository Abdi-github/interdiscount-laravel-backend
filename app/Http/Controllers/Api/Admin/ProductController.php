<?php

namespace App\Http\Controllers\Api\Admin;

use App\Domain\Product\Services\ProductService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Product\StoreProductRequest;
use App\Http\Requests\Product\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Infrastructure\External\Cloudinary\CloudinaryService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    use ApiResponse;

    public function __construct(
        private ProductService $productService,
        private CloudinaryService $cloudinaryService,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $products = $this->productService->paginate(
            $request->only(['search', 'category_id', 'brand_id', 'status', 'is_active', 'availability_state', 'min_price', 'max_price', 'sort_by', 'sort_order']),
            $request->integer('limit', 20),
        );

        /* Log::debug('Products list - count: ' . count($products->items())); */
        return $this->paginated($products, 'ProductResource', 'Products retrieved successfully');
    }

    public function store(StoreProductRequest $request): JsonResponse
    {
        $product = $this->productService->create($request->validated());
        // TODO: Add product creation event for analytics tracking

        return $this->created(new ProductResource($product), 'Product created successfully');
    }

    public function show(int $id): JsonResponse
    {
        $product = $this->productService->findById($id);

        if (!$product) {
            return $this->notFound('Product not found');
        }

        $product->load(['brand', 'category']);
        /* Log::debug('Product loaded with relations'); */

        return $this->success(new ProductResource($product), 'Product retrieved successfully');
    }

    public function update(UpdateProductRequest $request, int $id): JsonResponse
    {
        $product = $this->productService->findById($id);

        if (!$product) {
            return $this->notFound('Product not found');
        }

        $product = $this->productService->update($id, $request->validated());

        return $this->success(new ProductResource($product), 'Product updated successfully');
    }

    public function updateStatus(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'status' => ['required', 'string'],
        ]);

        $product = $this->productService->findById($id);

        if (!$product) {
            return $this->notFound('Product not found');
        }

        $product = $this->productService->updateStatus($id, $request->input('status'));
        /* Log::info('Product status changed', ['id' => $id, 'new_status' => $product->status]); */

        return $this->success(new ProductResource($product), 'Product status updated successfully');
    }

    public function uploadImages(Request $request, int $id): JsonResponse
    {
        $product = $this->productService->findById($id);

        if (!$product) {
            return $this->notFound('Product not found');
        }

        $request->validate([
            'images' => ['required', 'array', 'min:1'],
            'images.*' => ['image', 'max:5120', 'mimes:jpeg,jpg,png,webp,avif'],
        ]);

        // For now, store image URLs from request data
        // Full Cloudinary integration handled separately
        $existingImages = $product->images ?? [];
        $newImages = [];

        foreach ($request->file('images', []) as $image) {
            try {
                $uploaded = $this->cloudinaryService->upload($image, 'interdiscount/products');
                $newImages[] = [
                    'alt' => $product->name,
                    'public_id' => $uploaded['public_id'],
                    'src' => [
                        'xs' => $this->cloudinaryService->generateUrl($uploaded['public_id'], 150, 150),
                        'sm' => $this->cloudinaryService->generateUrl($uploaded['public_id'], 300, 300),
                        'md' => $uploaded['url'],
                    ],
                ];
            } catch (\InvalidArgumentException $e) {
                return $this->error($e->getMessage(), 422);
            }
        }

        $product = $this->productService->update($id, [
            'images' => array_merge($existingImages, $newImages),
        ]);

        return $this->success(new ProductResource($product), 'Images uploaded successfully');
    }

    public function deleteImage(int $id, int $imageId): JsonResponse
    {
        $product = $this->productService->findById($id);

        if (!$product) {
            return $this->notFound('Product not found');
        }

        $images = $product->images ?? [];

        if (!isset($images[$imageId])) {
            return $this->notFound('Image not found');
        }

        // Delete from Cloudinary if public_id exists
        if (isset($images[$imageId]['public_id'])) {
            // TODO: Add retry logic for failed Cloudinary deletions
            $this->cloudinaryService->delete($images[$imageId]['public_id']);
            /* Log::debug('Image deleted from Cloudinary', ['public_id' => $images[$imageId]['public_id']]); */
        }

        unset($images[$imageId]);
        $images = array_values($images);

        $product = $this->productService->update($id, ['images' => $images]);

        return $this->success(new ProductResource($product), 'Image deleted successfully');
    }
}
