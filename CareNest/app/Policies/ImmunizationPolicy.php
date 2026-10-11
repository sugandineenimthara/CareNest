<?php

namespace App\Policies;

use App\Models\Immunization;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ImmunizationPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['provider', 'admin', 'midwife']);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return in_array($user->role, ['provider', 'admin', 'midwife']);
    }
}
