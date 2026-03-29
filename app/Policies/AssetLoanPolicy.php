<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\AssetLoan;
use Illuminate\Auth\Access\HandlesAuthorization;

class AssetLoanPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:AssetLoan');
    }

    public function view(AuthUser $authUser, AssetLoan $assetLoan): bool
    {
        return $authUser->can('View:AssetLoan');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:AssetLoan');
    }

    public function update(AuthUser $authUser, AssetLoan $assetLoan): bool
    {
        return $authUser->can('Update:AssetLoan');
    }

    public function delete(AuthUser $authUser, AssetLoan $assetLoan): bool
    {
        return $authUser->can('Delete:AssetLoan');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:AssetLoan');
    }

    public function restore(AuthUser $authUser, AssetLoan $assetLoan): bool
    {
        return $authUser->can('Restore:AssetLoan');
    }

    public function forceDelete(AuthUser $authUser, AssetLoan $assetLoan): bool
    {
        return $authUser->can('ForceDelete:AssetLoan');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:AssetLoan');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:AssetLoan');
    }

    public function replicate(AuthUser $authUser, AssetLoan $assetLoan): bool
    {
        return $authUser->can('Replicate:AssetLoan');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:AssetLoan');
    }

}