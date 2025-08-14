<?php

namespace App\Repositories;

use App\Interfaces\AdminRepositoryInterface;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
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
        return User::findOrFail($id);
    }

    public function createAdmin(array $data): User
    {
        return DB::transaction(function () use ($data) {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'google_id' => 'admin-' . Str::uuid(),
                'password' => Hash::make(Str::random(16)), // Generate random password
                'rw_id' => $data['rw_id'],
            ]);

            $user->assignRole('admin');

            return $user;
        });
    }

    public function updateAdmin(array $data, int $id): bool
    {
        $user = User::findOrFail($id);

        $userData = [
            'name' => $data['name'],
        ];

        // Only update email if provided
        if (isset($data['email'])) {
            $userData['email'] = $data['email'];
        }

        // Only update rw_id if provided
        if (isset($data['rw_id'])) {
            $userData['rw_id'] = $data['rw_id'];
        }

        // Only update password if provided
        if (isset($data['password']) && !empty($data['password'])) {
            $userData['password'] = Hash::make($data['password']);
        }

        return $user->update($userData);
    }

    public function deleteAdmin(int $id): bool
    {
        $user = User::findOrFail($id);
        return $user->delete();
    }
}