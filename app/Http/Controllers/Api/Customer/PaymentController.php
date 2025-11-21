<?php

namespace App\Http\Controllers\Api\Customer;

use App\Domain\Order\Repositories\OrderRepositoryInterface;
use App\Domain\Payment\Services\PaymentService;
use App\Http\Controllers\Controller;
use App\Http\Resources\PaymentResource;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    use ApiResponse;

    public function __construct(
        private PaymentService $paymentService,
        private OrderRepositoryInterface $orderRepository,
    ) {}

    /**
     * Initiate payment for an order.
     * POST /api/v1/customer/payments/{orderId}/initiate
     */
    public function initiate(Request $request, int $orderId): JsonResponse
    {
        $order = $this->orderRepository->findById($orderId);

        if (!$order || $order->user_id !== $request->user()->id) {
            return $this->notFound('Order not found');
        }

        if ($order->payment_status->value !== 'PENDING') {
            return $this->error('Payment has already been initiated for this order', 422);
        }

        try {
            $payment = $this->paymentService->initiatePayment($order);

            return $this->created(
                new PaymentResource($payment),
                'Payment initiated successfully',
            );
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 422);
        }
    }

    /**
     * Simulate payment (development only).
     * POST /api/v1/customer/payments/{orderId}/simulate
     */
    public function simulate(Request $request, int $orderId): JsonResponse
    {
        if (app()->environment('production')) {
            return $this->error('Payment simulation is not available in production', 403);
        }

        $order = $this->orderRepository->findById($orderId);

        if (!$order || $order->user_id !== $request->user()->id) {
            return $this->notFound('Order not found');
        }

        try {
            $payment = $this->paymentService->simulatePayment($order);

            return $this->success(
                new PaymentResource($payment),
                'Payment simulated successfully',
            );
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 422);
        }
    }

    /**
     * Confirm invoice payment with transfer number.
     * POST /api/v1/customer/payments/{orderId}/invoice
     */
    public function confirmInvoice(Request $request, int $orderId): JsonResponse
    {
        $request->validate([
            'transfer_number' => 'required|string|max:100',
        ]);

        $order = $this->orderRepository->findById($orderId);

        if (!$order || $order->user_id !== $request->user()->id) {
            return $this->notFound('Order not found');
        }

        if ($order->payment_method !== 'invoice') {
            return $this->error('This order does not use invoice payment', 422);
        }

        try {
            $payment = $this->paymentService->confirmInvoice(
                $order,
                $request->input('transfer_number'),
            );

            return $this->success(
                new PaymentResource($payment),
                'Invoice payment confirmed successfully',
            );
        } catch (\InvalidArgumentException $e) {
            return $this->error($e->getMessage(), 422);
        }
    }

    /**
     * Get payment status for an order.
     * GET /api/v1/customer/payments/{orderId}
     */
    public function status(Request $request, int $orderId): JsonResponse
    {
        $order = $this->orderRepository->findById($orderId);

        if (!$order || $order->user_id !== $request->user()->id) {
            return $this->notFound('Order not found');
        }

        $payments = $this->paymentService->getPaymentStatus($order);

        return $this->success(
            PaymentResource::collection($payments),
            'Payment status retrieved successfully',
        );
    }
}
