<?php

namespace App\Http\Controllers\Api\Customer;

use App\Domain\Wishlist\Services\WishlistService;
use App\Http\Controllers\Controller;
use App\Http\Resources\WishlistResource;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    use ApiResponse;

    public function __construct(
        private WishlistService $wishlistService,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $wishlists = $this->wishlistService->getByUser($request->user()->id);

        return $this->success(
            WishlistResource::collection($wishlists),
            'Wishlist retrieved successfully',
        );
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'product_id' => ['required', 'integer', 'exists:products,id'],
        ]);

        $wishlist = $this->wishlistService->add(
            $request->user()->id,
            $request->input('product_id'),
        );

        return $this->created(
            new WishlistResource($wishlist->load('product.brand', 'product.category')),
            'Product added to wishlist',
        );
    }

    public function destroy(Request $request, int $productId): JsonResponse
    {
        $removed = $this->wishlistService->remove(
            $request->user()->id,
            $productId,
        );

        if (!$removed) {
            return $this->notFound('Product not found in wishlist');
        }

        return $this->success(null, 'Product removed from wishlist');
    }
}
