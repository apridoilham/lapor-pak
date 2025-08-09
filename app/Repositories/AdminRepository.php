<?php

namespace App\Repositories;

use App\Interfaces\AdminRepositoryInterface;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class AdminRepository implements AdminRepositoryInterface
{
    public function getAllAdmins()
    {
        // Mengambil semua user yang memiliki peran 'admin'
        return User::role('admin')->get();
    }

    public function getAdminById(int $id)
    {
        return User::role('admin')->findOrFail($id);
    }

    public function createAdmin(array $data): User
    {
        return DB::transaction(function () use ($data) {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => $data['password'], // Model akan hash otomatis
            ]);

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
        return $user->delete(); // Ini adalah soft delete karena model User sudah pakai trait
    }
}