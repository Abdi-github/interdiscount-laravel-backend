<?php

namespace App\Http\Controllers\Api\Admin;

use App\Domain\Order\Services\OrderService;
use App\Http\Controllers\Controller;
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
        $orders = $this->orderService->paginate(
            $request->only(['search', 'status', 'payment_status', 'user_id', 'sort_by', 'sort_order']),
            $request->integer('limit', 20),
        );

        // TODO: Add caching for frequently accessed order filters
        /* Log::debug('Orders retrieved - count: ' . count($orders->items())); */
        return $this->paginated($orders, 'OrderResource', 'Orders retrieved successfully');
    }

    public function show(int $id): JsonResponse
    {
        $order = $this->orderService->findById($id);

        if (!$order) {
            return $this->notFound('Order not found');
        }

        // TODO: Implement order history tracking for audit trails
        $order->load(['items.product', 'user', 'payments', 'shippingAddress', 'billingAddress']);
        /* Log::debug('Order loaded with relations - items count: ' . count($order->items)); */

        return $this->success(new OrderResource($order), 'Order retrieved successfully');
    }

    public function updateStatus(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'status' => ['required', 'string'],
        ]);

        $order = $this->orderService->findById($id);

        if (!$order) {
            return $this->notFound('Order not found');
        }

        try {
            $order = $this->orderService->updateStatus($id, $request->input('status'));
        } catch (\RuntimeException $e) {
            return $this->error($e->getMessage(), 422);
        }

        return $this->success(new OrderResource($order), 'Order status updated successfully');
    }

    public function export(Request $request): JsonResponse
    {
        $orders = $this->orderService->paginate(
            $request->only(['status', 'payment_status', 'from_date', 'to_date']),
            $request->integer('limit', 1000),
        );

        $exportData = collect($orders->items())->map(fn ($order) => [
            'order_number' => $order->order_number,
            'customer' => $order->user?->first_name . ' ' . $order->user?->last_name,
            'email' => $order->user?->email,
            'status' => $order->status,
            'payment_status' => $order->payment_status,
            'total' => $order->total,
            'currency' => $order->currency,
            'created_at' => $order->created_at?->toISOString(),
        ]);

        /* Log::debug('Export data prepared - rows: ' . count($exportData)); */
        return $this->success($exportData, 'Orders exported successfully');
    }
}
