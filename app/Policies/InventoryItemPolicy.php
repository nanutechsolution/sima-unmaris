<?php

namespace App\Policies;

use App\Models\InventoryItem;
use App\Models\User;

class InventoryItemPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('view_any_inventory');
    }

    public function view(User $user, InventoryItem $inventoryItem): bool
    {
        return $user->hasPermissionTo('view_any_inventory');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('manage_inventory');
    }

    public function update(User $user, InventoryItem $inventoryItem): bool
    {
        return $user->hasPermissionTo('manage_inventory');
    }

    public function delete(User $user, InventoryItem $inventoryItem): bool
    {
        // Hanya Super Admin yang boleh hapus ATK dari database, staf hanya boleh ubah stok
        return $user->hasRole('Super Admin'); 
    }
}