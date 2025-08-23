<?php

namespace Tests\Feature\Report;

use App\Models\Report;
use App\Models\ReportCategory;
use App\Models\Resident;
use App\Models\User;
use Database\Seeders\AdminSeeder;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class DeleteReportTest extends TestCase
{
    use RefreshDatabase;

    private User $superAdminUser;
    private Report $report;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
        $this->seed(AdminSeeder::class);

        $this->superAdminUser = User::where('email', 'bsblapor@gmail.com')->first();

        $residentUser = User::factory()->create();
        $residentUser->assignRole('resident');
        $resident = Resident::factory()->for($residentUser)->create();
        $category = ReportCategory::factory()->create();

        $this->report = Report::factory()->create([
            'resident_id' => $resident->id,
            'report_category_id' => $category->id,
        ]);
    }

    #[Test]
    public function an_admin_can_delete_a_report(): void
    {
        $this->assertDatabaseHas('reports', ['id' => $this->report->id]);

        $response = $this
            ->actingAs($this->superAdminUser)
            ->delete(route('admin.report.destroy', $this->report->id));

        $response->assertRedirect(route('admin.report.index'));
        $this->assertDatabaseMissing('reports', ['id' => $this->report->id]);
    }

    #[Test]
    public function a_non_admin_user_cannot_delete_a_report(): void
    {
        $residentUser = User::factory()->create();
        $residentUser->assignRole('resident');

        $response = $this
            ->actingAs($residentUser)
            ->delete(route('admin.report.destroy', $this->report->id));

        $response->assertStatus(403);
        $this->assertDatabaseHas('reports', ['id' => $this->report->id]);
    }
}