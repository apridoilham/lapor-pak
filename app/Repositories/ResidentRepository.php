<?php

namespace App\Repositories;

use App\Interfaces\ResidentRepositoryInterface;
use App\Models\Resident;
use App\Models\User;
use Illuminate\Support\Facades\DB; // <-- DITAMBAHKAN
use Illuminate\Support\Facades\Storage;

class ResidentRepository implements ResidentRepositoryInterface
{
    public function getAllResidents()
    {
        return Resident::all();
    }

    public function getResidentById(int $id)
    {
        return Resident::where('id', $id)->first();
    }

    /**
     * PERUBAHAN DI SINI: Dibungkus dalam transaksi dan bcrypt() dihapus.
     */
    public function createResident(array $data)
    {
        return DB::transaction(function () use ($data) {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => $data['password'], // Model User akan melakukan hash secara otomatis
            ]);

            $user->assignRole('resident');

            return $user->resident()->create($data);
        });
    }

    /**
     * PERUBAHAN DI SINI: Logika update password diperbaiki dan bcrypt() dihapus.
     */
    public function updateResident(array $data, int $id)
    {
        $resident = $this->getResidentById($id);

        $userData = [
            'name' => $data['name'],
        ];

        // Hanya update password jika diisi dan tidak kosong
        if (!empty($data['password'])) {
            $userData['password'] = $data['password'];
        }

        $resident->user->update($userData);

        // Hapus password dari array data agar tidak mencoba mengupdate kolom
        // password di tabel residents.
        unset($data['password']);

        return $resident->update($data);
    }

    /**
     * PERUBAHAN DI SINI: Dibungkus dalam transaksi dan User ikut dihapus.
     */
    public function deleteResident(int $id)
    {
        return DB::transaction(function () use ($id) {
            $resident = $this->getResidentById($id);

            if ($resident) {
                // Lakukan soft delete pada user yang berelasi
                $resident->user()->delete();

                // Lakukan soft delete pada resident
                return $resident->delete();
            }

            return false;
        });
    }
}