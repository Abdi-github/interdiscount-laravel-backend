<?php

namespace App\Http\Controllers\Api\Customer;

use App\Domain\User\Services\UserService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ChangePasswordRequest;
use App\Http\Requests\Profile\UpdateProfileRequest;
use App\Http\Resources\UserResource;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    use ApiResponse;

    public function __construct(
        private UserService $userService,
    ) {}

    public function show(Request $request): JsonResponse
    {
        return $this->success(
            new UserResource($request->user()),
            'Profile retrieved successfully',
        );
    }

    public function update(UpdateProfileRequest $request): JsonResponse
    {
        $user = $this->userService->updateProfile(
            $request->user()->id,
            $request->validated(),
        );

        return $this->success(
            new UserResource($user),
            'Profile updated successfully',
        );
    }

    public function changePassword(ChangePasswordRequest $request): JsonResponse
    {
        $changed = $this->userService->changePassword(
            $request->user(),
            $request->validated('current_password'),
            $request->validated('password'),
        );

        if (!$changed) {
            return $this->error('Current password is incorrect', 422);
        }

        return $this->success(null, 'Password changed successfully');
    }
}
