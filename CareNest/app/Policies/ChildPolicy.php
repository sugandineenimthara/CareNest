<?php

namespace App\Policies;

use App\Models\Child;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ChildPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['provider', 'admin', 'midwife']);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Child $child): bool
    {
        if (in_array($user->role, ['provider', 'admin'])) return true;

        if ($user->role === 'midwife') {
            // Midwife can see if they are assigned to the child's mother OR the child directly (assuming midwife_id on child)
            if ($user->midwife && $child->midwife_id === $user->midwife->midwife_id) {
                return true;
            }
            if ($user->midwife && $child->mother && $child->mother->midwife_id === $user->midwife->midwife_id) {
                return true;
            }
            return false;
        }

        if ($user->role === 'mother') {
            return $user->mother && $child->mother_id === $user->mother->mother_id;
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return in_array($user->role, ['provider', 'admin', 'midwife']);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Child $child): bool
    {
        if (in_array($user->role, ['provider', 'admin'])) return true;

        if ($user->role === 'midwife') {
            if ($user->midwife && $child->midwife_id === $user->midwife->midwife_id) return true;
            if ($user->midwife && $child->mother && $child->mother->midwife_id === $user->midwife->midwife_id) return true;
        }

        return false; // Mother cannot update child clinical records directly.
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Child $child): bool
    {
        return in_array($user->role, ['provider', 'admin']);
    }
}
