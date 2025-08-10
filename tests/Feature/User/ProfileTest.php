<?php

namespace Tests\Feature\User;

use App\Models\Report;
use App\Models\Resident;
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
        Resident::factory()->for($this->residentUser)->create();
    }

    public function test_an_authenticated_user_can_view_their_profile_page_with_correct_stats(): void
    {
        $activeReport = Report::factory()->create(['resident_id' => $this->residentUser->resident->id]);
        $activeReport->reportStatuses()->create([
            'status' => 'in_process',
            'description' => 'Sedang diproses',
        ]);

        $completedReport = Report::factory()->create(['resident_id' => $this->residentUser->resident->id]);
        $completedReport->reportStatuses()->create([
            'status' => 'completed',
            'description' => 'Telah selesai',
        ]);

        $rejectedReport = Report::factory()->create(['resident_id' => $this->residentUser->resident->id]);
        $rejectedReport->reportStatuses()->create([
            'status' => 'rejected',
            'description' => 'Ditolak',
        ]);

        $response = $this->actingAs($this->residentUser)->get(route('profile'));

        $response->assertStatus(200);
        $response->assertSeeText($this->residentUser->name);
        $response->assertSeeText($this->residentUser->email);

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