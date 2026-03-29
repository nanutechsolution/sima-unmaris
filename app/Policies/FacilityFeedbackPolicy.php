<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\FacilityFeedback;
use Illuminate\Auth\Access\HandlesAuthorization;

class FacilityFeedbackPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:FacilityFeedback');
    }

    public function view(AuthUser $authUser, FacilityFeedback $facilityFeedback): bool
    {
        return $authUser->can('View:FacilityFeedback');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:FacilityFeedback');
    }

    public function update(AuthUser $authUser, FacilityFeedback $facilityFeedback): bool
    {
        return $authUser->can('Update:FacilityFeedback');
    }

    public function delete(AuthUser $authUser, FacilityFeedback $facilityFeedback): bool
    {
        return $authUser->can('Delete:FacilityFeedback');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:FacilityFeedback');
    }

    public function restore(AuthUser $authUser, FacilityFeedback $facilityFeedback): bool
    {
        return $authUser->can('Restore:FacilityFeedback');
    }

    public function forceDelete(AuthUser $authUser, FacilityFeedback $facilityFeedback): bool
    {
        return $authUser->can('ForceDelete:FacilityFeedback');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:FacilityFeedback');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:FacilityFeedback');
    }

    public function replicate(AuthUser $authUser, FacilityFeedback $facilityFeedback): bool
    {
        return $authUser->can('Replicate:FacilityFeedback');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:FacilityFeedback');
    }

}