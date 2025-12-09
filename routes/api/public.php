<?php

use App\Http\Controllers\Api\Public\AuthController;
use App\Http\Controllers\Api\Public\BrandController;
use App\Http\Controllers\Api\Public\CantonController;
use App\Http\Controllers\Api\Public\CategoryController;
use App\Http\Controllers\Api\Public\CityController;
use App\Http\Controllers\Api\Public\ProductController;
use App\Http\Controllers\Api\Public\SearchController;
use App\Http\Controllers\Api\Public\StoreController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public API Routes
|--------------------------------------------------------------------------
|
| These routes are publicly accessible without authentication.
| Prefix: /api/v1/public
|
*/

Route::prefix('public')->group(function () {
    // Auth routes
    Route::prefix('auth')->group(function () {
        Route::post('/register', [AuthController::class, 'register']);
        Route::post('/login', [AuthController::class, 'login']);
        Route::post('/refresh', [AuthController::class, 'refresh']);
        Route::get('/verify-email/{token}', [AuthController::class, 'verifyEmail']);
        Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
        Route::post('/reset-password', [AuthController::class, 'resetPassword']);
        Route::post('/resend-verification', [AuthController::class, 'resendVerification']);
    });

    // Cantons
    Route::get('/cantons', [CantonController::class, 'index']);

    // Cities
    Route::get('/cities', [CityController::class, 'index']);

    // Stores
    Route::prefix('stores')->group(function () {
        Route::get('/', [StoreController::class, 'index']);
        Route::get('/slug/{slug}', [StoreController::class, 'showBySlug']);
        Route::get('/{id}', [StoreController::class, 'show'])->where('id', '[0-9]+');
    });

    // Categories
    Route::prefix('categories')->group(function () {
        Route::get('/', [CategoryController::class, 'index']);
        Route::get('/product-counts', [CategoryController::class, 'productCounts']);
        Route::get('/slug/{slug}', [CategoryController::class, 'showBySlug']);
        Route::get('/{id}/children', [CategoryController::class, 'children'])->where('id', '[0-9]+');
        Route::get('/{id}/breadcrumb', [CategoryController::class, 'breadcrumb'])->where('id', '[0-9]+');
        Route::get('/{id}/products', [CategoryController::class, 'products'])->where('id', '[0-9]+');
        Route::get('/{id}', [CategoryController::class, 'show'])->where('id', '[0-9]+');
    });

    // Brands
    Route::get('/brands', [BrandController::class, 'index']);

    // Products
    Route::prefix('products')->group(function () {
        Route::get('/', [ProductController::class, 'index']);
        Route::get('/slug/{slug}', [ProductController::class, 'showBySlug']);
        Route::get('/{id}/reviews', [ProductController::class, 'reviews'])->where('id', '[0-9]+');
        Route::get('/{id}/related', [ProductController::class, 'related'])->where('id', '[0-9]+');
        Route::get('/{id}', [ProductController::class, 'show'])->where('id', '[0-9]+');
    });

    // Search
    Route::get('/search', [SearchController::class, 'index']);
});
