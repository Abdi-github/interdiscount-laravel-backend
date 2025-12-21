<?php

use App\Domain\Admin\Models\Admin;
use App\Domain\Brand\Models\Brand;
use App\Domain\Category\Models\Category;
use App\Domain\Inventory\Models\StoreInventory;
use App\Domain\Product\Models\Product;
use App\Domain\RBAC\Models\Permission;
use App\Domain\RBAC\Models\Role;
use App\Domain\Store\Models\Store;
use Laravel\Sanctum\Sanctum;

beforeEach(function () {
    $this->store = Store::factory()->create();

    // Create permissions and role for store manager
    $permission = Permission::create([
        'name' => '*:*',
        'display_name' => ['de' => 'Alle', 'en' => 'All', 'fr' => 'Tout', 'it' => 'Tutto'],
        'description' => ['de' => 'Vollzugriff', 'en' => 'Full access', 'fr' => 'Accès complet', 'it' => 'Accesso completo'],
        'resource' => '*',
        'action' => '*',
        'is_active' => true,
    ]);
    $role = Role::create([
        'name' => 'store_manager',
        'display_name' => ['de' => 'Filialleiter', 'en' => 'Store Manager', 'fr' => 'Gérant', 'it' => 'Responsabile'],
        'description' => ['de' => 'Filialverwaltung', 'en' => 'Store management', 'fr' => 'Gestion du magasin', 'it' => 'Gestione del negozio'],
        'is_system' => true,
        'is_active' => true,
    ]);
    $role->permissions()->attach($permission->id);

    $this->manager = Admin::factory()->storeManager()->create([
        'store_id' => $this->store->id,
    ]);
    $this->manager->roles()->attach($role->id, [
        'assigned_at' => now(),
        'is_active' => true,
    ]);

    Sanctum::actingAs($this->manager);
});

test('list store inventory', function () {
    $brand = Brand::factory()->create();
    $category = Category::factory()->create();
    $product = Product::factory()->create([
        'brand_id' => $brand->id,
        'category_id' => $category->id,
    ]);

    StoreInventory::create([
        'store_id' => $this->store->id,
        'product_id' => $product->id,
        'quantity' => 50,
        'reserved' => 0,
        'min_stock' => 5,
        'max_stock' => 100,
        'is_active' => true,
    ]);

    $response = $this->getJson('/api/v1/store/inventory');

    $response->assertOk()
        ->assertJson(['success' => true]);
});

test('get low stock items', function () {
    $response = $this->getJson('/api/v1/store/inventory/low-stock');

    $response->assertOk()
        ->assertJson(['success' => true]);
});

test('list store promotions', function () {
    $response = $this->getJson('/api/v1/store/promotions');

    $response->assertOk()
        ->assertJson(['success' => true]);
});

test('get store info', function () {
    $response = $this->getJson('/api/v1/store/info');

    $response->assertOk()
        ->assertJson(['success' => true]);
});

test('get store dashboard', function () {
    $response = $this->getJson('/api/v1/store/dashboard');

    $response->assertOk()
        ->assertJson(['success' => true]);
});

test('list transfers', function () {
    $response = $this->getJson('/api/v1/store/transfers');

    $response->assertOk()
        ->assertJson(['success' => true]);
});
