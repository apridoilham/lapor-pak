<?php

namespace App\Interfaces;

use App\Models\User;

interface AdminRepositoryInterface
{
    public function getAllAdmins();
    public function getAdminById(int $id);
    public function createAdmin(array $data): User;
    public function updateAdmin(array $data, int $id): bool;
    public function deleteAdmin(int $id): bool;
}