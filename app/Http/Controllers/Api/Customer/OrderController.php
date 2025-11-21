<?php

namespace App\Http\Controllers\Api\Customer;

use App\Domain\Order\Services\OrderService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Order\CancelOrderRequest;
use App\Http\Requests\Order\CreateOrderRequest;
use App\Http\Resources\OrderResource;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    use ApiResponse;

    public function __construct(
        private OrderService $orderService,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $perPage = (int) $request->input('limit', 20);
        $filters = $request->only(['status', 'payment_status', 'sort_by', 'sort_order']);

        $orders = $this->orderService->paginateForUser(
            $request->user()->id,
            $filters,
            $perPage,
        );

        return $this->paginated($orders, 'OrderResource', 'Orders retrieved successfully');
    }

    public function store(CreateOrderRequest $request): JsonResponse
    {
        try {
            $order = $this->orderService->createOrder(
                $request->user()->id,
                $request->validated(),
            );

            return $this->created(
                new OrderResource($order),
                'Order created successfully',
            );
        } catch (\InvalidArgumentException $e) {
            return $this->error($e->getMessage(), 422);
        }
    }

    public function show(Request $request, int $id): JsonResponse
    {
        $order = $this->orderService->findById($id);

        if (!$order || $order->user_id !== $request->user()->id) {
            return $this->notFound('Order not found');
        }

        return $this->success(
            new OrderResource($order),
            'Order retrieved successfully',
        );
    }

    public function cancel(CancelOrderRequest $request, int $id): JsonResponse
    {
        $order = $this->orderService->findById($id);

        if (!$order || $order->user_id !== $request->user()->id) {
            return $this->notFound('Order not found');
        }

        try {
            $updated = $this->orderService->cancelOrder(
                $order,
                $request->validated('reason'),
            );

            return $this->success(
                new OrderResource($updated),
                'Order cancelled successfully',
            );
        } catch (\InvalidArgumentException $e) {
            return $this->error($e->getMessage(), 422);
        }
    }

    public function returnOrder(Request $request, int $id): JsonResponse
    {
        $order = $this->orderService->findById($id);

        if (!$order || $order->user_id !== $request->user()->id) {
            return $this->notFound('Order not found');
        }

        try {
            $updated = $this->orderService->returnOrder($order);

            return $this->success(
                new OrderResource($updated),
                'Return request submitted successfully',
            );
        } catch (\InvalidArgumentException $e) {
            return $this->error($e->getMessage(), 422);
        }
    }
}
