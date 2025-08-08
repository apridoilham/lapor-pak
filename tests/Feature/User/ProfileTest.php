<?php

namespace Tests\Feature\User;

use App\Models\Report;
use App\Models\ReportStatus;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    private User $residentUser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);

        $this->residentUser = User::factory()->create();
        $this->residentUser->assignRole('resident');
        $this->residentUser->resident()->create(['avatar' => 'avatar.jpg']);
    }

    public function test_an_authenticated_user_can_view_their_profile_page_with_correct_stats(): void
    {
        // 1. Buat data laporan untuk user ini dengan berbagai status
        // Laporan Aktif (in_process)
        $activeReport = Report::factory()->create(['resident_id' => $this->residentUser->resident->id]);
        $activeReport->reportStatuses()->create([
            'status' => 'in_process',
            'description' => 'Sedang diproses',
        ]);

        // Laporan Selesai (completed)
        $completedReport = Report::factory()->create(['resident_id' => $this->residentUser->resident->id]);
        $completedReport->reportStatuses()->create([
            'status' => 'completed',
            'description' => 'Telah selesai',
        ]);

        // Laporan Ditolak (rejected)
        $rejectedReport = Report::factory()->create(['resident_id' => $this->residentUser->resident->id]);
        $rejectedReport->reportStatuses()->create([
            'status' => 'rejected',
            'description' => 'Ditolak',
        ]);

        // 2. Aksi: Akses halaman profil sebagai user ini
        $response = $this->actingAs($this->residentUser)->get(route('profile'));

        // 3. Assert: Pastikan halaman berhasil diakses dan menampilkan data yang benar
        $response->assertStatus(200);
        $response->assertSeeText($this->residentUser->name); // Tampilkan nama user
        $response->assertSeeText($this->residentUser->email); // Tampilkan email user

        // Pastikan statistik yang ditampilkan di view benar
        $response->assertSeeInOrder(['<h5 class="card-title">1</h5>', '<p class="card-text">Aktif</p>']);
        $response->assertSeeInOrder(['<h5 class="card-title">1</h5>', '<p class="card-text">Selesai</p>']);
        $response->assertSeeInOrder(['<h5 class="card-title">1</h5>', '<p class="card-text">Ditolak</p>']);
    }

    public function test_a_guest_cannot_view_the_profile_page(): void
    {
        $response = $this->get(route('profile'));

        $response->assertRedirect(route('login'));
    }
}