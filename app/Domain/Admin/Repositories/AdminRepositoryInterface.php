<?php

namespace App\Domain\Admin\Repositories;

use App\Domain\Admin\Models\Admin;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface AdminRepositoryInterface
{
    public function findById(int $id): ?Admin;
    public function findByEmail(string $email): ?Admin;
    public function paginate(array $filters = [], int $perPage = 20): LengthAwarePaginator;
    public function create(array $data): Admin;
    public function update(int $id, array $data): Admin;
    public function delete(int $id): bool;
    public function findByStore(int $storeId): \Illuminate\Support\Collection;
}
