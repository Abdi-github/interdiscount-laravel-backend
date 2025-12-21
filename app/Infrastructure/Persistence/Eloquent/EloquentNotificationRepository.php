<?php

namespace App\Infrastructure\Persistence\Eloquent;

use App\Domain\Notification\Models\Notification;
use App\Domain\Notification\Repositories\NotificationRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class EloquentNotificationRepository implements NotificationRepositoryInterface
{
    public function __construct(private Notification $model) {}

    public function findById(int $id): ?Notification
    {
        return $this->model->find($id);
    }

    public function paginateByUser(int $userId, array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        $query = $this->model->where('user_id', $userId);

        if (isset($filters['is_read'])) {
            $query->where('is_read', $filters['is_read']);
        }

        if (!empty($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    public function create(array $data): Notification
    {
        return $this->model->create($data);
    }

    public function markAsRead(int $id): Notification
    {
        $notification = $this->model->findOrFail($id);
        $notification->update([
            'is_read' => true,
            'read_at' => now(),
        ]);
        return $notification->fresh();
    }

    public function delete(int $id): bool
    {
        return $this->model->findOrFail($id)->delete();
    }

    public function unreadCount(int $userId): int
    {
        return $this->model->where('user_id', $userId)->unread()->count();
    }
}
