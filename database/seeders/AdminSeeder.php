<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create or update super-admin
        $superAdmin = User::updateOrCreate(
            ['email' => 'bsblapor@gmail.com'],
            [
                'name' => 'Super Admin Haeritage 31',
                'google_id' => null, // Akan diisi saat login dengan Google
                'password' => Hash::make('password'), // Password backup jika perlu
            ]
        );
        
        // Assign role super-admin
        if (!$superAdmin->hasRole('super-admin')) {
            $superAdmin->assignRole('super-admin');
        }
    }
}