<?php

namespace Tests\Feature\Report;

use App\Models\Report;
use App\Models\ReportCategory;
use App\Models\Resident;
use App\Models\User;
use Database\Seeders\AdminSeeder;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UpdateReportTest extends TestCase
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
        Resident::factory()->for($residentUser)->create();

        $category = ReportCategory::factory()->create();

        $this->report = Report::factory()->create([
            'resident_id' => $residentUser->resident->id,
            'report_category_id' => $category->id,
        ]);
    }

    public function test_an_admin_can_update_a_report(): void
    {
        $updatedData = [
            'title' => 'Judul Laporan Sudah Diperbarui oleh Admin',
            'description' => 'Deskripsi ini juga sudah diperbarui oleh Admin.',
            'resident_id' => $this->report->resident_id,
            'report_category_id' => $this->report->report_category_id,
            'latitude' => $this->report->latitude,
            'longitude' => $this->report->longitude,
            'address' => $this->report->address,
        ];

        $response = $this
            ->actingAs($this->superAdminUser)
            ->put(route('admin.report.update', $this->report->id), $updatedData);

        $response->assertRedirect(route('admin.report.index'));
        $this->assertDatabaseHas('reports', [
            'id' => $this->report->id,
            'title' => 'Judul Laporan Sudah Diperbarui oleh Admin',
        ]);
    }

    public function test_a_non_admin_user_cannot_update_a_report(): void
    {
        $otherUser = User::factory()->create();
        $otherUser->assignRole('resident');

        $updatedData = [
            'title' => 'Mencoba Mengubah Judul Orang Lain',
        ];

        $response = $this
            ->actingAs($otherUser)
            ->put(route('admin.report.update', $this->report->id), $updatedData);

        $response->assertStatus(403);
    }
}