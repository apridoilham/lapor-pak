<?php

namespace App\Repositories;

use App\Interfaces\ResidentRepositoryInterface;
use App\Models\Resident;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Builder;

class ResidentRepository implements ResidentRepositoryInterface
{
    public function getAllResidents(int $rwId = null, int $rtId = null)
    {
        return Resident::with(['user', 'rt', 'rw'])
            ->when($rtId, function (Builder $query) use ($rtId) {
                return $query->where('rt_id', $rtId);
            })
            ->when($rwId && !$rtId, function (Builder $query) use ($rwId) {
                return $query->where('rw_id', $rwId);
            })
            ->get();
    }

    public function getResidentById(int $id)
    {
        return Resident::findOrFail($id);
    }

    public function createResident(array $data)
    {
        return DB::transaction(function () use ($data) {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => isset($data['password']) ? bcrypt($data['password']) : null,
                'google_id' => null,
            ]);

            $user->assignRole('resident');

            $residentData = [
                'avatar' => $data['avatar'] ?? null,
                'phone' => $data['phone'] ?? null,
                'address' => $data['address'],
                'rt_id' => $data['rt_id'],
                'rw_id' => $data['rw_id'],
            ];

            return $user->resident()->create($residentData);
        });
    }

    public function updateResident(array $data, int $id)
    {
        return DB::transaction(function () use ($data, $id) {
            $resident = $this->getResidentById($id);

            if (isset($data['password']) && !empty($data['password'])) {
                $resident->user()->update([
                    'password' => bcrypt($data['password'])
                ]);
            }
            
            if (isset($data['name'])) {
                $resident->user()->update([
                    'name' => $data['name']
                ]);
            }

            // ▼▼▼ PERBAIKAN UTAMA DI SINI ▼▼▼
            $residentData = [
                'rt_id' => $data['rt_id'],
                'rw_id' => $data['rw_id'],
                'address' => $data['address'],
                'phone' => $data['phone'] ?? null, // Tambahkan baris ini untuk menyimpan no. telepon
            ];

            if (isset($data['avatar'])) {
                if ($resident->avatar && !Str::startsWith($resident->avatar, 'http') && Storage::disk('public')->exists($resident->avatar)) {
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
        $resident = $this->getResidentById($id);

        return DB::transaction(function () use ($resident) {
            return $resident->user()->delete();
        });
    }

    public function countResidents(int $rwId = null): int
    {
        return Resident::when($rwId, function ($query) use ($rwId) {
            $query->where('rw_id', $rwId);
        })->count();
    }
    
    public function getTopReporters(int $rwId = null)
    {
        return Resident::with('user', 'rt', 'rw')
            ->withCount('reports')
            ->when($rwId, fn (Builder $q) => $q->where('rw_id', $rwId))
            ->orderByDesc('reports_count')
            ->having('reports_count', '>', 0)
            ->take(5)
            ->get();
    }
}