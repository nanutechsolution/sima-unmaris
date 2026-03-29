<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\MaintenanceLog;
use Illuminate\Auth\Access\HandlesAuthorization;

class MaintenanceLogPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:MaintenanceLog');
    }

    public function view(AuthUser $authUser, MaintenanceLog $maintenanceLog): bool
    {
        return $authUser->can('View:MaintenanceLog');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:MaintenanceLog');
    }

    public function update(AuthUser $authUser, MaintenanceLog $maintenanceLog): bool
    {
        return $authUser->can('Update:MaintenanceLog');
    }

    public function delete(AuthUser $authUser, MaintenanceLog $maintenanceLog): bool
    {
        return $authUser->can('Delete:MaintenanceLog');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:MaintenanceLog');
    }

    public function restore(AuthUser $authUser, MaintenanceLog $maintenanceLog): bool
    {
        return $authUser->can('Restore:MaintenanceLog');
    }

    public function forceDelete(AuthUser $authUser, MaintenanceLog $maintenanceLog): bool
    {
        return $authUser->can('ForceDelete:MaintenanceLog');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:MaintenanceLog');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:MaintenanceLog');
    }

    public function replicate(AuthUser $authUser, MaintenanceLog $maintenanceLog): bool
    {
        return $authUser->can('Replicate:MaintenanceLog');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:MaintenanceLog');
    }

}