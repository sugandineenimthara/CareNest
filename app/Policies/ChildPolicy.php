<?php

namespace App\Policies;

use App\Models\Child;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ChildPolicy
{
    

    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['provider', 'admin', 'midwife', 'mother']);
    }

    

    public function view(User $user, Child $child): bool
    {
        if (in_array($user->role, ['provider', 'admin'])) return true;

        if ($user->role === 'midwife') {
            
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

    

    public function create(User $user): bool
    {
        return in_array($user->role, ['provider', 'admin', 'midwife']);
    }

    

    public function update(User $user, Child $child): bool
    {
        if (in_array($user->role, ['provider', 'admin'])) return true;

        if ($user->role === 'midwife') {
            if ($user->midwife && $child->midwife_id === $user->midwife->midwife_id) return true;
            if ($user->midwife && $child->mother && $child->mother->midwife_id === $user->midwife->midwife_id) return true;
        }

        return false; 
    }

    

    public function delete(User $user, Child $child): bool
    {
        return in_array($user->role, ['provider', 'admin']);
    }
}
