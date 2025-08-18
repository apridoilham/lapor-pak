<?php

namespace Tests\Feature\Report;

use App\Enums\ReportStatusEnum;
use App\Models\Report;
use App\Models\ReportCategory;
use App\Models\Resident;
use App\Models\User;
use Database\Seeders\AdminSeeder;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class UpdateReportTest extends TestCase
{
    use RefreshDatabase;

    private User $superAdminUser;
    private User $residentUser; // PROPERTI BARU
    private Report $report;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
        $this->seed(AdminSeeder::class);

        $this->superAdminUser = User::where('email', 'bsblapor@gmail.com')->first();

        $this->residentUser = User::factory()->create();
        $this->residentUser->assignRole('resident');
        Resident::factory()->for($this->residentUser)->create();

        $category = ReportCategory::factory()->create();

        $this->report = Report::factory()->create([
            'resident_id' => $this->residentUser->resident->id,
            'report_category_id' => $category->id,
        ]);

        // [PERBAIKAN] Tambahkan status awal 'delivered' untuk laporan
        // agar bisa lolos dari policy otorisasi saat update.
        $this->report->reportStatuses()->create([
            'status' => ReportStatusEnum::DELIVERED,
            'description' => 'Laporan dibuat untuk pengujian.',
            'created_by_role' => 'resident',
        ]);
    }

    /**
     * Memastikan warga (resident) dapat mengubah laporannya sendiri.
     */
    #[Test] // UBAH @test di komentar menjadi attribute seperti ini
    public function test_a_resident_can_update_their_own_report(): void
    {
        $newCategory = ReportCategory::factory()->create();
        $updatedData = [
            'title' => 'Judul Laporan Diubah oleh Pemilik',
            'description' => 'Deskripsi laporan ini telah berhasil diubah.',
            'report_category_id' => $newCategory->id,
            'visibility' => 'private',
        ];

        $response = $this
            ->actingAs($this->residentUser)
            ->put(route('report.update', $this->report->id), $updatedData);

        $response->assertRedirect(route('report.myreport'));

        $this->assertDatabaseHas('reports', [
            'id' => $this->report->id,
            'title' => 'Judul Laporan Diubah oleh Pemilik',
            'report_category_id' => $newCategory->id,
            'visibility' => 'private',
        ]);
    }

    /**
     * @test
     * Memastikan seorang warga tidak dapat mengubah laporan milik warga lain.
     */
    public function test_a_user_cannot_update_other_users_report(): void
    {
        // Buat user lain
        $otherUser = User::factory()->create();
        $otherUser->assignRole('resident');
        Resident::factory()->for($otherUser)->create();

        $updatedData = [
            'title' => 'Mencoba Mengubah Judul Orang Lain',
            'description' => 'Deskripsi percobaan.',
            'report_category_id' => $this->report->report_category_id,
            'visibility' => 'public',
        ];

        // Lakukan request sebagai user lain
        $response = $this
            ->actingAs($otherUser)
            ->put(route('report.update', $this->report->id), $updatedData);

        // Harusnya gagal karena policy (Authorization Exception)
        $response->assertStatus(403);

        // Pastikan judul laporan tidak berubah di database
        $this->assertDatabaseMissing('reports', [
            'id' => $this->report->id,
            'title' => 'Mencoba Mengubah Judul Orang Lain',
        ]);
    }

    /**
     * @test
     * Memastikan admin dapat mengubah laporan.
     */
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

    /**
     * @test
     * Memastikan pengguna non-admin (warga) tidak bisa mengakses route update admin.
     */
    public function test_a_non_admin_user_cannot_update_a_report_via_admin_route(): void
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