<?php

namespace Tests\Feature\Report;

use App\Enums\ReportVisibilityEnum;
use App\Models\Report; // Pastikan ini di-import
use App\Models\ReportCategory;
use App\Models\Resident;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\Test;
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

    #[Test]
    public function an_authenticated_user_can_create_a_report(): void
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
            'visibility' => ReportVisibilityEnum::PUBLIC->value,
        ];

        $response = $this->actingAs($this->residentUser)->post(route('report.store'), $reportData);

        // --- BAGIAN INI DIPERBAIKI ---
        // 1. Ambil laporan yang baru saja dibuat
        $report = Report::latest('id')->first();

        // 2. Pastikan redirect mengarah ke halaman summary dari laporan tersebut
        $response->assertRedirect(route('report.summary', $report));
        // --- AKHIR PERBAIKAN ---

        $this->assertDatabaseHas('reports', [
            'title' => 'Jalan Berlubang di Depan Rumah',
            'resident_id' => $this->residentUser->resident->id,
        ]);

        $this->assertDatabaseHas('report_statuses', [
            'report_id' => $report->id, // Gunakan ID dari laporan yang baru dibuat
            'status' => 'delivered',
        ]);

        $this->assertCount(1, Storage::disk('public')->files('assets/report/image'));
    }

    #[Test]
    public function report_creation_fails_if_title_is_missing(): void
    {
        $response = $this->actingAs($this->residentUser)->post(route('report.store'), [
            'title' => '', // Data tidak valid
            'report_category_id' => $this->category->id,
            'description' => 'Deskripsi valid.',
            'image' => UploadedFile::fake()->image('laporan.jpg'),
            'latitude' => '-6.200000',
            'longitude' => '106.816666',
            'address' => 'Jl. Jenderal Sudirman, Jakarta',
            'visibility' => ReportVisibilityEnum::PUBLIC->value,
        ]);

        $response->assertSessionHasErrors('title');
        $this->assertDatabaseMissing('reports', [
            'resident_id' => $this->residentUser->resident->id,
        ]);
    }
}