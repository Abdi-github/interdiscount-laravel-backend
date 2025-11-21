<?php

namespace App\Http\Controllers\Api\Store;

use App\Domain\Transfer\Services\TransferService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Transfer\CreateTransferRequest;
use App\Http\Requests\Transfer\ReceiveTransferRequest;
use App\Http\Resources\StockTransferResource;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TransferController extends Controller
{
    use ApiResponse;

    public function __construct(
        private TransferService $transferService,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $admin = request()->user();
        $perPage = (int) $request->input('limit', 20);

        $filters = $request->only(['status']);
        $filters['store_id'] = $admin->store_id;

        $transfers = $this->transferService->paginate($filters, $perPage);

        return $this->paginated($transfers, 'StockTransferResource', 'Transfers retrieved successfully');
    }

    public function store(CreateTransferRequest $request): JsonResponse
    {
        $admin = request()->user();

        $transfer = $this->transferService->createTransfer(
            $admin->id,
            $request->validated(),
        );

        return $this->created(
            new StockTransferResource($transfer),
            'Transfer request created successfully',
        );
    }

    public function show(int $id): JsonResponse
    {
        $admin = request()->user();
        $transfer = $this->transferService->findById($id);

        if (!$transfer) {
            return $this->notFound('Transfer not found');
        }

        if ($transfer->from_store_id !== $admin->store_id && $transfer->to_store_id !== $admin->store_id) {
            return $this->forbidden('You do not have access to this transfer');
        }

        return $this->success(
            new StockTransferResource($transfer),
            'Transfer retrieved successfully',
        );
    }

    public function ship(int $id): JsonResponse
    {
        $admin = request()->user();
        $transfer = $this->transferService->findById($id);

        if (!$transfer) {
            return $this->notFound('Transfer not found');
        }

        if ($transfer->from_store_id !== $admin->store_id) {
            return $this->forbidden('Only the source store can ship a transfer');
        }

        try {
            $updated = $this->transferService->shipTransfer($transfer);

            return $this->success(
                new StockTransferResource($updated),
                'Transfer shipped successfully',
            );
        } catch (\InvalidArgumentException $e) {
            return $this->error($e->getMessage(), 422);
        }
    }

    public function receive(ReceiveTransferRequest $request, int $id): JsonResponse
    {
        $admin = request()->user();
        $transfer = $this->transferService->findById($id);

        if (!$transfer) {
            return $this->notFound('Transfer not found');
        }

        if ($transfer->to_store_id !== $admin->store_id) {
            return $this->forbidden('Only the destination store can receive a transfer');
        }

        try {
            $updated = $this->transferService->receiveTransfer(
                $transfer,
                $request->validated('items'),
            );

            return $this->success(
                new StockTransferResource($updated),
                'Transfer received successfully',
            );
        } catch (\InvalidArgumentException $e) {
            return $this->error($e->getMessage(), 422);
        }
    }

    public function cancel(int $id): JsonResponse
    {
        $admin = request()->user();
        $transfer = $this->transferService->findById($id);

        if (!$transfer) {
            return $this->notFound('Transfer not found');
        }

        if ($transfer->from_store_id !== $admin->store_id && $transfer->to_store_id !== $admin->store_id) {
            return $this->forbidden('You do not have access to this transfer');
        }

        try {
            $updated = $this->transferService->cancelTransfer($transfer);

            return $this->success(
                new StockTransferResource($updated),
                'Transfer cancelled successfully',
            );
        } catch (\InvalidArgumentException $e) {
            return $this->error($e->getMessage(), 422);
        }
    }
}
