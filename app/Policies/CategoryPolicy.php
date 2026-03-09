<?php

namespace App\Policies;

use App\Models\Category;
use App\Models\User;

class CategoryPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('view_any_master');
    }

    public function view(User $user, Category $category): bool
    {
        return $user->hasPermissionTo('view_any_master');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('manage_master');
    }

    public function update(User $user, Category $category): bool
    {
        return $user->hasPermissionTo('manage_master');
    }

    public function delete(User $user, Category $category): bool
    {
        return $user->hasPermissionTo('manage_master');
    }

    public function forceDelete(User $user, Category $category): bool
    {
        return $user->hasRole('Super Admin');
    }
}