<?php

namespace App\Domain\User\Repositories;

use App\Domain\User\Models\Address;
use Illuminate\Support\Collection;

interface AddressRepositoryInterface
{
    public function findById(int $id): ?Address;
    public function findByUser(int $userId): Collection;
    public function create(array $data): Address;
    public function update(int $id, array $data): Address;
    public function delete(int $id): bool;
}
