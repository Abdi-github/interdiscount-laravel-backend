<?php

use App\Domain\Brand\Models\Brand;
use App\Domain\Category\Models\Category;
use App\Domain\Product\Models\Product;
use App\Domain\Store\Models\Store;

test('list products returns paginated results', function () {
    $brand = Brand::factory()->create();
    $category = Category::factory()->create();
    Product::factory()->count(3)->create([
        'brand_id' => $brand->id,
        'category_id' => $category->id,
    ]);

    $response = $this->getJson('/api/v1/public/products');

    $response->assertOk()
        ->assertJson(['success' => true])
        ->assertJsonStructure([
            'data',
            'pagination' => ['page', 'limit', 'total', 'totalPages'],
        ]);

    expect($response->json('pagination.total'))->toBe(3);
});

test('get product by slug', function () {
    $brand = Brand::factory()->create();
    $category = Category::factory()->create();
    $product = Product::factory()->create([
        'brand_id' => $brand->id,
        'category_id' => $category->id,
        'slug' => 'test-product-123',
    ]);

    $response = $this->getJson('/api/v1/public/products/slug/test-product-123');

    $response->assertOk()
        ->assertJson(['success' => true]);
});

test('get product by id', function () {
    $brand = Brand::factory()->create();
    $category = Category::factory()->create();
    $product = Product::factory()->create([
        'brand_id' => $brand->id,
        'category_id' => $category->id,
    ]);

    $response = $this->getJson("/api/v1/public/products/{$product->id}");

    $response->assertOk()
        ->assertJson(['success' => true]);
});

test('get product returns 404 for nonexistent id', function () {
    $response = $this->getJson('/api/v1/public/products/99999');

    $response->assertStatus(404);
});

test('list categories', function () {
    Category::factory()->count(3)->create();

    $response = $this->getJson('/api/v1/public/categories');

    $response->assertOk()
        ->assertJson(['success' => true]);
});

test('get category by slug', function () {
    $category = Category::factory()->create(['slug' => 'electronics-001']);

    $response = $this->getJson('/api/v1/public/categories/slug/electronics-001');

    $response->assertOk()
        ->assertJson(['success' => true]);
});

test('get category breadcrumb', function () {
    $parent = Category::factory()->create(['level' => 1]);
    $child = Category::factory()->child($parent->id, 2)->create();

    $response = $this->getJson("/api/v1/public/categories/{$child->id}/breadcrumb");

    $response->assertOk()
        ->assertJson(['success' => true]);
});

test('list brands', function () {
    Brand::factory()->count(5)->create();

    $response = $this->getJson('/api/v1/public/brands');

    $response->assertOk()
        ->assertJson(['success' => true]);
});

test('list stores', function () {
    Store::factory()->count(3)->create();

    $response = $this->getJson('/api/v1/public/stores');

    $response->assertOk()
        ->assertJson(['success' => true]);
});

test('get store by slug', function () {
    $store = Store::factory()->create(['slug' => 'interdiscount-zurich-01']);

    $response = $this->getJson('/api/v1/public/stores/slug/interdiscount-zurich-01');

    $response->assertOk()
        ->assertJson(['success' => true]);
});

test('get store by id', function () {
    $store = Store::factory()->create();

    $response = $this->getJson("/api/v1/public/stores/{$store->id}");

    $response->assertOk()
        ->assertJson(['success' => true]);
});
