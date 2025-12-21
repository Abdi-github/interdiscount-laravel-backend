<?php

namespace Tests\Feature;

use App\Domain\Admin\Models\Admin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Jetstream\Features;
use Tests\TestCase;

class DeleteAccountTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->markTestSkipped('Admin panel Inertia views not yet built — covered by Playwright E2E in Phase 22.');
    }

    public function test_user_accounts_can_be_deleted(): void
    {
        if (! Features::hasAccountDeletionFeatures()) {
            $this->markTestSkipped('Account deletion is not enabled.');
        }

        $this->actingAs($admin = Admin::factory()->create(), 'web');

        $this->delete('/user', [
            'password' => 'Password123!',
        ]);

        $this->assertNull($admin->fresh());
    }

    public function test_correct_password_must_be_provided_before_account_can_be_deleted(): void
    {
        if (! Features::hasAccountDeletionFeatures()) {
            $this->markTestSkipped('Account deletion is not enabled.');
        }

        $this->actingAs($admin = Admin::factory()->create(), 'web');

        $this->delete('/user', [
            'password' => 'wrong-password',
        ]);

        $this->assertNotNull($admin->fresh());
    }
}
