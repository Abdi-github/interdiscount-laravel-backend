<?php

use App\Http\Controllers\Api\Customer\AddressController;
use App\Http\Controllers\Api\Customer\CouponController;
use App\Http\Controllers\Api\Customer\NotificationController;
use App\Http\Controllers\Api\Customer\OrderController;
use App\Http\Controllers\Api\Customer\PaymentController;
use App\Http\Controllers\Api\Customer\ProfileController;
use App\Http\Controllers\Api\Customer\ReviewController;
use App\Http\Controllers\Api\Customer\WishlistController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Customer API Routes
|--------------------------------------------------------------------------
|
| These routes require Sanctum token authentication (User model).
| Prefix: /api/v1/customer
|
*/

Route::prefix('customer')->middleware('auth:sanctum')->group(function () {
    // Logout
    Route::post('/logout', [\App\Http\Controllers\Api\Public\AuthController::class, 'logout']);

    // Profile
    Route::get('/', [ProfileController::class, 'show']);
    Route::put('/', [ProfileController::class, 'update']);
    Route::post('/change-password', [ProfileController::class, 'changePassword']);

    // Addresses
    Route::prefix('addresses')->group(function () {
        Route::get('/', [AddressController::class, 'index']);
        Route::post('/', [AddressController::class, 'store']);
        Route::get('/{id}', [AddressController::class, 'show'])->where('id', '[0-9]+');
        Route::put('/{id}', [AddressController::class, 'update'])->where('id', '[0-9]+');
        Route::delete('/{id}', [AddressController::class, 'destroy'])->where('id', '[0-9]+');
    });

    // Wishlist
    Route::prefix('wishlist')->group(function () {
        Route::get('/', [WishlistController::class, 'index']);
        Route::post('/', [WishlistController::class, 'store']);
        Route::delete('/{productId}', [WishlistController::class, 'destroy'])->where('productId', '[0-9]+');
    });

    // Reviews
    Route::prefix('reviews')->group(function () {
        Route::get('/', [ReviewController::class, 'index']);
        Route::post('/', [ReviewController::class, 'store']);
        Route::put('/{id}', [ReviewController::class, 'update'])->where('id', '[0-9]+');
        Route::delete('/{id}', [ReviewController::class, 'destroy'])->where('id', '[0-9]+');
    });

    // Coupons
    Route::prefix('coupons')->group(function () {
        Route::post('/validate', [CouponController::class, 'validate']);
        Route::get('/available', [CouponController::class, 'available']);
    });

    // Notifications
    Route::prefix('notifications')->group(function () {
        Route::get('/', [NotificationController::class, 'index']);
        Route::put('/{id}/read', [NotificationController::class, 'markAsRead'])->where('id', '[0-9]+');
        Route::delete('/{id}', [NotificationController::class, 'destroy'])->where('id', '[0-9]+');
    });

    // Orders
    Route::prefix('orders')->group(function () {
        Route::get('/', [OrderController::class, 'index']);
        Route::post('/', [OrderController::class, 'store']);
        Route::get('/{id}', [OrderController::class, 'show'])->where('id', '[0-9]+');
        Route::post('/{id}/cancel', [OrderController::class, 'cancel'])->where('id', '[0-9]+');
        Route::post('/{id}/return', [OrderController::class, 'returnOrder'])->where('id', '[0-9]+');
    });

    // Payments
    Route::prefix('payments')->group(function () {
        Route::post('/{orderId}/initiate', [PaymentController::class, 'initiate'])->where('orderId', '[0-9]+');
        Route::post('/{orderId}/simulate', [PaymentController::class, 'simulate'])->where('orderId', '[0-9]+');
        Route::post('/{orderId}/invoice', [PaymentController::class, 'confirmInvoice'])->where('orderId', '[0-9]+');
        Route::get('/{orderId}', [PaymentController::class, 'status'])->where('orderId', '[0-9]+');
    });
});
