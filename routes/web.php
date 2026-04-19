<?php

use App\Http\Controllers\Admin\AnalyticsController;
use App\Http\Controllers\Admin\BrandController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CouponController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\InventoryController;
use App\Http\Controllers\Admin\LocationController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\PromotionController;
use App\Http\Controllers\Admin\ReviewController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\StoreController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\TransferController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Redirect root to login
Route::redirect('/', '/login');

// Admin panel routes (Jetstream handles /login, /logout, etc.)
Route::middleware([
    'auth',
    config('jetstream.auth_session'),
])->prefix('admin')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Products CRUD
    Route::get('/products', [ProductController::class, 'index'])->name('admin.products.index');
    Route::get('/products/create', [ProductController::class, 'create'])->name('admin.products.create');
    Route::post('/products', [ProductController::class, 'store'])->name('admin.products.store');
    Route::get('/products/{id}', [ProductController::class, 'show'])->name('admin.products.show');
    Route::get('/products/{id}/edit', [ProductController::class, 'edit'])->name('admin.products.edit');
    Route::put('/products/{id}', [ProductController::class, 'update'])->name('admin.products.update');
    Route::delete('/products/{id}', [ProductController::class, 'destroy'])->name('admin.products.destroy');
    Route::put('/products/{id}/status', [ProductController::class, 'updateStatus'])->name('admin.products.status');

    // Orders
    Route::get('/orders', [OrderController::class, 'index'])->name('admin.orders.index');
    Route::get('/orders/{id}', [OrderController::class, 'show'])->name('admin.orders.show');
    Route::put('/orders/{id}/status', [OrderController::class, 'updateStatus'])->name('admin.orders.status');

    // Stores
    Route::get('/stores', [StoreController::class, 'index'])->name('admin.stores.index');
    Route::get('/stores/{id}', [StoreController::class, 'show'])->name('admin.stores.show');

    // Users
    Route::get('/users', [UserController::class, 'index'])->name('admin.users.index');
    Route::get('/users/{id}', [UserController::class, 'show'])->name('admin.users.show');
    Route::put('/users/{id}/toggle-active', [UserController::class, 'toggleActive'])->name('admin.users.toggleActive');

    // Categories
    Route::get('/categories', [CategoryController::class, 'index'])->name('admin.categories.index');

    // Brands
    Route::get('/brands', [BrandController::class, 'index'])->name('admin.brands.index');

    // Coupons
    Route::get('/coupons', [CouponController::class, 'index'])->name('admin.coupons.index');

    // Reviews (moderation)
    Route::get('/reviews', [ReviewController::class, 'index'])->name('admin.reviews.index');
    Route::put('/reviews/{id}/approve', [ReviewController::class, 'approve'])->name('admin.reviews.approve');
    Route::delete('/reviews/{id}', [ReviewController::class, 'destroy'])->name('admin.reviews.destroy');

    // Locations (cantons + cities)
    Route::get('/locations', [LocationController::class, 'index'])->name('admin.locations.index');

    // Roles
    Route::get('/roles', [RoleController::class, 'index'])->name('admin.roles.index');
    Route::get('/roles/{id}', [RoleController::class, 'show'])->name('admin.roles.show');
    Route::put('/roles/{id}/permissions', [RoleController::class, 'updatePermissions'])->name('admin.roles.updatePermissions');

    // Permissions
    Route::get('/permissions', [PermissionController::class, 'index'])->name('admin.permissions.index');

    // Analytics
    Route::get('/analytics', [AnalyticsController::class, 'index'])->name('admin.analytics.index');

    // Settings
    Route::get('/settings', [SettingsController::class, 'index'])->name('admin.settings.index');

    // Inventory
    Route::get('/inventory', [InventoryController::class, 'index'])->name('admin.inventory.index');

    // Transfers
    Route::get('/transfers', [TransferController::class, 'index'])->name('admin.transfers.index');

    // Promotions
    Route::get('/promotions', [PromotionController::class, 'index'])->name('admin.promotions.index');
});
