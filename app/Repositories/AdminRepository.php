<?php

namespace App\Repositories;

use App\Interfaces\AdminRepositoryInterface;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AdminRepository implements AdminRepositoryInterface
{
    public function getAllAdmins()
    {
        return User::role('admin')
            ->with('rw')
            ->where('id', '!=', Auth::id())
            ->get();
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
                'google_id' => 'admin-' . Str::uuid(),
                'rw_id' => $data['rw_id'],
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
            'rw_id' => $data['rw_id'],
        ];

        return $user->update($userData);
    }

    public function deleteAdmin(int $id): bool
    {
        $user = $this->getAdminById($id);
        return $user->delete();
    }
}