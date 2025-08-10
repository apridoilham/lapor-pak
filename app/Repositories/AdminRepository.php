<?php

namespace App\Repositories;

use App\Interfaces\AdminRepositoryInterface;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AdminRepository implements AdminRepositoryInterface
{
    public function getAllAdmins()
    {
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
                'password' => $data['password'],
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
        ];

        if (isset($data['email'])) {
            $userData['email'] = $data['email'];
        }
        
        if (isset($data['rw_id'])) {
            $userData['rw_id'] = $data['rw_id'];
        }

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