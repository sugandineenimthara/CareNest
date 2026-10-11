<?php

namespace App\Policies;

use App\Models\Mother;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class MotherPolicy
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
    public function view(User $user, Mother $mother): bool
    {
        if (in_array($user->role, ['provider', 'admin'])) return true;
        
        if ($user->role === 'midwife') {
            // Check if midwife is assigned to this mother
            return $user->midwife && $user->midwife->midwife_id === $mother->midwife_id;
        }

        if ($user->role === 'mother') {
            return $user->mother && $user->mother->mother_id === $mother->mother_id;
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
    public function update(User $user, Mother $mother): bool
    {
        if (in_array($user->role, ['provider', 'admin'])) return true;
        
        if ($user->role === 'midwife') {
            return $user->midwife && $user->midwife->midwife_id === $mother->midwife_id;
        }

        if ($user->role === 'mother') {
            return $user->mother && $user->mother->mother_id === $mother->mother_id;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Mother $mother): bool
    {
        return in_array($user->role, ['provider', 'admin']);
    }
}
