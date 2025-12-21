<?php

namespace App\Domain\Transfer\Services;

use App\Domain\Transfer\Enums\TransferStatus;
use App\Domain\Transfer\Models\StockTransfer;
use App\Domain\Transfer\Repositories\StockTransferRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;

class TransferService
{
    public function __construct(
        private StockTransferRepositoryInterface $transferRepository,
    ) {}

    public function findById(int $id): ?StockTransfer
    {
        return $this->transferRepository->findById($id);
    }

    public function paginate(array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        return $this->transferRepository->paginate($filters, $perPage);
    }

    public function createTransfer(int $adminId, array $data): StockTransfer
    {
        $transferNumber = 'TRF-' . strtoupper(Str::random(8)) . '-' . time();

        return $this->transferRepository->create([
            'transfer_number' => $transferNumber,
            'from_store_id' => $data['from_store_id'],
            'to_store_id' => $data['to_store_id'],
            'initiated_by' => $adminId,
            'status' => TransferStatus::REQUESTED,
            'items' => $data['items'],
            'notes' => $data['notes'] ?? null,
        ]);
    }

    public function shipTransfer(StockTransfer $transfer): StockTransfer
    {
        $this->validateTransition($transfer, TransferStatus::SHIPPED, [TransferStatus::APPROVED]);

        return $this->transferRepository->update($transfer->id, [
            'status' => TransferStatus::SHIPPED,
            'shipped_at' => now(),
        ]);
    }

    public function receiveTransfer(StockTransfer $transfer, ?array $receivedItems = null): StockTransfer
    {
        $this->validateTransition($transfer, TransferStatus::RECEIVED, [TransferStatus::SHIPPED]);

        $updateData = [
            'status' => TransferStatus::RECEIVED,
            'received_at' => now(),
        ];

        if ($receivedItems) {
            $items = $transfer->items;
            foreach ($items as &$item) {
                foreach ($receivedItems as $received) {
                    if ($item['product_id'] === $received['product_id']) {
                        $item['received_quantity'] = $received['received_quantity'];
                    }
                }
            }
            $updateData['items'] = $items;
        }

        return $this->transferRepository->update($transfer->id, $updateData);
    }

    public function cancelTransfer(StockTransfer $transfer): StockTransfer
    {
        $this->validateTransition($transfer, TransferStatus::CANCELLED, [
            TransferStatus::REQUESTED,
            TransferStatus::APPROVED,
        ]);

        return $this->transferRepository->update($transfer->id, [
            'status' => TransferStatus::CANCELLED,
        ]);
    }

    private function validateTransition(StockTransfer $transfer, TransferStatus $target, array $allowedFrom): void
    {
        if (!in_array($transfer->status, $allowedFrom)) {
            $allowed = implode(', ', array_map(fn ($s) => $s->value, $allowedFrom));
            throw new \InvalidArgumentException(
                "Cannot transition from {$transfer->status->value} to {$target->value}. Allowed from: {$allowed}"
            );
        }
    }
}
