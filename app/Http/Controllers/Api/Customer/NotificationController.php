<?php

namespace App\Http\Controllers\Api\Customer;

use App\Domain\Notification\Repositories\NotificationRepositoryInterface;
use App\Domain\Notification\Services\NotificationService;
use App\Http\Controllers\Controller;
use App\Http\Resources\NotificationResource;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    use ApiResponse;

    public function __construct(
        private NotificationService $notificationService,
        private NotificationRepositoryInterface $notificationRepository,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $perPage = (int) $request->input('limit', 20);
        $filters = $request->only(['is_read', 'type']);

        $notifications = $this->notificationService->paginateByUser(
            $request->user()->id,
            $filters,
            $perPage,
        );

        return $this->paginated(
            $notifications,
            'NotificationResource',
            'Notifications retrieved successfully',
        );
    }

    public function markAsRead(Request $request, int $id): JsonResponse
    {
        $notification = $this->notificationRepository->findById($id);

        if (!$notification || $notification->user_id !== $request->user()->id) {
            return $this->notFound('Notification not found');
        }

        $updated = $this->notificationService->markAsRead($id);

        return $this->success(
            new NotificationResource($updated),
            'Notification marked as read',
        );
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        $notification = $this->notificationRepository->findById($id);

        if (!$notification || $notification->user_id !== $request->user()->id) {
            return $this->notFound('Notification not found');
        }

        $this->notificationService->delete($id);

        return $this->success(null, 'Notification deleted successfully');
    }
}
