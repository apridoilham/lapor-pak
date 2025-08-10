<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_application_returns_a_successful_response(): void
    {
        $this->seed(RolePermissionSeeder::class);
        
        $user = User::factory()->create();
        $user->assignRole('resident');
        $user->resident()->create([
            'avatar' => 'fake-avatar.jpg',
            'rt_id' => null,
            'rw_id' => null,
            'address' => 'Alamat tidak diisi',
        ]);
        
        $response = $this->actingAs($user)->get('/');

        $response->assertStatus(200);
    }
}