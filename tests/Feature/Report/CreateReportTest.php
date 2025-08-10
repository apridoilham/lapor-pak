<?php

namespace Tests\Feature\Report;

use App\Models\ReportCategory;
use App\Models\Resident;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CreateReportTest extends TestCase
{
    use RefreshDatabase;

    private User $residentUser;
    private ReportCategory $category;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);

        $this->residentUser = User::factory()->create();
        $this->residentUser->assignRole('resident');
        Resident::factory()->for($this->residentUser)->create();

        $this->category = ReportCategory::factory()->create();
    }

    public function test_an_authenticated_user_can_create_a_report(): void
    {
        Storage::fake('public');

        $reportData = [
            'title' => 'Jalan Berlubang di Depan Rumah',
            'report_category_id' => $this->category->id,
            'description' => 'Deskripsi lengkap mengenai jalan berlubang.',
            'image' => UploadedFile::fake()->image('laporan.jpg'),
            'latitude' => '-6.200000',
            'longitude' => '106.816666',
            'address' => 'Jl. Jenderal Sudirman, Jakarta',
        ];

        $response = $this->actingAs($this->residentUser)->post(route('report.store'), $reportData);

        $response->assertRedirect(route('report.success'));

        $this->assertDatabaseHas('reports', [
            'title' => 'Jalan Berlubang di Depan Rumah',
            'resident_id' => $this->residentUser->resident->id,
        ]);

        $this->assertDatabaseHas('report_statuses', [
            'report_id' => 1,
            'status' => 'delivered',
        ]);

        $this->assertCount(1, Storage::disk('public')->files('assets/report/image'));
    }

    public function test_report_creation_fails_if_title_is_missing(): void
    {
        $response = $this->actingAs($this->residentUser)->post(route('report.store'), [
            'title' => '',
            'report_category_id' => $this->category->id,
            'description' => 'Deskripsi valid.',
            'image' => UploadedFile::fake()->image('laporan.jpg'),
            'latitude' => '-6.200000',
            'longitude' => '106.816666',
            'address' => 'Jl. Jenderal Sudirman, Jakarta',
        ]);

        $response->assertSessionHasErrors('title');
        $this->assertDatabaseMissing('reports', [
            'resident_id' => $this->residentUser->resident->id,
        ]);
    }
}