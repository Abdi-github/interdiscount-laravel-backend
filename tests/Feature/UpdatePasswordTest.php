<?php

namespace Tests\Feature;

use App\Domain\Admin\Models\Admin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UpdatePasswordTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->markTestSkipped('Admin panel Inertia views not yet built — covered by Playwright E2E in Phase 22.');
    }

    public function test_password_can_be_updated(): void
    {
        $this->actingAs($admin = Admin::factory()->create(), 'web');

        $this->put('/user/password', [
            'current_password' => 'Password123!',
            'password' => 'NewPassword123!',
            'password_confirmation' => 'NewPassword123!',
        ]);

        $this->assertTrue(Hash::check('NewPassword123!', $admin->fresh()->password));
    }

    public function test_current_password_must_be_correct(): void
    {
        $this->actingAs($admin = Admin::factory()->create(), 'web');

        $response = $this->put('/user/password', [
            'current_password' => 'wrong-password',
            'password' => 'NewPassword123!',
            'password_confirmation' => 'NewPassword123!',
        ]);

        $response->assertSessionHasErrors();

        $this->assertTrue(Hash::check('Password123!', $admin->fresh()->password));
    }

    public function test_new_passwords_must_match(): void
    {
        $this->actingAs($admin = Admin::factory()->create(), 'web');

        $response = $this->put('/user/password', [
            'current_password' => 'Password123!',
            'password' => 'NewPassword123!',
            'password_confirmation' => 'wrong-password',
        ]);

        $response->assertSessionHasErrors();

        $this->assertTrue(Hash::check('Password123!', $admin->fresh()->password));
    }
}
