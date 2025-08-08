<?php

namespace Tests\Feature\Report;

use App\Models\Report;
use App\Models\User;
use Database\Seeders\AdminSeeder;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeleteReportTest extends TestCase
{
    use RefreshDatabase;

    private User $adminUser;
    private Report $report;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
        $this->seed(AdminSeeder::class);

        $this->adminUser = User::where('email', 'admin@laporpak.com')->first();
        $this->report = Report::factory()->create();
    }

    public function test_an_admin_can_delete_a_report(): void
    {
        $this->assertDatabaseHas('reports', ['id' => $this->report->id]);

        $response = $this
            ->actingAs($this->adminUser)
            ->delete(route('admin.report.destroy', $this->report->id));

        $response->assertRedirect(route('admin.report.index'));
        $this->assertSoftDeleted('reports', ['id' => $this->report->id]);
    }

    public function test_a_non_admin_user_cannot_delete_a_report(): void
    {
        $residentUser = User::factory()->create();
        $residentUser->assignRole('resident');

        $response = $this
            ->actingAs($residentUser)
            ->delete(route('admin.report.destroy', $this->report->id));

        $response->assertStatus(403);
        $this->assertNotSoftDeleted('reports', ['id' => $this->report->id]);
    }
}