<?php

namespace Tests\Feature;

use App\Domain\Admin\Models\Admin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PasswordConfirmationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->markTestSkipped('Admin panel Inertia views not yet built — covered by Playwright E2E in Phase 22.');
    }

    public function test_confirm_password_screen_can_be_rendered(): void
    {
        $admin = Admin::factory()->create();

        $response = $this->actingAs($admin, 'web')->get('/user/confirm-password');

        $response->assertStatus(200);
    }

    public function test_password_can_be_confirmed(): void
    {
        $admin = Admin::factory()->create();

        $response = $this->actingAs($admin, 'web')->post('/user/confirm-password', [
            'password' => 'Password123!',
        ]);

        $response->assertRedirect();
        $response->assertSessionHasNoErrors();
    }

    public function test_password_is_not_confirmed_with_invalid_password(): void
    {
        $admin = Admin::factory()->create();

        $response = $this->actingAs($admin, 'web')->post('/user/confirm-password', [
            'password' => 'wrong-password',
        ]);

        $response->assertSessionHasErrors();
    }
}
