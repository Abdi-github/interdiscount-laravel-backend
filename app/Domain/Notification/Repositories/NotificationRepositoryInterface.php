<?php

namespace App\Domain\Notification\Repositories;

use App\Domain\Notification\Models\Notification;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface NotificationRepositoryInterface
{
    public function findById(int $id): ?Notification;
    public function paginateByUser(int $userId, array $filters = [], int $perPage = 20): LengthAwarePaginator;
    public function create(array $data): Notification;
    public function markAsRead(int $id): Notification;
    public function delete(int $id): bool;
    public function unreadCount(int $userId): int;
}
