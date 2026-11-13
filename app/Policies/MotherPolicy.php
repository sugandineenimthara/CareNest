<?php

namespace App\Policies;

use App\Models\Mother;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class MotherPolicy
{
    

    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['provider', 'admin', 'midwife']);
    }

    

    public function view(User $user, Mother $mother): bool
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

    

    public function create(User $user): bool
    {
        return in_array($user->role, ['provider', 'admin', 'midwife']);
    }

    

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

    

    public function delete(User $user, Mother $mother): bool
    {
        if (in_array($user->role, ['provider', 'admin'])) {
            return true;
        }

        if ($user->role === 'midwife') {
            return $user->midwife && (int)$user->midwife->midwife_id === (int)$mother->midwife_id;
        }

        return false;
    }
}
