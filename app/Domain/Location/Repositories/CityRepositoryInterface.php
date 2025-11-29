<?php

namespace App\Domain\Location\Repositories;

use App\Domain\Location\Models\City;
use Illuminate\Support\Collection;

interface CityRepositoryInterface
{
    public function findById(int $id): ?City;
    public function all(): Collection;
    public function findByCanton(int $cantonId): Collection;
    public function create(array $data): City;
    public function update(int $id, array $data): City;
    public function delete(int $id): bool;
}
