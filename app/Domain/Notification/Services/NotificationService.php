<?php

namespace App\Domain\Notification\Services;

use App\Domain\Notification\Models\Notification;
use App\Domain\Notification\Repositories\NotificationRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class NotificationService
{
    public function __construct(
        private NotificationRepositoryInterface $notificationRepository,
    ) {}

    public function paginateByUser(int $userId, array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        return $this->notificationRepository->paginateByUser($userId, $filters, $perPage);
    }

    public function markAsRead(int $id): Notification
    {
        return $this->notificationRepository->markAsRead($id);
    }

    public function delete(int $id): bool
    {
        return $this->notificationRepository->delete($id);
    }

    public function unreadCount(int $userId): int
    {
        return $this->notificationRepository->unreadCount($userId);
    }
}
