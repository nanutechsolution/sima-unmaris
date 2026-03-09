<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    /**
     * Hanya yang punya izin manage_users yang bisa melihat menu Pengguna.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('manage_users');
    }

    public function view(User $user, User $model): bool
    {
        return $user->hasPermissionTo('manage_users');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('manage_users');
    }

    public function update(User $user, User $model): bool
    {
        // Cegah Staf biasa mengedit akun Super Admin!
        if ($model->hasRole('Super Admin') && !$user->hasRole('Super Admin')) {
            return false;
        }
        return $user->hasPermissionTo('manage_users');
    }

    public function delete(User $user, User $model): bool
    {
        // Tidak boleh menghapus diri sendiri atau Super Admin
        if ($user->id === $model->id || $model->hasRole('Super Admin')) {
            return false;
        }
        return $user->hasPermissionTo('manage_users');
    }
}