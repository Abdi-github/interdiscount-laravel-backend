<?php

namespace App\Http\Controllers\Api\Store;

use App\Domain\Order\Enums\OrderStatus;
use App\Domain\Order\Events\OrderConfirmed;
use App\Domain\Order\Events\OrderReadyForPickup;
use App\Domain\Order\Events\OrderPickedUp;
use App\Domain\Order\Events\OrderCancelled;
use App\Domain\Order\Repositories\OrderRepositoryInterface;
use App\Domain\Order\Services\OrderService;
use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PickupOrderController extends Controller
{
    use ApiResponse;

    public function __construct(
        private OrderRepositoryInterface $orderRepository,
        private OrderService $orderService,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $admin = request()->user();
        $perPage = (int) $request->input('limit', 20);

        $filters = $request->only(['status']);
        $filters['store_pickup_id'] = $admin->store_id;
        $filters['is_store_pickup'] = true;

        $orders = $this->orderRepository->paginate($filters, $perPage);

        return $this->paginated($orders, 'OrderResource', 'Pickup orders retrieved successfully');
    }

    public function show(int $id): JsonResponse
    {
        $admin = request()->user();
        $order = $this->orderRepository->findById($id);

        if (!$order || !$order->is_store_pickup || $order->store_pickup_id !== $admin->store_id) {
            return $this->notFound('Pickup order not found');
        }

        return $this->success(
            new OrderResource($order),
            'Pickup order retrieved successfully',
        );
    }

    public function confirm(int $id): JsonResponse
    {
        $admin = request()->user();
        $order = $this->orderRepository->findById($id);

        if (!$order || !$order->is_store_pickup || $order->store_pickup_id !== $admin->store_id) {
            return $this->notFound('Pickup order not found');
        }

        try {
            $updated = $this->orderService->updateStatus($order, OrderStatus::CONFIRMED);
            OrderConfirmed::dispatch($updated);

            return $this->success(
                new OrderResource($updated),
                'Pickup order confirmed successfully',
            );
        } catch (\InvalidArgumentException $e) {
            return $this->error($e->getMessage(), 422);
        }
    }

    public function ready(int $id): JsonResponse
    {
        $admin = request()->user();
        $order = $this->orderRepository->findById($id);

        if (!$order || !$order->is_store_pickup || $order->store_pickup_id !== $admin->store_id) {
            return $this->notFound('Pickup order not found');
        }

        try {
            $updated = $this->orderService->updateStatus($order, OrderStatus::READY_FOR_PICKUP);
            OrderReadyForPickup::dispatch($updated);

            return $this->success(
                new OrderResource($updated),
                'Pickup order marked as ready',
            );
        } catch (\InvalidArgumentException $e) {
            return $this->error($e->getMessage(), 422);
        }
    }

    public function collected(int $id): JsonResponse
    {
        $admin = request()->user();
        $order = $this->orderRepository->findById($id);

        if (!$order || !$order->is_store_pickup || $order->store_pickup_id !== $admin->store_id) {
            return $this->notFound('Pickup order not found');
        }

        try {
            $updated = $this->orderService->updateStatus($order, OrderStatus::PICKED_UP);
            OrderPickedUp::dispatch($updated);

            return $this->success(
                new OrderResource($updated),
                'Pickup order marked as collected',
            );
        } catch (\InvalidArgumentException $e) {
            return $this->error($e->getMessage(), 422);
        }
    }

    public function cancel(int $id): JsonResponse
    {
        $admin = request()->user();
        $order = $this->orderRepository->findById($id);

        if (!$order || !$order->is_store_pickup || $order->store_pickup_id !== $admin->store_id) {
            return $this->notFound('Pickup order not found');
        }

        try {
            $updated = $this->orderService->cancelOrder($order, 'Cancelled by store staff');

            return $this->success(
                new OrderResource($updated),
                'Pickup order cancelled successfully',
            );
        } catch (\InvalidArgumentException $e) {
            return $this->error($e->getMessage(), 422);
        }
    }
}
