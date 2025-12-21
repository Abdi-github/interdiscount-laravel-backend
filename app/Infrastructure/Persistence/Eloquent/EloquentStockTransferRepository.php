<?php

namespace App\Infrastructure\Persistence\Eloquent;

use App\Domain\Transfer\Models\StockTransfer;
use App\Domain\Transfer\Repositories\StockTransferRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class EloquentStockTransferRepository implements StockTransferRepositoryInterface
{
    public function __construct(private StockTransfer $model) {}

    public function findById(int $id): ?StockTransfer
    {
        return $this->model->with(['fromStore', 'toStore', 'initiator', 'approver'])->find($id);
    }

    public function findByTransferNumber(string $transferNumber): ?StockTransfer
    {
        return $this->model->with(['fromStore', 'toStore', 'initiator', 'approver'])
            ->where('transfer_number', $transferNumber)
            ->first();
    }

    public function paginate(array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        $query = $this->model->with(['fromStore', 'toStore', 'initiator']);

        if (!empty($filters['from_store_id'])) {
            $query->where('from_store_id', $filters['from_store_id']);
        }

        if (!empty($filters['to_store_id'])) {
            $query->where('to_store_id', $filters['to_store_id']);
        }

        if (!empty($filters['store_id'])) {
            $storeId = $filters['store_id'];
            $query->where(function ($q) use ($storeId) {
                $q->where('from_store_id', $storeId)
                  ->orWhere('to_store_id', $storeId);
            });
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    public function create(array $data): StockTransfer
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): StockTransfer
    {
        $transfer = $this->model->findOrFail($id);
        $transfer->update($data);
        return $transfer->fresh(['fromStore', 'toStore', 'initiator', 'approver']);
    }
}
