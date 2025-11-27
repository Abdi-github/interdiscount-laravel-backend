<?php

namespace App\Domain\User\Services;

use App\Domain\User\Models\User;
use App\Domain\User\Repositories\UserRepositoryInterface;
use App\Domain\User\Repositories\AddressRepositoryInterface;
use App\Domain\User\Models\Address;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;

class UserService
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private AddressRepositoryInterface $addressRepository,
    ) {}

    public function findById(int $id): ?User
    {
        return $this->userRepository->findById($id);
    }

    public function paginate(array $filters = [], int $perPage = 20): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        return $this->userRepository->paginate($filters, $perPage);
    }

    public function update(int $id, array $data): User
    {
        return $this->userRepository->update($id, $data);
    }

    public function updateProfile(int $userId, array $data): User
    {
        return $this->userRepository->update($userId, $data);
    }

    public function changePassword(User $user, string $currentPassword, string $newPassword): bool
    {
        if (!Hash::check($currentPassword, $user->password)) {
            return false;
        }

        $this->userRepository->update($user->id, [
            'password' => $newPassword,
        ]);

        return true;
    }

    // Address methods

    public function getAddresses(int $userId): Collection
    {
        return $this->addressRepository->findByUser($userId);
    }

    public function findAddress(int $id): ?Address
    {
        return $this->addressRepository->findById($id);
    }

    public function createAddress(int $userId, array $data): Address
    {
        $data['user_id'] = $userId;

        // If this is set as default, unset other defaults
        if (!empty($data['is_default'])) {
            $this->clearDefaultAddresses($userId);
        }

        return $this->addressRepository->create($data);
    }

    public function updateAddress(int $id, array $data): Address
    {
        $address = $this->addressRepository->findById($id);

        // If setting as default, unset other defaults
        if (!empty($data['is_default']) && $address) {
            $this->clearDefaultAddresses($address->user_id);
        }

        return $this->addressRepository->update($id, $data);
    }

    public function deleteAddress(int $id): bool
    {
        return $this->addressRepository->delete($id);
    }

    private function clearDefaultAddresses(int $userId): void
    {
        $addresses = $this->addressRepository->findByUser($userId);
        foreach ($addresses as $address) {
            if ($address->is_default) {
                $this->addressRepository->update($address->id, ['is_default' => false]);
            }
        }
    }
}
