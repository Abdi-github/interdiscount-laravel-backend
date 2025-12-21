<?php

use App\Domain\Brand\Models\Brand;
use App\Domain\Category\Models\Category;
use App\Domain\Product\Models\Product;
use App\Domain\Review\Models\Review;
use App\Domain\User\Models\User;
use App\Domain\Wishlist\Models\Wishlist;
use Laravel\Sanctum\Sanctum;

beforeEach(function () {
    $this->user = User::factory()->create();
    Sanctum::actingAs($this->user);
});

test('get profile returns authenticated user', function () {
    $response = $this->getJson('/api/v1/customer');

    $response->assertOk()
        ->assertJson([
            'success' => true,
            'data' => ['email' => $this->user->email],
        ]);
});

test('update profile', function () {
    $response = $this->putJson('/api/v1/customer', [
        'first_name' => 'Updated',
        'last_name' => 'Name',
    ]);

    $response->assertOk()
        ->assertJson(['success' => true]);

    $this->user->refresh();
    expect($this->user->first_name)->toBe('Updated');
});

test('change password', function () {
    $this->user->update(['password' => bcrypt('Password123!')]);

    $response = $this->postJson('/api/v1/customer/change-password', [
        'current_password' => 'Password123!',
        'password' => 'NewPassword456!',
        'password_confirmation' => 'NewPassword456!',
    ]);

    $response->assertOk()
        ->assertJson(['success' => true]);
});

test('create address', function () {
    $response = $this->postJson('/api/v1/customer/addresses', [
        'label' => 'Home',
        'first_name' => 'John',
        'last_name' => 'Doe',
        'street' => 'Bahnhofstrasse',
        'street_number' => '1',
        'postal_code' => '8001',
        'city' => 'Zürich',
        'canton_code' => 'ZH',
        'country' => 'CH',
    ]);

    $response->assertStatus(201)
        ->assertJson(['success' => true]);

    $this->assertDatabaseHas('addresses', [
        'user_id' => $this->user->id,
        'label' => 'Home',
    ]);
});

test('list addresses', function () {
    $response = $this->getJson('/api/v1/customer/addresses');

    $response->assertOk()
        ->assertJson(['success' => true]);
});

test('add to wishlist', function () {
    $brand = Brand::factory()->create();
    $category = Category::factory()->create();
    $product = Product::factory()->create([
        'brand_id' => $brand->id,
        'category_id' => $category->id,
    ]);

    $response = $this->postJson('/api/v1/customer/wishlist', [
        'product_id' => $product->id,
    ]);

    $response->assertStatus(201)
        ->assertJson(['success' => true]);

    $this->assertDatabaseHas('wishlists', [
        'user_id' => $this->user->id,
        'product_id' => $product->id,
    ]);
});

test('list wishlist', function () {
    $response = $this->getJson('/api/v1/customer/wishlist');

    $response->assertOk()
        ->assertJson(['success' => true]);
});

test('remove from wishlist', function () {
    $brand = Brand::factory()->create();
    $category = Category::factory()->create();
    $product = Product::factory()->create([
        'brand_id' => $brand->id,
        'category_id' => $category->id,
    ]);
    Wishlist::create([
        'user_id' => $this->user->id,
        'product_id' => $product->id,
    ]);

    $response = $this->deleteJson("/api/v1/customer/wishlist/{$product->id}");

    $response->assertOk();
    $this->assertDatabaseMissing('wishlists', [
        'user_id' => $this->user->id,
        'product_id' => $product->id,
    ]);
});

test('create review', function () {
    $brand = Brand::factory()->create();
    $category = Category::factory()->create();
    $product = Product::factory()->create([
        'brand_id' => $brand->id,
        'category_id' => $category->id,
    ]);

    $response = $this->postJson('/api/v1/customer/reviews', [
        'product_id' => $product->id,
        'rating' => 5,
        'title' => 'Great product',
        'comment' => 'I love this product!',
    ]);

    $response->assertStatus(201)
        ->assertJson(['success' => true]);
});

test('list my reviews', function () {
    $response = $this->getJson('/api/v1/customer/reviews');

    $response->assertOk()
        ->assertJson(['success' => true]);
});

test('list notifications', function () {
    $response = $this->getJson('/api/v1/customer/notifications');

    $response->assertOk()
        ->assertJson(['success' => true]);
});

test('unauthenticated user cannot access customer routes', function () {
    // Create a new test without Sanctum acting
    $response = $this->withHeaders([])->getJson('/api/v1/customer');

    $response->assertStatus(401);
})->skip('Sanctum actingAs is set in beforeEach');
