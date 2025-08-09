<?php

namespace App\Repositories;

use App\Interfaces\AdminRepositoryInterface;
use App\Models\User;
use Illuminate\Support\Facades\Auth; // <-- DITAMBAHKAN
use Illuminate\Support\Facades\DB;

class AdminRepository implements AdminRepositoryInterface
{
    public function getAllAdmins()
    {
        // PERUBAHAN DI SINI:
        // Ambil semua user dengan peran 'admin' ATAU 'super-admin',
        // KECUALI user yang sedang login saat ini.
        return User::role(['admin', 'super-admin'])
            ->where('id', '!=', Auth::id())
            ->get();
    }

    public function getAdminById(int $id)
    {
        return User::role(['admin', 'super-admin'])->findOrFail($id);
    }

    public function createAdmin(array $data): User
    {
        return DB::transaction(function () use ($data) {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => $data['password'], // Model akan hash otomatis
            ]);

            // Saat membuat user baru dari form ini, berikan peran 'admin' biasa
            $user->assignRole('admin');

            return $user;
        });
    }

    public function updateAdmin(array $data, int $id): bool
    {
        $user = $this->getAdminById($id);

        $userData = [
            'name' => $data['name'],
            'email' => $data['email'],
        ];

        if (!empty($data['password'])) {
            $userData['password'] = $data['password'];
        }

        return $user->update($userData);
    }

    public function deleteAdmin(int $id): bool
    {
        $user = $this->getAdminById($id);
        return $user->delete();
    }
}