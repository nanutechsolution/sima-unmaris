<?php

namespace App\Policies;

use App\Models\Asset;
use App\Models\User;

class AssetPolicy
{
    /**
     * Menentukan apakah user bisa melihat daftar aset.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('view_any_asset');
    }

    /**
     * Menentukan apakah user bisa melihat detail satu aset.
     */
    public function view(User $user, Asset $asset): bool
    {
        return $user->hasPermissionTo('view_asset');
    }

    /**
     * Menentukan apakah user bisa menambah aset baru.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('create_asset');
    }

    /**
     * Menentukan apakah user bisa mengubah data aset.
     */
    public function update(User $user, Asset $asset): bool
    {
        return $user->hasPermissionTo('update_asset');
    }

    /**
     * Menentukan apakah user bisa menghapus aset (Soft Delete).
     */
    public function delete(User $user, Asset $asset): bool
    {
        return $user->hasPermissionTo('delete_asset');
    }

    /**
     * Menentukan apakah user bisa menghapus aset secara permanen.
     */
    public function forceDelete(User $user, Asset $asset): bool
    {
        // Biasanya hanya Super Admin yang boleh Force Delete
        return $user->hasRole('Super Admin') && $user->hasPermissionTo('force_delete_asset');
    }

    /**
     * Menentukan apakah user bisa memulihkan aset yang dihapus.
     */
    public function restore(User $user, Asset $asset): bool
    {
        return $user->hasPermissionTo('restore_asset');
    }
}
