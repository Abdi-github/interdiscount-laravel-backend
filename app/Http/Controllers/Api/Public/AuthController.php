<?php

namespace App\Http\Controllers\Api\Public;

use App\Domain\Auth\DTOs\LoginData;
use App\Domain\Auth\DTOs\RegisterData;
use App\Domain\Auth\Services\AuthService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Http\Resources\UserResource;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    use ApiResponse;

    public function __construct(
        private AuthService $authService,
    ) {}

    public function register(RegisterRequest $request): JsonResponse
    {
        $data = RegisterData::from($request->validated());
        $result = $this->authService->register($data);

        return $this->created([
            'user' => new UserResource($result['user']),
            'tokens' => $result['tokens']->toArray(),
        ], 'Registration successful. Please verify your email.');
    }

    public function login(LoginRequest $request): JsonResponse
    {
        try {
            $data = LoginData::from($request->validated());
            $result = $this->authService->login($data);

            return $this->success([
                'user' => new UserResource($result['user']),
                'tokens' => $result['tokens']->toArray(),
            ], 'Login successful.');
        } catch (ValidationException $e) {
            return $this->error(
                $e->getMessage(),
                401,
                'INVALID_CREDENTIALS',
                $e->errors(),
            );
        }
    }

    public function refresh(Request $request): JsonResponse
    {
        $request->validate([
            'refresh_token' => ['required', 'string'],
        ]);

        try {
            $tokens = $this->authService->refresh($request->input('refresh_token'));

            return $this->success([
                'tokens' => $tokens->toArray(),
            ], 'Token refreshed successfully.');
        } catch (ValidationException $e) {
            return $this->error(
                $e->getMessage(),
                401,
                'INVALID_TOKEN',
                $e->errors(),
            );
        }
    }

    public function verifyEmail(string $token): JsonResponse
    {
        try {
            $user = $this->authService->verifyEmail($token);

            return $this->success(
                new UserResource($user),
                'Email verified successfully.',
            );
        } catch (ValidationException $e) {
            return $this->error(
                $e->getMessage(),
                400,
                'VERIFICATION_FAILED',
                $e->errors(),
            );
        }
    }

    public function forgotPassword(ForgotPasswordRequest $request): JsonResponse
    {
        $this->authService->forgotPassword($request->input('email'));

        return $this->success(
            null,
            'If an account with that email exists, a password reset link has been sent.',
        );
    }

    public function resetPassword(ResetPasswordRequest $request): JsonResponse
    {
        try {
            $this->authService->resetPassword(
                $request->input('token'),
                $request->input('password'),
            );

            return $this->success(null, 'Password reset successfully.');
        } catch (ValidationException $e) {
            return $this->error(
                $e->getMessage(),
                400,
                'RESET_FAILED',
                $e->errors(),
            );
        }
    }

    public function resendVerification(Request $request): JsonResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $this->authService->resendVerification($request->input('email'));

        return $this->success(
            null,
            'If your email is registered and unverified, a new verification link has been sent.',
        );
    }

    public function logout(Request $request): JsonResponse
    {
        $this->authService->logout($request->user());

        return $this->success(null, 'Logged out successfully.');
    }
}
