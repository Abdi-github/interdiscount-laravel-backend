<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Store Manager API Routes
|--------------------------------------------------------------------------
|
| These routes require auth + store_manager role + store access.
| Prefix: /api/v1/store
|
*/

Route::prefix('store')->middleware(['auth:sanctum', 'require.store.access'])->group(function () {
    // Inventory
    Route::prefix('inventory')->group(function () {
        Route::get('/', [\App\Http\Controllers\Api\Store\InventoryController::class, 'index']);
        Route::get('/low-stock', [\App\Http\Controllers\Api\Store\InventoryController::class, 'lowStock']);
        Route::get('/out-of-stock', [\App\Http\Controllers\Api\Store\InventoryController::class, 'outOfStock']);
        Route::get('/export', [\App\Http\Controllers\Api\Store\InventoryController::class, 'export']);
        Route::post('/bulk-update', [\App\Http\Controllers\Api\Store\InventoryController::class, 'bulkUpdate']);
        Route::post('/scan', [\App\Http\Controllers\Api\Store\InventoryController::class, 'scan']);
        Route::get('/{productId}', [\App\Http\Controllers\Api\Store\InventoryController::class, 'show']);
        Route::put('/{productId}', [\App\Http\Controllers\Api\Store\InventoryController::class, 'update']);
    });

    // Transfers
    Route::prefix('transfers')->group(function () {
        Route::get('/', [\App\Http\Controllers\Api\Store\TransferController::class, 'index']);
        Route::post('/', [\App\Http\Controllers\Api\Store\TransferController::class, 'store']);
        Route::get('/{id}', [\App\Http\Controllers\Api\Store\TransferController::class, 'show']);
        Route::put('/{id}/ship', [\App\Http\Controllers\Api\Store\TransferController::class, 'ship']);
        Route::put('/{id}/receive', [\App\Http\Controllers\Api\Store\TransferController::class, 'receive']);
        Route::put('/{id}/cancel', [\App\Http\Controllers\Api\Store\TransferController::class, 'cancel']);
    });

    // Pickup Orders
    Route::prefix('pickup-orders')->group(function () {
        Route::get('/', [\App\Http\Controllers\Api\Store\PickupOrderController::class, 'index']);
        Route::get('/{id}', [\App\Http\Controllers\Api\Store\PickupOrderController::class, 'show']);
        Route::put('/{id}/confirm', [\App\Http\Controllers\Api\Store\PickupOrderController::class, 'confirm']);
        Route::put('/{id}/ready', [\App\Http\Controllers\Api\Store\PickupOrderController::class, 'ready']);
        Route::put('/{id}/collected', [\App\Http\Controllers\Api\Store\PickupOrderController::class, 'collected']);
        Route::put('/{id}/cancel', [\App\Http\Controllers\Api\Store\PickupOrderController::class, 'cancel']);
    });

    // Promotions
    Route::prefix('promotions')->group(function () {
        Route::get('/', [\App\Http\Controllers\Api\Store\PromotionController::class, 'index']);
        Route::post('/', [\App\Http\Controllers\Api\Store\PromotionController::class, 'store']);
        Route::get('/{id}', [\App\Http\Controllers\Api\Store\PromotionController::class, 'show']);
        Route::put('/{id}', [\App\Http\Controllers\Api\Store\PromotionController::class, 'update']);
        Route::delete('/{id}', [\App\Http\Controllers\Api\Store\PromotionController::class, 'destroy']);
    });

    // Store Info
    Route::get('/info', [\App\Http\Controllers\Api\Store\StoreInfoController::class, 'show']);
    Route::put('/info', [\App\Http\Controllers\Api\Store\StoreInfoController::class, 'update']);

    // Staff listing
    Route::get('/staff', function () {
        $admin = request()->user();
        $staff = \App\Domain\Admin\Models\Admin::where('store_id', $admin->store_id)
            ->where('is_active', true)
            ->get(['id', 'first_name', 'last_name', 'email', 'admin_type']);

        return response()->json([
            'success' => true,
            'message' => 'Store staff retrieved successfully',
            'data' => $staff,
        ]);
    });

    // Dashboard & Analytics
    Route::get('/dashboard', [\App\Http\Controllers\Api\Store\StoreDashboardController::class, 'index']);
    Route::get('/analytics', [\App\Http\Controllers\Api\Store\StoreAnalyticsController::class, 'index']);
});
