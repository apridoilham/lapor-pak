<?php

namespace Tests\Feature;

use App\Models\User; // <-- DITAMBAHKAN
use Illuminate\Foundation\Testing\RefreshDatabase; // <-- DITAMBAHKAN (jika belum ada)
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase; // <-- DITAMBAHKAN (jika belum ada)

    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        // 1. Buat user baru untuk tes
        $user = User::factory()->create();

        // 2. Lakukan request sebagai user yang sudah login dan akses halaman utama
        $response = $this->actingAs($user)->get('/');

        // 3. Sekarang tes akan mengharapkan status 200 dan berhasil
        $response->assertStatus(200);
    }
}