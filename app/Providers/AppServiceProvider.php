<?php

namespace App\Providers;

use App\Domain\Admin\Models\Admin;
use App\Domain\Auth\Events\PasswordChanged;
use App\Domain\Auth\Events\PasswordResetRequested;
use App\Domain\Auth\Events\UserEmailVerified;
use App\Domain\Auth\Events\UserRegistered;
use App\Domain\Auth\Listeners\SendPasswordChangedEmail;
use App\Domain\Auth\Listeners\SendPasswordResetEmail;
use App\Domain\Auth\Listeners\SendVerificationEmail;
use App\Domain\Auth\Listeners\SendWelcomeEmail;
use App\Domain\Inventory\Events\LowStockDetected;
use App\Domain\Inventory\Listeners\NotifyLowStock;
use App\Domain\Order\Events\OrderCancelled;
use App\Domain\Order\Events\OrderConfirmed;
use App\Domain\Order\Events\OrderDelivered;
use App\Domain\Order\Events\OrderPlaced;
use App\Domain\Order\Events\OrderReadyForPickup;
use App\Domain\Order\Events\OrderReturned;
use App\Domain\Order\Events\OrderShipped;
use App\Domain\Order\Listeners\NotifyOrderPlaced;
use App\Domain\Order\Listeners\NotifyOrderStatusChanged;
use App\Domain\Order\Listeners\ProcessOrderRefund;
use App\Domain\Order\Listeners\UpdateInventoryOnOrder;
use App\Domain\Payment\Events\PaymentFailed;
use App\Domain\Payment\Events\PaymentRefunded;
use App\Domain\Payment\Events\PaymentSucceeded;
use App\Domain\Payment\Listeners\NotifyPaymentFailed;
use App\Domain\Payment\Listeners\NotifyPaymentRefunded;
use App\Domain\Payment\Listeners\UpdateOrderPaymentStatus;
use App\Domain\Product\Events\ProductCreated;
use App\Domain\Product\Events\ProductStatusChanged;
use App\Domain\Product\Events\ProductUpdated;
use App\Domain\Product\Listeners\IndexProductInSearch;
use App\Domain\Product\Listeners\RemoveProductFromSearch;
use App\Domain\Review\Events\ReviewApproved;
use App\Domain\Review\Events\ReviewCreated;
use App\Domain\Review\Listeners\NotifyReviewModeration;
use App\Domain\Review\Listeners\UpdateProductRating;
use App\Domain\Transfer\Events\TransferApproved;
use App\Domain\Transfer\Events\TransferCancelled;
use App\Domain\Transfer\Events\TransferReceived;
use App\Domain\Transfer\Events\TransferRequested;
use App\Domain\Transfer\Events\TransferShipped;
use App\Domain\Transfer\Listeners\NotifyTransferStatusChanged;
use App\Domain\Transfer\Listeners\UpdateInventoryOnTransferReceive;
use App\Domain\Transfer\Listeners\UpdateInventoryOnTransferShip;
use Dedoc\Scramble\Scramble;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->register(\App\Infrastructure\Providers\RepositoryServiceProvider::class);
    }

    public function boot(): void
    {
        Scramble::configure()->expose(
            ui: '/docs/api/v1',
            document: '/docs/api/v1/openapi.json',
        );

        $this->configureGate();
        $this->configureEvents();
    }

    private function configureGate(): void
    {
        Gate::before(function ($user, string $ability) {
            // RBAC applies only to Admin model
            if (!$user instanceof Admin) {
                return null;
            }

            // Super admin wildcard bypass
            if ($user->hasPermissionTo('*:*')) {
                return true;
            }

            // Direct permission match (e.g. 'products:read')
            if ($user->hasPermissionTo($ability)) {
                return true;
            }

            // Resource wildcard (e.g. 'products:*' grants all product actions)
            if (str_contains($ability, ':')) {
                [$resource] = explode(':', $ability, 2);
                if ($user->hasPermissionTo("{$resource}:*")) {
                    return true;
                }
            }

            return null;
        });
    }

    private function configureEvents(): void
    {
        // Auth events
        Event::listen(UserRegistered::class, SendVerificationEmail::class);
        Event::listen(UserEmailVerified::class, SendWelcomeEmail::class);
        Event::listen(PasswordResetRequested::class, SendPasswordResetEmail::class);
        Event::listen(PasswordChanged::class, SendPasswordChangedEmail::class);

        // Order events
        Event::listen(OrderPlaced::class, [NotifyOrderPlaced::class, UpdateInventoryOnOrder::class]);
        Event::listen(OrderConfirmed::class, NotifyOrderStatusChanged::class);
        Event::listen(OrderShipped::class, NotifyOrderStatusChanged::class);
        Event::listen(OrderDelivered::class, NotifyOrderStatusChanged::class);
        Event::listen(OrderCancelled::class, [NotifyOrderStatusChanged::class, ProcessOrderRefund::class]);
        Event::listen(OrderReturned::class, [NotifyOrderStatusChanged::class, ProcessOrderRefund::class]);
        Event::listen(OrderReadyForPickup::class, NotifyOrderStatusChanged::class);

        // Payment events
        Event::listen(PaymentSucceeded::class, UpdateOrderPaymentStatus::class);
        Event::listen(PaymentFailed::class, NotifyPaymentFailed::class);
        Event::listen(PaymentRefunded::class, NotifyPaymentRefunded::class);

        // Product events
        Event::listen(ProductCreated::class, IndexProductInSearch::class);
        Event::listen(ProductUpdated::class, IndexProductInSearch::class);
        Event::listen(ProductStatusChanged::class, [IndexProductInSearch::class, RemoveProductFromSearch::class]);

        // Inventory events
        Event::listen(LowStockDetected::class, NotifyLowStock::class);

        // Transfer events
        Event::listen(TransferRequested::class, NotifyTransferStatusChanged::class);
        Event::listen(TransferApproved::class, NotifyTransferStatusChanged::class);
        Event::listen(TransferShipped::class, [NotifyTransferStatusChanged::class, UpdateInventoryOnTransferShip::class]);
        Event::listen(TransferReceived::class, [NotifyTransferStatusChanged::class, UpdateInventoryOnTransferReceive::class]);
        Event::listen(TransferCancelled::class, NotifyTransferStatusChanged::class);

        // Review events
        Event::listen(ReviewCreated::class, [UpdateProductRating::class, NotifyReviewModeration::class]);
        Event::listen(ReviewApproved::class, UpdateProductRating::class);
    }
}
