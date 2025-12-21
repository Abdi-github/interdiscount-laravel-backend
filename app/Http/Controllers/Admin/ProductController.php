<?php

namespace App\Http\Controllers\Admin;

use App\Domain\Brand\Models\Brand;
use App\Domain\Category\Models\Category;
use App\Domain\Product\Enums\AvailabilityState;
use App\Domain\Product\Enums\ProductStatus;
use App\Domain\Product\Services\ProductService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Product\StoreProductRequest;
use App\Http\Requests\Product\UpdateProductRequest;
use App\Http\Resources\BrandResource;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\ProductResource;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class ProductController extends Controller
{
    public function __construct(private ProductService $productService) {}

    public function index(Request $request): Response
    {
        $products = $this->productService->paginate(
            $request->only(['search', 'status', 'brand_id', 'category_id', 'sort_by', 'sort_order']),
            (int) $request->input('per_page', 20)
        );

        return Inertia::render('Products/Index', [
            'products' => ProductResource::collection($products),
            'brands' => fn () => BrandResource::collection(Brand::where('is_active', true)->orderBy('name')->get()),
            'categories' => fn () => CategoryResource::collection(Category::where('is_active', true)->orderBy('level')->orderBy('sort_order')->get()),
            'filters' => $request->only(['search', 'status', 'brand_id', 'category_id', 'sort_by', 'sort_order', 'per_page']),
            'statuses' => collect(ProductStatus::cases())->map(fn ($s) => ['value' => $s->value, 'label' => $s->name]),
        ]);
    }

    public function show(int $id): Response
    {
        $product = $this->productService->findById($id);

        if (!$product) {
            abort(404);
        }

        $product->load(['brand', 'category', 'reviews' => function ($q) {
            $q->latest()->limit(10);
        }, 'reviews.user']);

        return Inertia::render('Products/Show', [
            'product' => new ProductResource($product),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Products/Create', [
            'brands' => BrandResource::collection(Brand::where('is_active', true)->orderBy('name')->get()),
            'categories' => CategoryResource::collection(Category::where('is_active', true)->orderBy('level')->orderBy('sort_order')->get()),
            'statuses' => collect(ProductStatus::cases())->map(fn ($s) => ['value' => $s->value, 'label' => $s->name]),
            'availabilityStates' => collect(AvailabilityState::cases())->map(fn ($s) => ['value' => $s->value, 'label' => $s->name]),
        ]);
    }

    public function store(StoreProductRequest $request): RedirectResponse
    {
        $data = $request->validated();

        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        $this->productService->create($data);

        return redirect()->route('admin.products.index')
            ->with('success', 'Product created successfully');
    }

    public function edit(int $id): Response
    {
        $product = $this->productService->findById($id);

        if (!$product) {
            abort(404);
        }

        return Inertia::render('Products/Edit', [
            'product' => new ProductResource($product),
            'brands' => BrandResource::collection(Brand::where('is_active', true)->orderBy('name')->get()),
            'categories' => CategoryResource::collection(Category::where('is_active', true)->orderBy('level')->orderBy('sort_order')->get()),
            'statuses' => collect(ProductStatus::cases())->map(fn ($s) => ['value' => $s->value, 'label' => $s->name]),
            'availabilityStates' => collect(AvailabilityState::cases())->map(fn ($s) => ['value' => $s->value, 'label' => $s->name]),
        ]);
    }

    public function update(UpdateProductRequest $request, int $id): RedirectResponse
    {
        $this->productService->update($id, $request->validated());

        return redirect()->route('admin.products.index')
            ->with('success', 'Product updated successfully');
    }

    public function destroy(int $id): RedirectResponse
    {
        $this->productService->delete($id);

        return redirect()->route('admin.products.index')
            ->with('success', 'Product deleted successfully');
    }

    public function updateStatus(Request $request, int $id): RedirectResponse
    {
        $request->validate([
            'status' => ['required', 'string', \Illuminate\Validation\Rule::enum(ProductStatus::class)],
        ]);

        $this->productService->updateStatus($id, $request->input('status'));

        return redirect()->back()
            ->with('success', 'Product status updated successfully');
    }
}
