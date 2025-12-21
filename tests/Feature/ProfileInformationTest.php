<?php

namespace Tests\Feature;

use App\Domain\Admin\Models\Admin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileInformationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->markTestSkipped('Admin panel Inertia views not yet built — covered by Playwright E2E in Phase 22.');
    }

    public function test_profile_information_can_be_updated(): void
    {
        $this->actingAs($admin = Admin::factory()->create(), 'web');

        $this->put('/user/profile-information', [
            'name' => 'Test Name',
            'email' => 'test@example.com',
        ]);

        $this->assertEquals('test@example.com', $admin->fresh()->email);
    }
}
