<?php

namespace App\Infrastructure\Persistence\Eloquent;

use App\Domain\Location\Models\City;
use App\Domain\Location\Repositories\CityRepositoryInterface;
use Illuminate\Support\Collection;

class EloquentCityRepository implements CityRepositoryInterface
{
    public function __construct(private City $model) {}

    public function findById(int $id): ?City
    {
        return $this->model->with(['canton'])->find($id);
    }

    public function all(): Collection
    {
        return $this->model->with(['canton'])->orderBy('name->de')->get();
    }

    public function findByCanton(int $cantonId): Collection
    {
        return $this->model->where('canton_id', $cantonId)->orderBy('name->de')->get();
    }

    public function create(array $data): City
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): City
    {
        $city = $this->model->findOrFail($id);
        $city->update($data);
        return $city->fresh(['canton']);
    }

    public function delete(int $id): bool
    {
        return $this->model->findOrFail($id)->delete();
    }
}
