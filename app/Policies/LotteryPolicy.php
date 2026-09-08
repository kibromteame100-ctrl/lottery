<?php

namespace App\Policies;

use App\Models\Lottery;
use App\Models\User;

class LotteryPolicy
{
    public function viewAny(User $user): bool  { return true; }
    public function view(User $user, Lottery $lottery): bool { return true; }

    public function create(User $user): bool
    {
        return $user->hasRole('super-admin');
    }

    public function update(User $user, Lottery $lottery): bool
    {
        return $user->hasRole('super-admin');
    }

    public function delete(User $user, Lottery $lottery): bool
    {
        return $user->hasRole('super-admin');
    }
}
