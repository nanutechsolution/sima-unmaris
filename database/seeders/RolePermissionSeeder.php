<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cache permission
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // ================================
        // 1. CREATE ROLES ONLY
        // ================================
        $superAdminRole = Role::firstOrCreate(['name' => 'Super Admin']);
        $stafRole = Role::firstOrCreate(['name' => 'Staf Inventaris']);
        $auditorRole = Role::firstOrCreate(['name' => 'Auditor']);

        // ================================
        // 2. CREATE DEFAULT USERS
        // ================================

        // --- SUPER ADMIN ---
        $admin = User::firstOrCreate(
            ['email' => 'admin@unmaris.ac.id'],
            [
                'name' => 'Administrator Utama',
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
            ]
        );

        if (!$admin->hasRole('Super Admin')) {
            $admin->assignRole($superAdminRole);
        }

        // --- STAF ---
        $staf = User::firstOrCreate(
            ['email' => 'staf@unmaris.ac.id'],
            [
                'name' => 'Staf Gudang Pusat',
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
            ]
        );

        if (!$staf->hasRole('Staf Inventaris')) {
            $staf->assignRole($stafRole);
        }

        // ================================
        // 3. OPTIONAL: AUTO FULL ACCESS SUPER ADMIN
        // ================================
        // NOTE:
        // Setelah jalankan: php artisan shield:generate
        // Uncomment ini kalau mau Super Admin langsung punya semua akses

        /*
        $superAdminRole->syncPermissions(
            \Spatie\Permission\Models\Permission::all()
        );
        */

        // ================================
        // DONE
        // ================================
        $this->command->info('✅ Role & User default berhasil dibuat (Shield Ready)');
    }
}