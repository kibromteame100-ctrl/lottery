<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['super-admin', 'payment-reviewer', 'report-viewer']);
    }

    public function view(User $user, User $target): bool
    {
        return $user->hasAnyRole(['super-admin', 'payment-reviewer', 'report-viewer']);
    }

    public function update(User $user, User $target): bool
    {
        return $user->hasRole('super-admin');
    }

    public function delete(User $user, User $target): bool
    {
        return $user->hasRole('super-admin') && $user->id !== $target->id;
    }

    /** Super-admin-only: manage admin accounts */
    public function manageAdmins(User $user): bool
    {
        return $user->hasRole('super-admin');
    }
}
