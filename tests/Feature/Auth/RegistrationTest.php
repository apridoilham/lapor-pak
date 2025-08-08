<?php

namespace Tests\Feature\Auth;

use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
    }

    public function test_a_new_user_can_register(): void
    {
        Storage::fake('public');

        $response = $this->post(route('register.store'), [
            'name' => 'Pengguna Baru',
            'email' => 'penggunabaru@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'avatar' => UploadedFile::fake()->image('avatar.jpg'),
        ]);

        $response->assertRedirect(route('login'));

        $this->assertDatabaseHas('users', [
            'email' => 'penggunabaru@example.com',
        ]);

        $this->assertDatabaseHas('residents', [
            'user_id' => 1,
        ]);

        $this->assertCount(1, Storage::disk('public')->files('assets/avatar'));
    }

    public function test_registration_fails_if_password_does_not_match(): void
    {
        $response = $this->post(route('register.store'), [
            'name' => 'Pengguna Gagal',
            'email' => 'penggunagagal@example.com',
            'password' => 'password123',
            'password_confirmation' => 'passwordSALAH',
            'avatar' => UploadedFile::fake()->image('avatar.jpg'),
        ]);

        $response->assertSessionHasErrors('password');
        $this->assertDatabaseMissing('users', [
            'email' => 'penggunagagal@example.com',
        ]);
    }
}