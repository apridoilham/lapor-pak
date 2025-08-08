<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    private User $residentUser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);

        $this->residentUser = User::factory()->create();
        $this->residentUser->assignRole('resident');
    }

    public function test_a_user_can_login_with_correct_credentials(): void
    {
        $response = $this->post(route('login.store'), [
            'email' => $this->residentUser->email,
            'password' => 'password',
        ]);

        $response->assertRedirect(route('home'));
        $this->assertAuthenticated();
    }

    public function test_a_user_cannot_login_with_incorrect_credentials(): void
    {
        $response = $this->post(route('login.store'), [
            'email' => $this->residentUser->email,
            'password' => 'password-salah',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }
}