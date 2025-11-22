<?php

namespace App\Domain\Order\Services;

use App\Domain\Order\Enums\OrderStatus;
use App\Domain\Order\Enums\PaymentStatus;
use App\Domain\Order\Events\OrderCancelled;
use App\Domain\Order\Events\OrderPlaced;
use App\Domain\Order\Events\OrderReturned;
use App\Domain\Order\Models\Order;
use App\Domain\Order\Repositories\OrderItemRepositoryInterface;
use App\Domain\Order\Repositories\OrderRepositoryInterface;
use App\Domain\Product\Repositories\ProductRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;

class OrderService
{
    public function __construct(
        private OrderRepositoryInterface $orderRepository,
        private OrderItemRepositoryInterface $orderItemRepository,
        private ProductRepositoryInterface $productRepository,
    ) {}

    public function findById(int $id): ?Order
    {
        return $this->orderRepository->findById($id);
    }

    public function paginateForUser(int $userId, array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        return $this->orderRepository->paginateForUser($userId, $filters, $perPage);
    }

    public function paginate(array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        return $this->orderRepository->paginate($filters, $perPage);
    }

    public function createOrder(int $userId, array $data): Order
    {
        // Generate unique order number
        $orderNumber = 'ORD-' . strtoupper(Str::random(8)) . '-' . time();

        // Calculate totals from items
        $items = $data['items'];
        $subtotal = 0;

        $resolvedItems = [];
        foreach ($items as $item) {
            $product = $this->productRepository->findById($item['product_id']);
            if (!$product) {
                throw new \InvalidArgumentException("Product {$item['product_id']} not found");
            }

            $unitPrice = (float) $product->price;
            $quantity = (int) $item['quantity'];
            $totalPrice = round($unitPrice * $quantity, 2);
            $subtotal += $totalPrice;

            $resolvedItems[] = [
                'product_id' => $product->id,
                'product_name' => $product->name,
                'product_code' => $product->code,
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'total_price' => $totalPrice,
                'currency' => 'CHF',
            ];
        }

        $shippingFee = (float) ($data['shipping_fee'] ?? 0);
        $discount = (float) ($data['discount'] ?? 0);
        $total = round($subtotal + $shippingFee - $discount, 2);

        $order = $this->orderRepository->create([
            'order_number' => $orderNumber,
            'user_id' => $userId,
            'shipping_address_id' => $data['shipping_address_id'],
            'billing_address_id' => $data['billing_address_id'] ?? $data['shipping_address_id'],
            'status' => OrderStatus::PENDING,
            'payment_method' => $data['payment_method'],
            'payment_status' => PaymentStatus::PENDING,
            'subtotal' => $subtotal,
            'shipping_fee' => $shippingFee,
            'discount' => $discount,
            'total' => $total,
            'currency' => 'CHF',
            'coupon_code' => $data['coupon_code'] ?? null,
            'notes' => $data['notes'] ?? null,
            'store_pickup_id' => $data['store_pickup_id'] ?? null,
            'is_store_pickup' => !empty($data['store_pickup_id']),
        ]);

        // Create order items
        foreach ($resolvedItems as $item) {
            $item['order_id'] = $order->id;
            $this->orderItemRepository->create($item);
        }

        // Reload with relationships
        $order = $this->orderRepository->findById($order->id);

        OrderPlaced::dispatch($order);

        return $order;
    }

    public function cancelOrder(Order $order, ?string $reason = null): Order
    {
        if (!$order->canTransitionTo(OrderStatus::CANCELLED)) {
            throw new \InvalidArgumentException(
                "Order cannot be cancelled from status {$order->status->value}"
            );
        }

        $order = $this->orderRepository->update($order->id, [
            'status' => OrderStatus::CANCELLED,
            'cancelled_at' => now(),
            'cancellation_reason' => $reason,
        ]);

        OrderCancelled::dispatch($order);

        return $order;
    }

    public function returnOrder(Order $order): Order
    {
        if (!$order->canTransitionTo(OrderStatus::RETURNED)) {
            throw new \InvalidArgumentException(
                "Order cannot be returned from status {$order->status->value}"
            );
        }

        $order = $this->orderRepository->update($order->id, [
            'status' => OrderStatus::RETURNED,
        ]);

        OrderReturned::dispatch($order);

        return $order;
    }

    public function updateStatus(Order $order, OrderStatus $newStatus): Order
    {
        if (!$order->canTransitionTo($newStatus)) {
            throw new \InvalidArgumentException(
                "Cannot transition from {$order->status->value} to {$newStatus->value}"
            );
        }

        $updateData = ['status' => $newStatus];

        if ($newStatus === OrderStatus::DELIVERED) {
            $updateData['delivered_at'] = now();
        }

        return $this->orderRepository->update($order->id, $updateData);
    }
}
