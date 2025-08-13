<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Hanya jalankan seeder yang esensial untuk aplikasi bisa berjalan
        $this->call([
            RolePermissionSeeder::class,
            AdminSeeder::class,
        ]);
    }
}