<?php

namespace App\Domain\Admin\Services;

use App\Domain\Admin\Models\Admin;
use App\Domain\Admin\Repositories\AdminRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;

class AdminService
{
    public function __construct(
        private AdminRepositoryInterface $adminRepository,
    ) {}

    public function findById(int $id): ?Admin
    {
        return $this->adminRepository->findById($id);
    }

    public function paginate(array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        return $this->adminRepository->paginate($filters, $perPage);
    }

    public function create(array $data): Admin
    {
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        return $this->adminRepository->create($data);
    }

    public function update(int $id, array $data): Admin
    {
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        return $this->adminRepository->update($id, $data);
    }

    public function delete(int $id): bool
    {
        return $this->adminRepository->delete($id);
    }

    public function toggleStatus(int $id): Admin
    {
        $admin = $this->adminRepository->findById($id);

        if (!$admin) {
            throw new \RuntimeException('Admin not found');
        }

        return $this->adminRepository->update($id, [
            'is_active' => !$admin->is_active,
        ]);
    }

    public function findByStore(int $storeId): Collection
    {
        return $this->adminRepository->findByStore($storeId);
    }
}
