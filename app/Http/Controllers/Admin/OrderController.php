<?php

namespace App\Http\Controllers\Admin;

use App\Domain\Order\Enums\OrderStatus;
use App\Domain\Order\Enums\PaymentStatus;
use App\Domain\Order\Services\OrderService;
use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class OrderController extends Controller
{
    public function __construct(private OrderService $orderService) {}

    public function index(Request $request): Response
    {
        $orders = $this->orderService->paginate(
            $request->only(['search', 'status', 'payment_status', 'sort_by', 'sort_order']),
            (int) $request->input('per_page', 20)
        );

        return Inertia::render('Orders/Index', [
            'orders' => OrderResource::collection($orders),
            'filters' => $request->only(['search', 'status', 'payment_status', 'sort_by', 'sort_order', 'per_page']),
            'statuses' => collect(OrderStatus::cases())->map(fn ($s) => ['value' => $s->value, 'label' => $s->name]),
            'paymentStatuses' => collect(PaymentStatus::cases())->map(fn ($s) => ['value' => $s->value, 'label' => $s->name]),
        ]);
    }

    public function show(int $id): Response
    {
        $order = $this->orderService->findById($id);

        if (!$order) {
            abort(404);
        }

        $order->load(['user', 'items.product', 'payments', 'shippingAddress', 'billingAddress', 'storePickup']);

        $validTransitions = $order->status->validTransitions();

        return Inertia::render('Orders/Show', [
            'order' => new OrderResource($order),
            'validTransitions' => collect($validTransitions)->map(fn ($s) => ['value' => $s->value, 'label' => $s->name]),
        ]);
    }

    public function updateStatus(Request $request, int $id): RedirectResponse
    {
        $request->validate([
            'status' => ['required', 'string'],
        ]);

        $order = $this->orderService->findById($id);

        if (!$order) {
            abort(404);
        }

        $newStatus = OrderStatus::from($request->input('status'));

        if (!$order->canTransitionTo($newStatus)) {
            return redirect()->back()->with('error', "Cannot transition from {$order->status->value} to {$newStatus->value}");
        }

        $this->orderService->updateStatus($order, $newStatus);

        return redirect()->back()->with('success', 'Order status updated successfully');
    }
}
