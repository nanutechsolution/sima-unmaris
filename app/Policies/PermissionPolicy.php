<?php

namespace App\Policies;

use App\Models\User;
use Spatie\Permission\Models\Permission;

class PermissionPolicy
{
    public function viewAny(User $user): bool
    {
        // Menu hak akses bisa dilihat oleh yang punya izin mengatur role
        return $user->hasPermissionTo('manage_roles');
    }

    public function view(User $user, Permission $permission): bool
    {
        return $user->hasPermissionTo('manage_roles');
    }

    public function create(User $user): bool
    {
        // KETAT: Membuat hak akses baru HANYA boleh dilakukan oleh Super Admin
        // Karena hak akses sistem idealnya hanya ditambah melalui koding/seeder
        return $user->hasRole('Super Admin');
    }

    public function update(User $user, Permission $permission): bool
    {
        return $user->hasRole('Super Admin');
    }

    public function delete(User $user, Permission $permission): bool
    {
        return $user->hasRole('Super Admin');
    }
}
