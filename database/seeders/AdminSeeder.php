<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Membuat user dan memberikannya peran 'super-admin'
        User::create([
            'name' => 'Super Admin Lapor Pak',
            'email' => 'admin@laporpak.com',
            'password' => 'password' // Model akan hash otomatis
        ])->assignRole('super-admin');
    }
}