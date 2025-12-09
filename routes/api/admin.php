<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin API Routes
|--------------------------------------------------------------------------
|
| These routes require auth + admin role (super_admin or platform_admin).
| Prefix: /api/v1/admin
|
*/

Route::prefix('admin')->middleware(['auth:sanctum', 'require.admin'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [\App\Http\Controllers\Api\Admin\DashboardController::class, 'index']);

    // Users (customers)
    Route::prefix('users')->group(function () {
        Route::get('/', [\App\Http\Controllers\Api\Admin\UserController::class, 'index']);
        Route::get('/{id}', [\App\Http\Controllers\Api\Admin\UserController::class, 'show']);
        Route::put('/{id}', [\App\Http\Controllers\Api\Admin\UserController::class, 'update']);
        Route::put('/{id}/status', [\App\Http\Controllers\Api\Admin\UserController::class, 'toggleStatus']);
    });

    // Products
    Route::prefix('products')->group(function () {
        Route::get('/', [\App\Http\Controllers\Api\Admin\ProductController::class, 'index']);
        Route::post('/', [\App\Http\Controllers\Api\Admin\ProductController::class, 'store']);
        Route::get('/{id}', [\App\Http\Controllers\Api\Admin\ProductController::class, 'show']);
        Route::put('/{id}', [\App\Http\Controllers\Api\Admin\ProductController::class, 'update']);
        Route::put('/{id}/status', [\App\Http\Controllers\Api\Admin\ProductController::class, 'updateStatus']);
        Route::post('/{id}/images', [\App\Http\Controllers\Api\Admin\ProductController::class, 'uploadImages']);
        Route::delete('/{id}/images/{imageId}', [\App\Http\Controllers\Api\Admin\ProductController::class, 'deleteImage']);
    });

    // Categories
    Route::prefix('categories')->group(function () {
        Route::get('/', [\App\Http\Controllers\Api\Admin\CategoryController::class, 'index']);
        Route::post('/', [\App\Http\Controllers\Api\Admin\CategoryController::class, 'store']);
        Route::put('/reorder', [\App\Http\Controllers\Api\Admin\CategoryController::class, 'reorder']);
        Route::put('/{id}', [\App\Http\Controllers\Api\Admin\CategoryController::class, 'update']);
        Route::delete('/{id}', [\App\Http\Controllers\Api\Admin\CategoryController::class, 'destroy']);
    });

    // Brands
    Route::prefix('brands')->group(function () {
        Route::get('/', [\App\Http\Controllers\Api\Admin\BrandController::class, 'index']);
        Route::post('/', [\App\Http\Controllers\Api\Admin\BrandController::class, 'store']);
        Route::get('/{id}', [\App\Http\Controllers\Api\Admin\BrandController::class, 'show']);
        Route::put('/{id}', [\App\Http\Controllers\Api\Admin\BrandController::class, 'update']);
        Route::delete('/{id}', [\App\Http\Controllers\Api\Admin\BrandController::class, 'destroy']);
    });

    // Stores
    Route::prefix('stores')->group(function () {
        Route::get('/', [\App\Http\Controllers\Api\Admin\StoreController::class, 'index']);
        Route::post('/', [\App\Http\Controllers\Api\Admin\StoreController::class, 'store']);
        Route::get('/{id}', [\App\Http\Controllers\Api\Admin\StoreController::class, 'show']);
        Route::put('/{id}', [\App\Http\Controllers\Api\Admin\StoreController::class, 'update']);
        Route::put('/{id}/status', [\App\Http\Controllers\Api\Admin\StoreController::class, 'toggleStatus']);
        Route::get('/{id}/inventory', [\App\Http\Controllers\Api\Admin\StoreController::class, 'inventory']);
        Route::get('/{id}/staff', [\App\Http\Controllers\Api\Admin\StoreController::class, 'staff']);
        Route::put('/{id}/staff', [\App\Http\Controllers\Api\Admin\StoreController::class, 'updateStaff']);
        Route::get('/{id}/analytics', [\App\Http\Controllers\Api\Admin\StoreController::class, 'analytics']);
    });

    // Orders
    Route::prefix('orders')->group(function () {
        Route::get('/', [\App\Http\Controllers\Api\Admin\OrderController::class, 'index']);
        Route::get('/export', [\App\Http\Controllers\Api\Admin\OrderController::class, 'export']);
        Route::get('/{id}', [\App\Http\Controllers\Api\Admin\OrderController::class, 'show']);
        Route::put('/{id}/status', [\App\Http\Controllers\Api\Admin\OrderController::class, 'updateStatus']);
    });

    // Reviews
    Route::prefix('reviews')->group(function () {
        Route::get('/', [\App\Http\Controllers\Api\Admin\ReviewController::class, 'index']);
        Route::get('/{id}', [\App\Http\Controllers\Api\Admin\ReviewController::class, 'show']);
        Route::put('/{id}/approve', [\App\Http\Controllers\Api\Admin\ReviewController::class, 'approve']);
        Route::delete('/{id}', [\App\Http\Controllers\Api\Admin\ReviewController::class, 'destroy']);
    });

    // Coupons
    Route::prefix('coupons')->group(function () {
        Route::get('/', [\App\Http\Controllers\Api\Admin\CouponController::class, 'index']);
        Route::post('/', [\App\Http\Controllers\Api\Admin\CouponController::class, 'store']);
        Route::get('/{id}', [\App\Http\Controllers\Api\Admin\CouponController::class, 'show']);
        Route::put('/{id}', [\App\Http\Controllers\Api\Admin\CouponController::class, 'update']);
        Route::delete('/{id}', [\App\Http\Controllers\Api\Admin\CouponController::class, 'destroy']);
    });

    // Transfers
    Route::prefix('transfers')->group(function () {
        Route::get('/', [\App\Http\Controllers\Api\Admin\TransferController::class, 'index']);
        Route::get('/analytics', [\App\Http\Controllers\Api\Admin\TransferController::class, 'analytics']);
        Route::get('/{id}', [\App\Http\Controllers\Api\Admin\TransferController::class, 'show']);
        Route::put('/{id}/approve', [\App\Http\Controllers\Api\Admin\TransferController::class, 'approve']);
    });

    // Locations
    Route::prefix('locations')->group(function () {
        // Cantons
        Route::get('/cantons', [\App\Http\Controllers\Api\Admin\LocationController::class, 'cantons']);
        Route::post('/cantons', [\App\Http\Controllers\Api\Admin\LocationController::class, 'storeCanton']);
        Route::put('/cantons/{id}', [\App\Http\Controllers\Api\Admin\LocationController::class, 'updateCanton']);
        Route::delete('/cantons/{id}', [\App\Http\Controllers\Api\Admin\LocationController::class, 'destroyCanton']);
        // Cities
        Route::get('/cities', [\App\Http\Controllers\Api\Admin\LocationController::class, 'cities']);
        Route::post('/cities', [\App\Http\Controllers\Api\Admin\LocationController::class, 'storeCity']);
        Route::put('/cities/{id}', [\App\Http\Controllers\Api\Admin\LocationController::class, 'updateCity']);
        Route::delete('/cities/{id}', [\App\Http\Controllers\Api\Admin\LocationController::class, 'destroyCity']);
    });

    // RBAC
    Route::prefix('rbac')->group(function () {
        Route::get('/roles', [\App\Http\Controllers\Api\Admin\RBACController::class, 'roles']);
        Route::post('/roles', [\App\Http\Controllers\Api\Admin\RBACController::class, 'storeRole']);
        Route::put('/roles/{id}', [\App\Http\Controllers\Api\Admin\RBACController::class, 'updateRole']);
        Route::get('/roles/{id}/permissions', [\App\Http\Controllers\Api\Admin\RBACController::class, 'rolePermissions']);
        Route::put('/roles/{id}/permissions', [\App\Http\Controllers\Api\Admin\RBACController::class, 'updateRolePermissions']);
        Route::get('/permissions', [\App\Http\Controllers\Api\Admin\RBACController::class, 'permissions']);
    });

    // Analytics
    Route::get('/analytics', [\App\Http\Controllers\Api\Admin\AnalyticsController::class, 'index']);
});
