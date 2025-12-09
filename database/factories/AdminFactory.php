<?php

namespace Database\Factories;

use App\Domain\Admin\Enums\AdminType;
use App\Domain\Admin\Models\Admin;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Domain\Admin\Models\Admin>
 */
class AdminFactory extends Factory
{
    protected $model = Admin::class;

    protected static ?string $password;

    public function definition(): array
    {
        return [
            'email' => fake()->unique()->safeEmail(),
            'password' => static::$password ??= Hash::make('Password123!'),
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'phone' => fake()->phoneNumber(),
            'admin_type' => AdminType::STORE_STAFF,
            'is_active' => true,
            'remember_token' => Str::random(10),
        ];
    }

    public function superAdmin(): static
    {
        return $this->state(fn () => [
            'admin_type' => AdminType::SUPER_ADMIN,
        ]);
    }

    public function platformAdmin(): static
    {
        return $this->state(fn () => [
            'admin_type' => AdminType::PLATFORM_ADMIN,
        ]);
    }

    public function storeManager(): static
    {
        return $this->state(fn () => [
            'admin_type' => AdminType::STORE_MANAGER,
        ]);
    }

    public function supportAgent(): static
    {
        return $this->state(fn () => [
            'admin_type' => AdminType::SUPPORT_AGENT,
        ]);
    }
}
