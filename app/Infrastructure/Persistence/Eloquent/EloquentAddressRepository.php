<?php

namespace App\Infrastructure\Persistence\Eloquent;

use App\Domain\User\Models\Address;
use App\Domain\User\Repositories\AddressRepositoryInterface;
use Illuminate\Support\Collection;

class EloquentAddressRepository implements AddressRepositoryInterface
{
    public function __construct(private Address $model) {}

    public function findById(int $id): ?Address
    {
        return $this->model->find($id);
    }

    public function findByUser(int $userId): Collection
    {
        return $this->model->where('user_id', $userId)->get();
    }

    public function create(array $data): Address
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): Address
    {
        $address = $this->model->findOrFail($id);
        $address->update($data);
        return $address->fresh();
    }

    public function delete(int $id): bool
    {
        return $this->model->findOrFail($id)->delete();
    }
}
