<?php

use App\Domain\Admin\Models\Admin;
use App\Domain\Brand\Models\Brand;
use App\Domain\Category\Models\Category;
use App\Domain\Order\Models\Order;
use App\Domain\Product\Models\Product;
use App\Domain\RBAC\Models\Permission;
use App\Domain\RBAC\Models\Role;
use App\Domain\User\Models\User;
use Laravel\Sanctum\Sanctum;

beforeEach(function () {
    // Create the wildcard permission and a super_admin role
    $permission = Permission::create([
        'name' => '*:*',
        'display_name' => ['de' => 'Alle', 'en' => 'All', 'fr' => 'Tout', 'it' => 'Tutto'],
        'description' => ['de' => 'Vollzugriff', 'en' => 'Full access', 'fr' => 'Accès complet', 'it' => 'Accesso completo'],
        'resource' => '*',
        'action' => '*',
        'is_active' => true,
    ]);
    $role = Role::create([
        'name' => 'super_admin',
        'display_name' => ['de' => 'Super Admin', 'en' => 'Super Admin', 'fr' => 'Super Admin', 'it' => 'Super Admin'],
        'description' => ['de' => 'Vollzugriff', 'en' => 'Full access', 'fr' => 'Accès complet', 'it' => 'Accesso completo'],
        'is_system' => true,
        'is_active' => true,
    ]);
    $role->permissions()->attach($permission->id);

    $this->admin = Admin::factory()->superAdmin()->create();
    $this->admin->roles()->attach($role->id, [
        'assigned_at' => now(),
        'is_active' => true,
    ]);

    Sanctum::actingAs($this->admin);
});

test('admin dashboard returns stats', function () {
    $response = $this->getJson('/api/v1/admin/dashboard');

    $response->assertOk()
        ->assertJson(['success' => true])
        ->assertJsonStructure(['data']);
});

test('admin list users', function () {
    User::factory()->count(3)->create();

    $response = $this->getJson('/api/v1/admin/users');

    $response->assertOk()
        ->assertJson(['success' => true]);
});

test('admin show user', function () {
    $user = User::factory()->create();

    $response = $this->getJson("/api/v1/admin/users/{$user->id}");

    $response->assertOk()
        ->assertJson(['success' => true]);
});

test('admin toggle user status', function () {
    $user = User::factory()->create(['is_active' => true]);

    $response = $this->putJson("/api/v1/admin/users/{$user->id}/status");

    $response->assertOk()
        ->assertJson(['success' => true]);
});

test('admin list products', function () {
    $brand = Brand::factory()->create();
    $category = Category::factory()->create();
    Product::factory()->count(2)->create([
        'brand_id' => $brand->id,
        'category_id' => $category->id,
    ]);

    $response = $this->getJson('/api/v1/admin/products');

    $response->assertOk()
        ->assertJson(['success' => true]);
});

test('admin create product', function () {
    $brand = Brand::factory()->create();
    $category = Category::factory()->create();

    $response = $this->postJson('/api/v1/admin/products', [
        'name' => 'Test Product',
        'name_short' => 'Test Prod',
        'slug' => 'test-product-' . uniqid(),
        'code' => 'TST-001',
        'displayed_code' => '001',
        'brand_id' => $brand->id,
        'category_id' => $category->id,
        'price' => 99.90,
        'currency' => 'CHF',
        'availability_state' => 'INSTOCK',
        'status' => 'PUBLISHED',
    ]);

    $response->assertStatus(201)
        ->assertJson(['success' => true]);
});

test('admin list orders', function () {
    $user = User::factory()->create();
    Order::factory()->count(2)->create(['user_id' => $user->id]);

    $response = $this->getJson('/api/v1/admin/orders');

    $response->assertOk()
        ->assertJson(['success' => true]);
});

test('admin list categories', function () {
    Category::factory()->count(3)->create();

    $response = $this->getJson('/api/v1/admin/categories');

    $response->assertOk()
        ->assertJson(['success' => true]);
});

test('admin create category', function () {
    $response = $this->postJson('/api/v1/admin/categories', [
        'name' => ['de' => 'Elektronik', 'en' => 'Electronics', 'fr' => 'Électronique', 'it' => 'Elettronica'],
        'slug' => 'elektronik-' . uniqid(),
        'category_id' => 'cat-test-001',
        'level' => 1,
        'sort_order' => 0,
    ]);

    $response->assertStatus(201)
        ->assertJson(['success' => true]);
});

test('admin list brands', function () {
    Brand::factory()->count(3)->create();

    $response = $this->getJson('/api/v1/admin/brands');

    $response->assertOk()
        ->assertJson(['success' => true]);
});

test('admin create brand', function () {
    $response = $this->postJson('/api/v1/admin/brands', [
        'name' => 'TestBrand',
        'slug' => 'testbrand-' . uniqid(),
    ]);

    $response->assertStatus(201)
        ->assertJson(['success' => true]);
});

test('admin list stores', function () {
    $response = $this->getJson('/api/v1/admin/stores');

    $response->assertOk()
        ->assertJson(['success' => true]);
});

test('admin list reviews', function () {
    $response = $this->getJson('/api/v1/admin/reviews');

    $response->assertOk()
        ->assertJson(['success' => true]);
});

test('admin list coupons', function () {
    $response = $this->getJson('/api/v1/admin/coupons');

    $response->assertOk()
        ->assertJson(['success' => true]);
});

test('admin list roles', function () {
    $response = $this->getJson('/api/v1/admin/rbac/roles');

    $response->assertOk()
        ->assertJson(['success' => true]);
});

test('admin list permissions', function () {
    $response = $this->getJson('/api/v1/admin/rbac/permissions');

    $response->assertOk()
        ->assertJson(['success' => true]);
});

test('non-admin user cannot access admin routes', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $response = $this->getJson('/api/v1/admin/dashboard');

    $response->assertStatus(401);
});
