<?php

namespace Tests\Feature;

use App\Domain\Admin\Models\Admin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Fortify\Features;
use Tests\TestCase;

class TwoFactorAuthenticationSettingsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->markTestSkipped('Admin panel Inertia views not yet built — covered by Playwright E2E in Phase 22.');
    }

    public function test_two_factor_authentication_can_be_enabled(): void
    {
        if (! Features::canManageTwoFactorAuthentication()) {
            $this->markTestSkipped('Two factor authentication is not enabled.');
        }

        $this->actingAs($admin = Admin::factory()->create(), 'web');

        $this->withSession(['auth.password_confirmed_at' => time()]);

        $this->post('/user/two-factor-authentication');

        $this->assertNotNull($admin->fresh()->two_factor_secret);
        $this->assertCount(8, $admin->fresh()->recoveryCodes());
    }

    public function test_recovery_codes_can_be_regenerated(): void
    {
        if (! Features::canManageTwoFactorAuthentication()) {
            $this->markTestSkipped('Two factor authentication is not enabled.');
        }

        $this->actingAs($admin = Admin::factory()->create(), 'web');

        $this->withSession(['auth.password_confirmed_at' => time()]);

        $this->post('/user/two-factor-authentication');
        $this->post('/user/two-factor-recovery-codes');

        $admin = $admin->fresh();

        $this->post('/user/two-factor-recovery-codes');

        $this->assertCount(8, $admin->recoveryCodes());
        $this->assertCount(8, array_diff($admin->recoveryCodes(), $admin->fresh()->recoveryCodes()));
    }

    public function test_two_factor_authentication_can_be_disabled(): void
    {
        if (! Features::canManageTwoFactorAuthentication()) {
            $this->markTestSkipped('Two factor authentication is not enabled.');
        }

        $this->actingAs($admin = Admin::factory()->create(), 'web');

        $this->withSession(['auth.password_confirmed_at' => time()]);

        $this->post('/user/two-factor-authentication');

        $this->assertNotNull($admin->fresh()->two_factor_secret);

        $this->delete('/user/two-factor-authentication');

        $this->assertNull($admin->fresh()->two_factor_secret);
    }
}
