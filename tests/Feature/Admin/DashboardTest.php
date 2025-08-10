<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    private User $adminUser;
    private User $residentUser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);

        $this->adminUser = User::factory()->create();
        $this->adminUser->assignRole('admin');

        $this->residentUser = User::factory()->create();
        $this->residentUser->assignRole('resident');
    }

    public function test_an_admin_can_view_the_admin_dashboard(): void
    {
        $response = $this->actingAs($this->adminUser)->get(route('admin.dashboard'));

        $response->assertStatus(200);

        $response->assertSee('<h1 class="h3 mb-4 text-gray-800">Dashboard</h1>', false);
        $response->assertSee('Total Laporan');
    }

    public function test_a_non_admin_cannot_view_the_admin_dashboard(): void
    {
        $response = $this->actingAs($this->residentUser)->get(route('admin.dashboard'));

        $response->assertStatus(403);
    }
}