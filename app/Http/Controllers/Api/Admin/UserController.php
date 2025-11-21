<?php

namespace App\Http\Controllers\Api\Admin;

use App\Domain\User\Services\UserService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateAdminUserRequest;
use App\Http\Resources\UserResource;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    use ApiResponse;

    public function __construct(
        private UserService $userService,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $users = $this->userService->paginate(
            $request->only(['search', 'is_active', 'is_verified']),
            $request->integer('limit', 20),
        );

        /* Log::debug('Users list retrieved - count: ' . count($users->items())); */
        return $this->paginated($users, 'UserResource', 'Users retrieved successfully');
    }

    public function show(int $id): JsonResponse
    {
        $user = $this->userService->findById($id);

        if (!$user) {
            return $this->notFound('User not found');
        }

        // TODO: Load user activity history for admin review
        $user->load('addresses');
        /* Log::debug('User loaded with addresses - count: ' . count($user->addresses)); */

        return $this->success(new UserResource($user), 'User retrieved successfully');
    }

    public function update(UpdateAdminUserRequest $request, int $id): JsonResponse
    {
        $user = $this->userService->findById($id);

        if (!$user) {
            return $this->notFound('User not found');
        }

        $user = $this->userService->update($id, $request->validated());

        return $this->success(new UserResource($user), 'User updated successfully');
    }

    public function toggleStatus(int $id): JsonResponse
    {
        $user = $this->userService->findById($id);

        if (!$user) {
            return $this->notFound('User not found');
        }

        $newStatus = !$user->is_active;
        $user = $this->userService->update($id, ['is_active' => $newStatus]);

        return $this->success(new UserResource($user), 'User status updated successfully');
    }
}
