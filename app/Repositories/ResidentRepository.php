<?php

namespace App\Repositories;

use App\Interfaces\ResidentRepositoryInterface;
use App\Models\Resident;
use App\Models\User;
use Illuminate\Support\Facades\DB;
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

    public function updateResident(array $data, int $id)
    {
        return DB::transaction(function () use ($data, $id) {
            $resident = $this->getResidentById($id);

            $userData = [
                'name' => $data['name'],
                'email' => $data['email'],
            ];

            if (!empty($data['password'])) {
                $userData['password'] = $data['password'];
            }
            $resident->user->update($userData);

            $residentData = [
                'rt_id' => $data['rt_id'],
                'rw_id' => $data['rw_id'],
                'address' => $data['address'],
            ];
            if (isset($data['avatar'])) {
                if ($resident->avatar && Storage::disk('public')->exists($resident->avatar)) {
                    Storage::disk('public')->delete($resident->avatar);
                }
                $residentData['avatar'] = $data['avatar'];
            }
            $resident->update($residentData);

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