<?php

namespace App\Repositories;

use App\Interfaces\ResidentRepositoryInterface;
use App\Models\Resident;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage; // <-- DITAMBAHKAN

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

    public function createResident(array $data)
    {
        return DB::transaction(function () use ($data) {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => $data['password'],
            ]);

            $user->assignRole('resident');

            return $user->resident()->create($data);
        });
    }

    /**
     * PERUBAHAN TOTAL DI SINI:
     * Logika update dibuat lebih robust untuk menangani pembaruan parsial
     * dan sekaligus menghapus avatar lama.
     */
    public function updateResident(array $data, int $id)
    {
        return DB::transaction(function () use ($data, $id) {
            $resident = $this->getResidentById($id);

            // 1. Menyiapkan dan memperbarui data untuk tabel 'users'
            $userData = [];
            if (isset($data['name'])) {
                $userData['name'] = $data['name'];
            }
            if (isset($data['email'])) {
                $userData['email'] = $data['email'];
            }
            if (!empty($data['password'])) {
                $userData['password'] = $data['password'];
            }

            if (!empty($userData)) {
                $resident->user->update($userData);
            }

            // 2. Menyiapkan dan memperbarui data untuk tabel 'residents' (avatar)
            if (isset($data['avatar'])) {
                // Hapus avatar lama jika ada dan bukan file default
                if ($resident->avatar && Storage::disk('public')->exists($resident->avatar)) {
                    Storage::disk('public')->delete($resident->avatar);
                }

                // Update dengan path avatar yang baru
                $resident->update(['avatar' => $data['avatar']]);
            }

            return $resident;
        });
    }

    public function deleteResident(int $id)
    {
        return DB::transaction(function () use ($id) {
            $resident = $this->getResidentById($id);

            if ($resident) {
                $resident->user()->delete();
                return $resident->delete();
            }

            return false;
        });
    }
}