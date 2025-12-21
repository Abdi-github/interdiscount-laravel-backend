<?php

namespace App\Infrastructure\Persistence\Eloquent;

use App\Domain\Location\Models\Canton;
use App\Domain\Location\Repositories\CantonRepositoryInterface;
use Illuminate\Support\Collection;

class EloquentCantonRepository implements CantonRepositoryInterface
{
    public function __construct(private Canton $model) {}

    public function findById(int $id): ?Canton
    {
        return $this->model->find($id);
    }

    public function findByCode(string $code): ?Canton
    {
        return $this->model->where('code', $code)->first();
    }

    public function all(): Collection
    {
        return $this->model->active()->orderBy('name->de')->get();
    }

    public function create(array $data): Canton
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): Canton
    {
        $canton = $this->model->findOrFail($id);
        $canton->update($data);
        return $canton->fresh();
    }

    public function delete(int $id): bool
    {
        return $this->model->findOrFail($id)->delete();
    }
}
