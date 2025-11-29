<?php

namespace App\Domain\Location\Services;

use App\Domain\Location\Models\Canton;
use App\Domain\Location\Models\City;
use App\Domain\Location\Repositories\CantonRepositoryInterface;
use App\Domain\Location\Repositories\CityRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class LocationService
{
    public function __construct(
        private CantonRepositoryInterface $cantonRepository,
        private CityRepositoryInterface $cityRepository,
    ) {}

    // Canton methods

    public function getAllCantons(): Collection
    {
        return $this->cantonRepository->all();
    }

    public function findCantonById(int $id): ?Canton
    {
        return $this->cantonRepository->findById($id);
    }

    public function findCantonByCode(string $code): ?Canton
    {
        return $this->cantonRepository->findByCode($code);
    }

    // City methods

    public function getAllCities(array $filters = []): Collection
    {
        if (!empty($filters['canton_id'])) {
            return $this->cityRepository->findByCanton($filters['canton_id']);
        }
        return $this->cityRepository->all();
    }

    public function findCityById(int $id): ?City
    {
        return $this->cityRepository->findById($id);
    }

    public function getCitiesByCanton(int $cantonId): Collection
    {
        return $this->cityRepository->findByCanton($cantonId);
    }

    // Canton CRUD

    public function createCanton(array $data): Canton
    {
        return $this->cantonRepository->create($data);
    }

    public function updateCanton(int $id, array $data): ?Canton
    {
        $canton = $this->cantonRepository->findById($id);
        if (!$canton) {
            return null;
        }
        return $this->cantonRepository->update($id, $data);
    }

    public function deleteCanton(int $id): bool
    {
        $canton = $this->cantonRepository->findById($id);
        if (!$canton) {
            return false;
        }
        return $this->cantonRepository->delete($id);
    }

    // City CRUD

    public function createCity(array $data): City
    {
        return $this->cityRepository->create($data);
    }

    public function updateCity(int $id, array $data): ?City
    {
        $city = $this->cityRepository->findById($id);
        if (!$city) {
            return null;
        }
        return $this->cityRepository->update($id, $data);
    }

    public function deleteCity(int $id): bool
    {
        $city = $this->cityRepository->findById($id);
        if (!$city) {
            return false;
        }
        return $this->cityRepository->delete($id);
    }
}
