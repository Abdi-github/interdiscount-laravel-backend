<?php

namespace App\Domain\Transfer\Repositories;

use App\Domain\Transfer\Models\StockTransfer;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface StockTransferRepositoryInterface
{
    public function findById(int $id): ?StockTransfer;
    public function findByTransferNumber(string $transferNumber): ?StockTransfer;
    public function paginate(array $filters = [], int $perPage = 20): LengthAwarePaginator;
    public function create(array $data): StockTransfer;
    public function update(int $id, array $data): StockTransfer;
}
