<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cache permission Spatie
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // 1. DAFTAR HAK AKSES FULL (Semua Modul)
        $permissionsByModule = [
            'Aset' => [
                'view_any_asset', 'view_asset', 'create_asset', 'update_asset', 'delete_asset',
                'restore_asset', 'force_delete_asset', 'handover_asset', 'generate_qr_asset',
            ],
            'Inventori & ATK' => [
                'view_any_inventory', 'manage_inventory',
            ],
            'Operasional' => [
                'view_any_loan', 'manage_loan',
                'view_any_maintenance', 'manage_maintenance',
            ],
            'Master Data' => [
                'view_any_master', 'manage_master', // Untuk Kategori, Lokasi, Ruangan, Supplier
            ],
            'Sistem & Keamanan' => [
                'manage_users', 'manage_roles', 'view_audit_trail',
            ],
            'Survei & Layanan' => [
                'view_any_survey', 'manage_survey',
            ],
        ];

        $allPermissions = [];
        foreach ($permissionsByModule as $module => $perms) {
            foreach ($perms as $perm) {
                Permission::firstOrCreate(['name' => $perm]);
                $allPermissions[] = $perm;
            }
        }

        // 2. SETUP ROLE & PENUGASAN
        
        // --- ROLE: Super Admin (Akses Tanpa Batas) ---
        $superAdminRole = Role::firstOrCreate(['name' => 'Super Admin']);
        $superAdminRole->syncPermissions(Permission::all());

        // --- ROLE: Staf Inventaris (Bisa operasional, dilarang hapus permanen & atur user) ---
        $stafRole = Role::firstOrCreate(['name' => 'Staf Inventaris']);
        $stafRole->syncPermissions([
            'view_any_asset', 'view_asset', 'create_asset', 'update_asset', 'handover_asset', 'generate_qr_asset',
            'view_any_inventory', 'manage_inventory',
            'view_any_loan', 'manage_loan',
            'view_any_maintenance', 'manage_maintenance',
            'view_any_master', 'manage_master',
        ]);

        // --- ROLE: Auditor / Pimpinan (Hanya bisa melihat, dilarang edit/tambah) ---
        $auditorRole = Role::firstOrCreate(['name' => 'Auditor']);
        $auditorRole->syncPermissions([
            'view_any_asset', 'view_asset', 'view_any_inventory', 'view_any_loan', 
            'view_any_maintenance', 'view_any_master', 'view_audit_trail', 'view_any_survey'
        ]);

        // 3. BUAT/UPDATE USER DEFAULT
        $admin = User::firstOrCreate(
            ['email' => 'admin@unmaris.ac.id'],
            ['name' => 'Administrator Utama', 'password' => Hash::make('password123'), 'email_verified_at' => now()]
        );
        if (!$admin->hasRole('Super Admin')) $admin->assignRole($superAdminRole);

        $staf = User::firstOrCreate(
            ['email' => 'staf@unmaris.ac.id'],
            ['name' => 'Staf Gudang Pusat', 'password' => Hash::make('password123'), 'email_verified_at' => now()]
        );
        if (!$staf->hasRole('Staf Inventaris')) $staf->assignRole($stafRole);

        $this->command->info('✅ Hak Akses Full berhasil di-seeding!');
    }
}