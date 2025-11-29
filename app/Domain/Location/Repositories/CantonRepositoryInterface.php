<?php

namespace App\Domain\Location\Repositories;

use App\Domain\Location\Models\Canton;
use Illuminate\Support\Collection;

interface CantonRepositoryInterface
{
    public function findById(int $id): ?Canton;
    public function findByCode(string $code): ?Canton;
    public function all(): Collection;
    public function create(array $data): Canton;
    public function update(int $id, array $data): Canton;
    public function delete(int $id): bool;
}
