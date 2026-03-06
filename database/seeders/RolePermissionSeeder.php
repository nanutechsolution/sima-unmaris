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
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cache roles dan permissions dari Spatie
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // 1. Daftar Hak Akses (Permissions)
        $permissions = [
            'view_asset',
            'create_asset',
            'edit_asset',
            'delete_asset',
            'process_handover',
            'view_master_data',
            'manage_master_data',
            'manage_users',
            'manage_roles',
            'view_any_asset',
            'update_asset'
        ];

        // Buat permissions ke database
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // 2. Buat Role dan berikan Hak Akses

        // Role: Super Admin (Bisa melakukan segalanya)
        $superAdminRole = Role::firstOrCreate(['name' => 'Super Admin']);
        $superAdminRole->givePermissionTo(Permission::all());

        // Role: Staf Operasional / Gudang (Akses terbatas)
        $staffRole = Role::firstOrCreate(['name' => 'Staf Operasional']);
        $staffRole->givePermissionTo([
            'view_asset',
            'create_asset',
            'edit_asset',
            'process_handover',
            'view_master_data',
            'manage_master_data',
            'view_any_asset',
        ]);

        // 3. Buat Akun User Default untuk Super Admin
        $adminUser = User::firstOrCreate(
            ['email' => 'admin@unmaris.ac.id'], // Ganti dengan email kampus yang sesuai
            [
                'name' => 'Super Administrator',
                'password' => Hash::make('password123'), // Password default
                'email_verified_at' => now(),
            ]
        );

        // Tempelkan role Super Admin ke user tersebut
        if (!$adminUser->hasRole('Super Admin')) {
            $adminUser->assignRole('Super Admin');
        }

        $this->command->info('✅ Seeder Role, Permission, dan User Admin berhasil dijalankan!');
    }
}
