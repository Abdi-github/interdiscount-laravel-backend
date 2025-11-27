<?php

namespace App\Domain\Auth\Services;

use App\Domain\Auth\DTOs\LoginData;
use App\Domain\Auth\DTOs\RegisterData;
use App\Domain\Auth\DTOs\TokenData;
use App\Domain\Auth\Events\PasswordChanged;
use App\Domain\Auth\Events\PasswordResetRequested;
use App\Domain\Auth\Events\UserEmailVerified;
use App\Domain\Auth\Events\UserLoggedIn;
use App\Domain\Auth\Events\UserRegistered;
use App\Domain\User\Models\User;
use App\Domain\User\Repositories\UserRepositoryInterface;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AuthService
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
    ) {}

    public function register(RegisterData $data): array
    {
        $verificationToken = Str::random(64);

        $user = $this->userRepository->create([
            'email' => $data->email,
            'password' => Hash::make($data->password),
            'first_name' => $data->first_name,
            'last_name' => $data->last_name,
            'phone' => $data->phone,
            'preferred_language' => $data->preferred_language,
            'is_active' => true,
            'is_verified' => false,
            'verification_token' => $verificationToken,
            'verification_token_expires' => now()->addHours(24),
        ]);

        event(new UserRegistered($user));

        $tokens = $this->createTokenPair($user);

        return [
            'user' => $user,
            'tokens' => $tokens,
        ];
    }

    public function login(LoginData $data): array
    {
        $user = $this->userRepository->findByEmail($data->email);

        if (!$user || !Hash::check($data->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        if (!$user->is_active) {
            throw ValidationException::withMessages([
                'email' => ['Your account has been deactivated.'],
            ]);
        }

        $this->userRepository->update($user->id, [
            'last_login_at' => now(),
        ]);

        event(new UserLoggedIn($user));

        $tokens = $this->createTokenPair($user);

        return [
            'user' => $user->fresh(),
            'tokens' => $tokens,
        ];
    }

    public function refresh(string $refreshToken): TokenData
    {
        [$id, $token] = explode('|', $refreshToken, 2);

        $accessToken = \Laravel\Sanctum\PersonalAccessToken::findToken($refreshToken);

        if (!$accessToken || $accessToken->name !== 'refresh_token') {
            throw ValidationException::withMessages([
                'refresh_token' => ['Invalid refresh token.'],
            ]);
        }

        if ($accessToken->expires_at && $accessToken->expires_at->isPast()) {
            $accessToken->delete();
            throw ValidationException::withMessages([
                'refresh_token' => ['Refresh token has expired.'],
            ]);
        }

        /** @var User $user */
        $user = $accessToken->tokenable;

        // Delete old tokens
        $user->tokens()->where('name', 'access_token')->delete();
        $accessToken->delete();

        return $this->createTokenPair($user);
    }

    public function verifyEmail(string $token): User
    {
        $user = $this->userRepository->findByVerificationToken($token);

        if (!$user) {
            throw ValidationException::withMessages([
                'token' => ['Invalid verification token.'],
            ]);
        }

        if ($user->verification_token_expires && $user->verification_token_expires->isPast()) {
            throw ValidationException::withMessages([
                'token' => ['Verification token has expired.'],
            ]);
        }

        $this->userRepository->update($user->id, [
            'is_verified' => true,
            'verified_at' => now(),
            'verification_token' => null,
            'verification_token_expires' => null,
        ]);

        $user = $user->fresh();

        event(new UserEmailVerified($user));

        return $user;
    }

    public function forgotPassword(string $email): void
    {
        $user = $this->userRepository->findByEmail($email);

        if (!$user) {
            // Don't reveal whether email exists
            return;
        }

        $resetToken = Str::random(64);

        $this->userRepository->update($user->id, [
            'reset_password_token' => $resetToken,
            'reset_password_expires' => now()->addHour(),
        ]);

        event(new PasswordResetRequested($user->fresh(), $resetToken));
    }

    public function resetPassword(string $token, string $password): void
    {
        $user = $this->userRepository->findByResetToken($token);

        if (!$user) {
            throw ValidationException::withMessages([
                'token' => ['Invalid reset token.'],
            ]);
        }

        if ($user->reset_password_expires && $user->reset_password_expires->isPast()) {
            throw ValidationException::withMessages([
                'token' => ['Reset token has expired.'],
            ]);
        }

        $this->userRepository->update($user->id, [
            'password' => Hash::make($password),
            'reset_password_token' => null,
            'reset_password_expires' => null,
        ]);

        // Revoke all tokens on password reset
        $user->tokens()->delete();

        event(new PasswordChanged($user->fresh()));
    }

    public function resendVerification(string $email): void
    {
        $user = $this->userRepository->findByEmail($email);

        if (!$user || $user->is_verified) {
            // Don't reveal email existence or verification status
            return;
        }

        $verificationToken = Str::random(64);

        $this->userRepository->update($user->id, [
            'verification_token' => $verificationToken,
            'verification_token_expires' => now()->addHours(24),
        ]);

        event(new UserRegistered($user->fresh()));
    }

    public function changePassword(User $user, string $currentPassword, string $newPassword): void
    {
        if (!Hash::check($currentPassword, $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => ['The current password is incorrect.'],
            ]);
        }

        $this->userRepository->update($user->id, [
            'password' => Hash::make($newPassword),
        ]);

        event(new PasswordChanged($user->fresh()));
    }

    public function logout(User $user): void
    {
        $user->currentAccessToken()->delete();
    }

    private function createTokenPair(User $user): TokenData
    {
        $accessToken = $user->createToken('access_token', ['*'], now()->addMinutes(15));
        $refreshToken = $user->createToken('refresh_token', ['refresh'], now()->addDays(7));

        return new TokenData(
            access_token: $accessToken->plainTextToken,
            refresh_token: $refreshToken->plainTextToken,
        );
    }
}
