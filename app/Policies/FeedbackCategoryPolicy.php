<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\FeedbackCategory;
use Illuminate\Auth\Access\HandlesAuthorization;

class FeedbackCategoryPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:FeedbackCategory');
    }

    public function view(AuthUser $authUser, FeedbackCategory $feedbackCategory): bool
    {
        return $authUser->can('View:FeedbackCategory');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:FeedbackCategory');
    }

    public function update(AuthUser $authUser, FeedbackCategory $feedbackCategory): bool
    {
        return $authUser->can('Update:FeedbackCategory');
    }

    public function delete(AuthUser $authUser, FeedbackCategory $feedbackCategory): bool
    {
        return $authUser->can('Delete:FeedbackCategory');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:FeedbackCategory');
    }

    public function restore(AuthUser $authUser, FeedbackCategory $feedbackCategory): bool
    {
        return $authUser->can('Restore:FeedbackCategory');
    }

    public function forceDelete(AuthUser $authUser, FeedbackCategory $feedbackCategory): bool
    {
        return $authUser->can('ForceDelete:FeedbackCategory');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:FeedbackCategory');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:FeedbackCategory');
    }

    public function replicate(AuthUser $authUser, FeedbackCategory $feedbackCategory): bool
    {
        return $authUser->can('Replicate:FeedbackCategory');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:FeedbackCategory');
    }

}