<?php

namespace App\Policies;

use App\Models\Immunization;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ImmunizationPolicy
{
    

    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['provider', 'admin', 'midwife', 'mother']);
    }

    

    public function create(User $user): bool
    {
        return in_array($user->role, ['provider', 'admin', 'midwife']);
    }
}
