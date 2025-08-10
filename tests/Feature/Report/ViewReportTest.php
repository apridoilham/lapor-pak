<?php

namespace Tests\Feature\Report;

use App\Models\Report;
use App\Models\ReportCategory;
use App\Models\Resident;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ViewReportTest extends TestCase
{
    use RefreshDatabase;

    private User $residentUser;
    private User $otherUser;
    private Report $userReport;
    private ReportCategory $category;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);

        $this->residentUser = User::factory()->create();
        $this->residentUser->assignRole('resident');
        Resident::factory()->for($this->residentUser)->create();

        $this->otherUser = User::factory()->create();
        $this->otherUser->assignRole('resident');
        Resident::factory()->for($this->otherUser)->create();

        $this->category = ReportCategory::factory()->create();

        $this->userReport = Report::factory()->create([
            'resident_id' => $this->residentUser->resident->id,
            'report_category_id' => $this->category->id,
        ]);

        $this->userReport->reportStatuses()->create([
            'status' => 'delivered',
            'description' => 'Laporan dibuat untuk testing.'
        ]);
    }

    public function test_an_authenticated_user_can_view_their_own_reports_list(): void
    {
        $response = $this->actingAs($this->residentUser)->get(route('report.myreport'));

        $response->assertStatus(200);
        $response->assertSeeText($this->userReport->title);
    }

    public function test_a_user_cannot_view_other_users_reports_in_their_list(): void
    {
        $otherReport = Report::factory()->create([
            'resident_id' => $this->otherUser->resident->id,
            'report_category_id' => $this->category->id,
        ]);

        $response = $this->actingAs($this->residentUser)->get(route('report.myreport'));

        $response->assertStatus(200);
        $response->assertDontSeeText($otherReport->title);
    }

    public function test_an_authenticated_user_can_view_a_single_report_detail(): void
    {
        $response = $this->actingAs($this->residentUser)->get(route('report.show', $this->userReport->code));

        $response->assertStatus(200);
        $response->assertSeeText($this->userReport->title);
        $response->assertSeeText($this->userReport->description);
    }
}