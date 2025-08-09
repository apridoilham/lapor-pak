<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Mendefinisikan semua permission
        $permissions = [
            'dashboard' => ['view'],
            'resident' => ['view', 'create', 'edit', 'delete'],
            'report-category' => ['view', 'create', 'edit', 'delete'],
            'report' => ['view', 'create', 'edit', 'delete'],
            'report-status' => ['view', 'create', 'edit', 'delete'],
            // Permission baru untuk manajemen admin
            'admin-user' => ['view', 'create', 'edit', 'delete'],
        ];

        // Membuat semua permission
        foreach ($permissions as $key => $value) {
            foreach ($value as $permission) {
                Permission::firstOrCreate(['name' => $key . '-' . $permission]);
            }
        }

        // --- Membuat Roles dan Memberikan Permissions ---

        // 1. Role "super-admin" mendapatkan SEMUA hak akses
        Role::firstOrCreate(['name' => 'super-admin'])->givePermissionTo(Permission::all());

        // 2. Role "admin" mendapatkan semua hak akses KECUALI manajemen admin
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $adminPermissions = Permission::where('name', 'NOT LIKE', 'admin-user-%')->get();
        $adminRole->syncPermissions($adminPermissions);

        // 3. Role "resident"
        Role::firstOrCreate(['name' => 'resident'])->givePermissionTo([
            'report-category-view',
            'report-view',
            'report-create',
            'report-edit',
            'report-delete',
            'report-status-view',
        ]);
    }
}