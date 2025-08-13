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
        User::firstOrCreate(
            ['email' => 'bsblapor@gmail.com'],
            [
                'name'      => 'Nama Admin Utama',
                'google_id' => null, // Atur ke null secara default
                'password'  => null, // Atur ke null secara default
            ]
        )->assignRole('super-admin');
    }
}