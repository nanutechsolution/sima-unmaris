<?php

namespace App\Policies;

use App\Models\User;
use Spatie\Permission\Models\Role;

class RolePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('manage_roles');
    }

    public function view(User $user, Role $role): bool
    {
        return $user->hasPermissionTo('manage_roles');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('manage_roles');
    }

    public function update(User $user, Role $role): bool
    {
        // Proteksi Ekstra: Role 'Super Admin' tidak boleh diubah oleh siapapun 
        // kecuali oleh user yang memang merupakan Super Admin itu sendiri.
        if ($role->name === 'Super Admin' && !$user->hasRole('Super Admin')) {
            return false;
        }
        return $user->hasPermissionTo('manage_roles');
    }

    public function delete(User $user, Role $role): bool
    {
        // Proteksi Ekstra: Role 'Super Admin' MUTLAK tidak boleh dihapus.
        if ($role->name === 'Super Admin') {
            return false;
        }
        return $user->hasPermissionTo('manage_roles');
    }
}