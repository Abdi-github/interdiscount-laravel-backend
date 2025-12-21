<?php

namespace App\Infrastructure\Providers;

use Illuminate\Support\ServiceProvider;

// Domain Interfaces
use App\Domain\User\Repositories\UserRepositoryInterface;
use App\Domain\User\Repositories\AddressRepositoryInterface;
use App\Domain\Admin\Repositories\AdminRepositoryInterface;
use App\Domain\Product\Repositories\ProductRepositoryInterface;
use App\Domain\Category\Repositories\CategoryRepositoryInterface;
use App\Domain\Brand\Repositories\BrandRepositoryInterface;
use App\Domain\Order\Repositories\OrderRepositoryInterface;
use App\Domain\Order\Repositories\OrderItemRepositoryInterface;
use App\Domain\Payment\Repositories\PaymentRepositoryInterface;
use App\Domain\Store\Repositories\StoreRepositoryInterface;
use App\Domain\Inventory\Repositories\StoreInventoryRepositoryInterface;
use App\Domain\Transfer\Repositories\StockTransferRepositoryInterface;
use App\Domain\Promotion\Repositories\StorePromotionRepositoryInterface;
use App\Domain\Review\Repositories\ReviewRepositoryInterface;
use App\Domain\Wishlist\Repositories\WishlistRepositoryInterface;
use App\Domain\Coupon\Repositories\CouponRepositoryInterface;
use App\Domain\Notification\Repositories\NotificationRepositoryInterface;
use App\Domain\Location\Repositories\CantonRepositoryInterface;
use App\Domain\Location\Repositories\CityRepositoryInterface;
use App\Domain\RBAC\Repositories\RoleRepositoryInterface;
use App\Domain\RBAC\Repositories\PermissionRepositoryInterface;

// Eloquent Implementations
use App\Infrastructure\Persistence\Eloquent\EloquentUserRepository;
use App\Infrastructure\Persistence\Eloquent\EloquentAddressRepository;
use App\Infrastructure\Persistence\Eloquent\EloquentAdminRepository;
use App\Infrastructure\Persistence\Eloquent\EloquentProductRepository;
use App\Infrastructure\Persistence\Eloquent\EloquentCategoryRepository;
use App\Infrastructure\Persistence\Eloquent\EloquentBrandRepository;
use App\Infrastructure\Persistence\Eloquent\EloquentOrderRepository;
use App\Infrastructure\Persistence\Eloquent\EloquentOrderItemRepository;
use App\Infrastructure\Persistence\Eloquent\EloquentPaymentRepository;
use App\Infrastructure\Persistence\Eloquent\EloquentStoreRepository;
use App\Infrastructure\Persistence\Eloquent\EloquentStoreInventoryRepository;
use App\Infrastructure\Persistence\Eloquent\EloquentStockTransferRepository;
use App\Infrastructure\Persistence\Eloquent\EloquentStorePromotionRepository;
use App\Infrastructure\Persistence\Eloquent\EloquentReviewRepository;
use App\Infrastructure\Persistence\Eloquent\EloquentWishlistRepository;
use App\Infrastructure\Persistence\Eloquent\EloquentCouponRepository;
use App\Infrastructure\Persistence\Eloquent\EloquentNotificationRepository;
use App\Infrastructure\Persistence\Eloquent\EloquentCantonRepository;
use App\Infrastructure\Persistence\Eloquent\EloquentCityRepository;
use App\Infrastructure\Persistence\Eloquent\EloquentRoleRepository;
use App\Infrastructure\Persistence\Eloquent\EloquentPermissionRepository;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(UserRepositoryInterface::class, EloquentUserRepository::class);
        $this->app->bind(AddressRepositoryInterface::class, EloquentAddressRepository::class);
        $this->app->bind(AdminRepositoryInterface::class, EloquentAdminRepository::class);
        $this->app->bind(ProductRepositoryInterface::class, EloquentProductRepository::class);
        $this->app->bind(CategoryRepositoryInterface::class, EloquentCategoryRepository::class);
        $this->app->bind(BrandRepositoryInterface::class, EloquentBrandRepository::class);
        $this->app->bind(OrderRepositoryInterface::class, EloquentOrderRepository::class);
        $this->app->bind(OrderItemRepositoryInterface::class, EloquentOrderItemRepository::class);
        $this->app->bind(PaymentRepositoryInterface::class, EloquentPaymentRepository::class);
        $this->app->bind(StoreRepositoryInterface::class, EloquentStoreRepository::class);
        $this->app->bind(StoreInventoryRepositoryInterface::class, EloquentStoreInventoryRepository::class);
        $this->app->bind(StockTransferRepositoryInterface::class, EloquentStockTransferRepository::class);
        $this->app->bind(StorePromotionRepositoryInterface::class, EloquentStorePromotionRepository::class);
        $this->app->bind(ReviewRepositoryInterface::class, EloquentReviewRepository::class);
        $this->app->bind(WishlistRepositoryInterface::class, EloquentWishlistRepository::class);
        $this->app->bind(CouponRepositoryInterface::class, EloquentCouponRepository::class);
        $this->app->bind(NotificationRepositoryInterface::class, EloquentNotificationRepository::class);
        $this->app->bind(CantonRepositoryInterface::class, EloquentCantonRepository::class);
        $this->app->bind(CityRepositoryInterface::class, EloquentCityRepository::class);
        $this->app->bind(RoleRepositoryInterface::class, EloquentRoleRepository::class);
        $this->app->bind(PermissionRepositoryInterface::class, EloquentPermissionRepository::class);
    }
}
